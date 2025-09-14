<?php
/**
 * DTDC API Service Class - Official Implementation
 * Based on official DTDC API documentation v2.2
 */

require_once __DIR__ . '/../config/dtdc_config.php';

class DTDCAPINew {
    private $config;
    private $baseUrl;
    private $username;
    private $password;
    private $token;
    private $tokenCache;
    
    public function __construct() {
        $this->config = include __DIR__ . '/../config/dtdc_config.php';
        $this->baseUrl = $this->config['api']['base_url'];
        $this->username = $this->config['api']['username'];
        $this->password = $this->config['api']['password'];
        $this->token = null;
        $this->tokenCache = null;
    }
    
    /**
     * Get authentication token
     * 
     * @return string|false Token or false on failure
     */
    private function getAuthToken() {
        // Check if we have a cached token
        if ($this->tokenCache && $this->tokenCache['expires'] > time()) {
            return $this->tokenCache['token'];
        }
        
        $endpoint = $this->baseUrl . $this->config['api']['endpoints']['authenticate'];
        $url = $endpoint . '?username=' . urlencode($this->username) . '&password=' . urlencode($this->password);
        
        error_log("DTDC Auth Request: $url");
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/plain',
            'User-Agent: EverythingB2C/1.0'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        error_log("DTDC Auth Response Code: $httpCode");
        error_log("DTDC Auth Response: $response");
        
        if ($error) {
            error_log("DTDC Auth cURL Error: $error");
            return false;
        }
        
        if ($httpCode === 200) {
            // Token is returned as plain text in response body
            $token = trim($response);
            if (!empty($token)) {
                // Cache token for 50 minutes (tokens typically expire in 1 hour)
                $this->tokenCache = [
                    'token' => $token,
                    'expires' => time() + (50 * 60)
                ];
                error_log("DTDC Auth Token obtained: " . substr($token, 0, 10) . "...");
                return $token;
            }
        }
        
        error_log("DTDC Auth Failed - HTTP: $httpCode, Response: $response");
        return false;
    }
    
    /**
     * Track a shipment using DTDC tracking ID
     * 
     * @param string $trackingId DTDC tracking ID
     * @return array|false Tracking data or false on failure
     */
    public function trackShipment($trackingId) {
        if (!$this->config['service']['enabled']) {
            return false;
        }
        
        // Get authentication token
        $token = $this->getAuthToken();
        if (!$token) {
            error_log("DTDC Tracking failed: Could not get authentication token");
            return false;
        }
        
        $endpoint = $this->baseUrl . $this->config['api']['endpoints']['tracking'];
        
        // Prepare request data according to official documentation
        $data = [
            'trkType' => 'cnno',
            'strcnno' => $trackingId,
            'addtnlDtl' => 'Y'
        ];
        
        error_log("DTDC Tracking Request URL: $endpoint");
        error_log("DTDC Tracking Request Data: " . json_encode($data));
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: EverythingB2C/1.0',
            'X-Access-Token: ' . $token
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        error_log("DTDC Tracking Response Code: $httpCode");
        error_log("DTDC Tracking Response: $response");
        
        if ($error) {
            error_log("DTDC Tracking cURL Error: $error");
            return false;
        }
        
        if ($httpCode === 200) {
            $decodedResponse = json_decode($response, true);
            if ($decodedResponse && isset($decodedResponse['statusCode']) && $decodedResponse['statusCode'] === 200) {
                return $this->parseTrackingResponse($decodedResponse);
            } elseif ($decodedResponse && isset($decodedResponse['statusCode']) && $decodedResponse['statusCode'] === 206) {
                // Handle partial content (tracking ID not found or not authorized)
                $errorMessage = 'Tracking ID not found or not authorized for your account';
                if (isset($decodedResponse['errorDetails']) && is_array($decodedResponse['errorDetails'])) {
                    foreach ($decodedResponse['errorDetails'] as $error) {
                        if (isset($error['value'])) {
                            $errorMessage = $error['value'];
                            break;
                        }
                    }
                }
                error_log("DTDC Tracking Error: $errorMessage");
                throw new Exception($errorMessage);
            }
        }
        
        error_log("DTDC Tracking failed - HTTP: $httpCode, Response: $response");
        return false;
    }
    
    /**
     * Parse tracking response from DTDC API
     * 
     * @param array $response API response
     * @return array Parsed tracking data
     */
    private function parseTrackingResponse($response) {
        $trackingData = [
            'tracking_id' => '',
            'status' => '',
            'status_description' => '',
            'current_location' => '',
            'delivery_date' => '',
            'delivered_to' => '',
            'mapped_status' => '',
            'events' => []
        ];
        
        if (isset($response['trackHeader'])) {
            $header = $response['trackHeader'];
            
            $trackingData['tracking_id'] = $header['strShipmentNo'] ?? '';
            $trackingData['status'] = $header['strStatus'] ?? '';
            $trackingData['status_description'] = $header['strStatus'] ?? '';
            $trackingData['current_location'] = $header['strDestination'] ?? '';
            $trackingData['delivered_to'] = $header['strStatusRelName'] ?? '';
            
            // Map status to user-friendly format
            $trackingData['mapped_status'] = $this->mapStatus($header['strStatus'] ?? '');
            
            // Parse delivery date
            if (isset($header['strStatusTransOn']) && isset($header['strStatusTransTime'])) {
                $date = $header['strStatusTransOn'];
                $time = $header['strStatusTransTime'];
                if (strlen($date) === 8 && strlen($time) === 4) {
                    $formattedDate = substr($date, 4, 4) . '-' . substr($date, 2, 2) . '-' . substr($date, 0, 2);
                    $formattedTime = substr($time, 0, 2) . ':' . substr($time, 2, 2);
                    $trackingData['delivery_date'] = $formattedDate . ' ' . $formattedTime;
                }
            }
        }
        
        // Parse tracking events
        if (isset($response['trackDetails']) && is_array($response['trackDetails'])) {
            foreach ($response['trackDetails'] as $event) {
                $eventData = [
                    'date' => '',
                    'time' => '',
                    'location' => $event['strOrigin'] ?? '',
                    'status' => $event['strAction'] ?? '',
                    'description' => $event['strAction'] ?? ''
                ];
                
                // Parse event date and time
                if (isset($event['strActionDate']) && isset($event['strActionTime'])) {
                    $date = $event['strActionDate'];
                    $time = $event['strActionTime'];
                    if (strlen($date) === 8 && strlen($time) === 4) {
                        $eventData['date'] = substr($date, 4, 4) . '-' . substr($date, 2, 2) . '-' . substr($date, 0, 2);
                        $eventData['time'] = substr($time, 0, 2) . ':' . substr($time, 2, 2);
                    }
                }
                
                $trackingData['events'][] = $eventData;
            }
        }
        
        return $trackingData;
    }
    
    /**
     * Map DTDC status to user-friendly status
     * 
     * @param string $status DTDC status
     * @return string Mapped status
     */
    private function mapStatus($status) {
        $statusMap = [
            'DELIVERED' => 'Delivered',
            'DELIVERY PROCESS IN PROGRESS' => 'In Transit',
            'ATTEMPTED' => 'Delivery Attempted',
            'HELDUP' => 'On Hold',
            'RTO' => 'Returned',
            'BOOKED' => 'Booked',
            'IN TRANSIT' => 'In Transit',
            'OUT FOR DELIVERY' => 'Out for Delivery',
            'NOT DELIVERED' => 'Not Delivered',
            'CONSIGNMENT RELEASED' => 'Released',
            'CONSIGNMENT HAS RETURNED' => 'Returned'
        ];
        
        return $statusMap[$status] ?? $status;
    }
    
    /**
     * Get configuration value
     * 
     * @param string $key Configuration key (supports dot notation)
     * @return mixed
     */
    public function getConfig($key) {
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return null;
            }
        }
        
        return $value;
    }
    
    /**
     * Check if DTDC service is enabled
     * 
     * @return bool
     */
    public function isEnabled() {
        return $this->config['service']['enabled'];
    }
}
