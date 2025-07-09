<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Calculate GST amount based on product details and delivery location
 * Note: GST calculation happens at checkout time based on delivery location vs billing location
 * - Same state: SGST + CGST (split 50-50)
 * - Different state: IGST (full rate)
 */
function calculateGST($product_price, $gst_rate, $gst_type, $delivery_state, $billing_state = null) {
    $gst_amount = ($product_price * $gst_rate) / 100;
    
    // If billing state is not provided, use delivery state
    $billing_state = $billing_state ?: $delivery_state;
    
    // Determine GST type based on delivery location
    if ($delivery_state === $billing_state) {
        // Same state - SGST + CGST
        $sgst_amount = $gst_amount / 2;
        $cgst_amount = $gst_amount / 2;
        return [
            'total_gst' => $gst_amount,
            'sgst' => $sgst_amount,
            'cgst' => $cgst_amount,
            'igst' => 0,
            'gst_type' => 'sgst_cgst'
        ];
    } else {
        // Different state - IGST
        return [
            'total_gst' => $gst_amount,
            'sgst' => 0,
            'cgst' => 0,
            'igst' => $gst_amount,
            'gst_type' => 'igst'
        ];
    }
}

/**
 * Get shipping zone for a given location
 */
function getShippingZone($state, $city = null, $pincode = null, $country = null) {
    global $pdo;
    // Standardize input: trim and lowercase for city, state, country; pincode as string
    $pincode = $pincode ? trim((string)$pincode) : null;
    $city = $city ? strtolower(trim($city)) : null;
    $state = $state ? strtolower(trim($state)) : null;
    $country = $country ? strtolower(trim($country)) : null;
    // Priority: pincode > city > state > country
    // 1. Pincode (exact match, but trim)
    if ($pincode) {
        $stmt = $pdo->prepare("SELECT sz.* FROM shipping_zones sz 
                              JOIN shipping_zone_locations szl ON sz.id = szl.zone_id 
                              WHERE szl.location_type = 'pincode' AND TRIM(szl.location_value) = ? 
                              AND sz.is_active = 1");
        $stmt->execute([$pincode]);
        $zone = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($zone) return $zone;
    }
    // 2. City (case-insensitive)
    if ($city) {
        $stmt = $pdo->prepare("SELECT sz.* FROM shipping_zones sz 
                              JOIN shipping_zone_locations szl ON sz.id = szl.zone_id 
                              WHERE szl.location_type = 'city' AND LOWER(TRIM(szl.location_value)) = ? 
                              AND sz.is_active = 1");
        $stmt->execute([$city]);
        $zone = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($zone) return $zone;
    }
    // 3. State (case-insensitive)
    if ($state) {
        $stmt = $pdo->prepare("SELECT sz.* FROM shipping_zones sz 
                              JOIN shipping_zone_locations szl ON sz.id = szl.zone_id 
                              WHERE szl.location_type = 'state' AND LOWER(TRIM(szl.location_value)) = ? 
                              AND sz.is_active = 1");
        $stmt->execute([$state]);
        $zone = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($zone) return $zone;
    }
    // 4. Country (case-insensitive)
    if ($country) {
        $stmt = $pdo->prepare("SELECT sz.* FROM shipping_zones sz 
                              JOIN shipping_zone_locations szl ON sz.id = szl.zone_id 
                              WHERE szl.location_type = 'country' AND LOWER(TRIM(szl.location_value)) = ? 
                              AND sz.is_active = 1");
        $stmt->execute([$country]);
        $zone = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($zone) return $zone;
    }
    return null;
}

/**
 * Calculate shipping charge for a given location and order amount
 */
function calculateShippingCharge($state, $city = null, $pincode = null, $order_amount = 0) {
    global $pdo;
    
    $zone = getShippingZone($state, $city, $pincode);
    if (!$zone) {
        return [
            'charge' => 0,
            'zone_id' => null,
            'zone_name' => 'No zone found'
        ];
    }
    
    // Get shipping charge for this zone
    $stmt = $pdo->prepare("SELECT * FROM shipping_charges 
                          WHERE zone_id = ? AND is_active = 1 
                          AND (min_order_amount <= ? OR min_order_amount = 0)
                          AND (max_order_amount IS NULL OR max_order_amount >= ?)
                          ORDER BY charge_value ASC LIMIT 1");
    $stmt->execute([$zone['id'], $order_amount, $order_amount]);
    $charge = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$charge) {
        return [
            'charge' => 0,
            'zone_id' => $zone['id'],
            'zone_name' => $zone['name']
        ];
    }
    
    $shipping_amount = $charge['charge_value'];
    if ($charge['charge_type'] === 'percentage') {
        $shipping_amount = ($order_amount * $charge['charge_value']) / 100;
    }
    
    return [
        'charge' => $shipping_amount,
        'zone_id' => $zone['id'],
        'zone_name' => $zone['name'],
        'charge_type' => $charge['charge_type'],
        'charge_value' => $charge['charge_value']
    ];
}

/**
 * Calculate total order amount including GST and shipping
 */
function calculateOrderTotal($cart_items, $delivery_state, $delivery_city = null, $delivery_pincode = null) {
    $subtotal = 0;
    $total_gst = 0;
    $gst_breakdown = [];
    $seller_state = 'Maharashtra'; // Set your seller's state here
    // Calculate subtotal and GST for each item
    foreach ($cart_items as $item) {
        $item_price = isset($item['selling_price']) ? $item['selling_price'] : 0;
        $item_total = $item_price * $item['quantity'];
        $subtotal += $item_total;
        // Calculate GST for this item using seller_state as billing_state
        $gst_calc = calculateGST($item_price, $item['gst_rate'], $item['gst_type'], $delivery_state, $seller_state);
        $item_gst = $gst_calc['total_gst'] * $item['quantity'];
        $total_gst += $item_gst;
        // Store GST breakdown
        $gst_breakdown[] = [
            'product_id' => $item['product_id'],
            'gst_amount' => $item_gst,
            'sgst' => $gst_calc['sgst'] * $item['quantity'],
            'cgst' => $gst_calc['cgst'] * $item['quantity'],
            'igst' => $gst_calc['igst'] * $item['quantity'],
            'gst_type' => $gst_calc['gst_type']
        ];
    }
    // Calculate shipping charge ONCE for the whole order
    $shipping = calculateShippingCharge($delivery_state, $delivery_city, $delivery_pincode, $subtotal);
    $total = $subtotal + $total_gst + $shipping['charge'];
    // Calculate totals for each GST type
    $sgst_total = 0; $cgst_total = 0; $igst_total = 0;
    foreach ($gst_breakdown as $item) {
        $sgst_total += $item['sgst'];
        $cgst_total += $item['cgst'];
        $igst_total += $item['igst'];
    }
    return [
        'subtotal' => $subtotal,
        'gst_amount' => $total_gst,
        'shipping_charge' => $shipping['charge'],
        'total' => $total,
        'gst_breakdown' => $gst_breakdown,
        'shipping_zone_id' => $shipping['zone_id'],
        'shipping_zone_name' => $shipping['zone_name'],
        'sgst_total' => $sgst_total,
        'cgst_total' => $cgst_total,
        'igst_total' => $igst_total
    ];
}

/**
 * Get all shipping zones for admin
 */
function getAllShippingZones() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM shipping_zones ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get shipping zone locations
 */
function getShippingZoneLocations($zone_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM shipping_zone_locations WHERE zone_id = ? ORDER BY location_type, location_value");
    $stmt->execute([$zone_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get shipping charges for a zone
 */
function getShippingCharges($zone_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM shipping_charges WHERE zone_id = ? ORDER BY min_order_amount");
    $stmt->execute([$zone_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get Indian states list
 */
function getIndianStates() {
    return [
        'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh',
        'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand',
        'Karnataka', 'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur',
        'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab',
        'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura',
        'Uttar Pradesh', 'Uttarakhand', 'West Bengal', 'Delhi', 'Jammu and Kashmir',
        'Ladakh', 'Chandigarh', 'Dadra and Nagar Haveli and Daman and Diu',
        'Lakshadweep', 'Puducherry', 'Andaman and Nicobar Islands'
    ];
}

/**
 * Get common Indian cities
 */
function getCommonCities() {
    return [
        'Mumbai', 'Delhi', 'Bangalore', 'Hyderabad', 'Chennai', 'Kolkata',
        'Pune', 'Ahmedabad', 'Jaipur', 'Surat', 'Lucknow', 'Kanpur',
        'Nagpur', 'Indore', 'Thane', 'Bhopal', 'Visakhapatnam', 'Pimpri-Chinchwad',
        'Patna', 'Vadodara', 'Ghaziabad', 'Ludhiana', 'Agra', 'Nashik',
        'Faridabad', 'Meerut', 'Rajkot', 'Kalyan-Dombivali', 'Vasai-Virar',
        'Varanasi', 'Srinagar', 'Aurangabad', 'Dhanbad', 'Amritsar',
        'Allahabad', 'Ranchi', 'Howrah', 'Coimbatore', 'Jabalpur',
        'Gwalior', 'Vijayawada', 'Jodhpur', 'Madurai', 'Raipur',
        'Kota', 'Guwahati', 'Chandigarh', 'Solapur', 'Hubli-Dharwad',
        'Bareilly', 'Moradabad', 'Mysore', 'Gurgaon', 'Aligarh',
        'Jalandhar', 'Tiruchirappalli', 'Bhubaneswar', 'Salem', 'Warangal',
        'Mira-Bhayandar', 'Thiruvananthapuram', 'Bhiwandi', 'Saharanpur',
        'Gorakhpur', 'Guntur', 'Bikaner', 'Amravati', 'Noida',
        'Jamshedpur', 'Bhilai', 'Cuttack', 'Firozabad', 'Kochi',
        'Nellore', 'Bhavnagar', 'Dehradun', 'Durgapur', 'Asansol',
        'Rourkela', 'Nanded', 'Kolhapur', 'Ajmer', 'Akola',
        'Gulbarga', 'Jamnagar', 'Ujjain', 'Loni', 'Siliguri',
        'Jhansi', 'Ulhasnagar', 'Jammu', 'Sangli-Miraj & Kupwad', 'Mangalore',
        'Erode', 'Belgaum', 'Ambattur', 'Tirunelveli', 'Malegaon',
        'Gaya', 'Jalgaon', 'Udaipur', 'Maheshtala', 'Tirupur',
        'Davanagere', 'Kozhikode', 'Akola', 'Kurnool', 'Rajpur Sonarpur',
        'Bokaro', 'South Dumdum', 'Bellary', 'Patiala', 'Gopalpur',
        'Agartala', 'Bhagalpur', 'Muzaffarnagar', 'Bhatpara', 'Panihati',
        'Latur', 'Dhule', 'Rohtak', 'Korba', 'Bhilwara',
        'Brahmapur', 'Muzaffarpur', 'Ahmednagar', 'Mathura', 'Kollam',
        'Avadi', 'Kadapa', 'Kamarhati', 'Bilaspur', 'Shahjahanpur',
        'Satara', 'Bijapur', 'Rampur', 'Shivamogga', 'Chandrapur',
        'Junagadh', 'Thrissur', 'Alwar', 'Bardhaman', 'Kulti',
        'Kakinada', 'Nizamabad', 'Parbhani', 'Tumkur', 'Hisar',
        'Ozhukarai', 'Bihar Sharif', 'Panipat', 'Darbhanga', 'Bally',
        'Aizawl', 'Dewas', 'Ichalkaranji', 'Tirupati', 'Karnal',
        'Bathinda', 'Rae Bareli', 'Farrukhabad-cum-Fatehgarh', 'Saugor', 'Ratlam',
        'Hapur', 'Arrah', 'Anantapur', 'Karimnagar', 'Etawah',
        'Ambernath', 'North Dumdum', 'Bharatpur', 'Begusarai', 'New Delhi',
        'Gandhidham', 'Baranagar', 'Tiruvottiyur', 'Puducherry', 'Sikar',
        'Thoothukkudi', 'Rewa', 'Mirzapur-cum-Vindhyachal', 'Raichur', 'Pali',
        'Ramagundam', 'Haridwar', 'Vijayanagaram', 'Katihar', 'Nagercoil',
        'Sri Ganganagar', 'Karawal Nagar', 'Mango', 'Thanjavur', 'Bulandshahr',
        'Uluberia', 'Katni', 'Sambhal', 'Singrauli', 'Nadiad',
        'Secunderabad', 'Naihati', 'Yamunanagar', 'Bidhan Nagar', 'Pallavaram',
        'Bidar', 'Munger', 'Panchkula', 'Burhanpur', 'Raurkela Industrial Township',
        'Kharagpur', 'Dindigul', 'Gandhinagar', 'Hospet', 'Nangloi Jat',
        'Malda', 'Ongole', 'Deoghar', 'Chapra', 'Haldia',
        'Khandwa', 'Nandyal', 'Morena', 'Amroha', 'Anand',
        'Bhind', 'Bhalswa Jahangir Pur', 'Madhyamgram', 'Bhiwani', 'Berhampore',
        'Ambala', 'Mori', 'Fatehpur', 'Raebareli', 'Khora, Ghaziabad',
        'Chittoor', 'Bhusawal', 'Orai', 'Bahraich', 'Phusro',
        'Vellore', 'Mehsana', 'Raipur', 'Dehri', 'Delhi Cantonment',
        'Chirala', 'Alappuzha', 'Kottayam', 'Machilipatnam', 'Shimla',
        'Adoni', 'Tenali', 'Proddatur', 'Saharsa', 'Hindupur',
        'Sasaram', 'Hajipur', 'Bhagalpur', 'Deoghar', 'Adityapur',
        'Bettiah', 'Katihar', 'Purulia', 'Hassan', 'Ambikapur',
        'Saidapur', 'Barasat', 'Tiruvannamalai', 'Kaithal', 'Godhra',
        'Budaun', 'Hazaribagh', 'Hindupur', 'Bhiwandi', 'Tonk',
        'Udhampur', 'Batala', 'Dabgram', 'Pithampur', 'Karnal',
        'Bhiwani', 'Hisar', 'Fatehabad', 'Jind', 'Sirsa',
        'Yamunanagar', 'Panipat', 'Karnal', 'Thanesar', 'Kaithal',
        'Gurgaon', 'Faridabad', 'Palwal', 'Ballabhgarh', 'Hodal',
        'Nuh', 'Ferozepur Jhirka', 'Sohna', 'Mewat', 'Rewari',
        'Bhiwani', 'Rohtak', 'Jhajjar', 'Sonipat', 'Gohana',
        'Gannaur', 'Samalkha', 'Panipat', 'Israna', 'Asandh',
        'Taraori', 'Nilokheri', 'Indri', 'Shahbad', 'Babain',
        'Karnal', 'Gharaunda', 'Assandh', 'Panipat', 'Samalkha',
        'Gohana', 'Sonipat', 'Gannaur', 'Rohtak', 'Jhajjar',
        'Bhiwani', 'Rewari', 'Mewat', 'Sohna', 'Ferozepur Jhirka',
        'Nuh', 'Hodal', 'Ballabhgarh', 'Palwal', 'Faridabad',
        'Gurgaon', 'Thanesar', 'Karnal', 'Panipat', 'Yamunanagar',
        'Sirsa', 'Jind', 'Fatehabad', 'Hisar', 'Bhiwani',
        'Karnal', 'Pithampur', 'Dabgram', 'Batala', 'Udhampur',
        'Tonk', 'Bhiwandi', 'Hindupur', 'Hazaribagh', 'Budaun',
        'Godhra', 'Kaithal', 'Tiruvannamalai', 'Barasat', 'Saidapur',
        'Ambikapur', 'Hassan', 'Purulia', 'Katihar', 'Bettiah',
        'Adityapur', 'Deoghar', 'Bhagalpur', 'Hajipur', 'Sasaram',
        'Hindupur', 'Saharsa', 'Proddatur', 'Tenali', 'Adoni',
        'Shimla', 'Machilipatnam', 'Kottayam', 'Alappuzha', 'Chirala',
        'Delhi Cantonment', 'Dehri', 'Raipur', 'Mehsana', 'Vellore',
        'Phusro', 'Bahraich', 'Orai', 'Bhusawal', 'Chittoor',
        'Khora, Ghaziabad', 'Raebareli', 'Fatehpur', 'Mori', 'Ambala',
        'Berhampore', 'Bhiwani', 'Madhyamgram', 'Bhalswa Jahangir Pur', 'Bhind',
        'Anand', 'Amroha', 'Morena', 'Nandyal', 'Khandwa',
        'Haldia', 'Chapra', 'Deoghar', 'Ongole', 'Malda',
        'Nangloi Jat', 'Hospet', 'Gandhinagar', 'Dindigul', 'Kharagpur',
        'Raurkela Industrial Township', 'Burhanpur', 'Panchkula', 'Munger', 'Bidar',
        'Pallavaram', 'Bidhan Nagar', 'Yamunanagar', 'Naihati', 'Secunderabad',
        'Nadiad', 'Singrauli', 'Sambhal', 'Katni', 'Uluberia',
        'Bulandshahr', 'Thanjavur', 'Mango', 'Karawal Nagar', 'Sri Ganganagar',
        'Nagercoil', 'Katihar', 'Vijayanagaram', 'Haridwar', 'Ramagundam',
        'Pali', 'Raichur', 'Mirzapur-cum-Vindhyachal', 'Rewa', 'Thoothukkudi',
        'Sikar', 'Puducherry', 'Tiruvottiyur', 'Baranagar', 'Gandhidham',
        'New Delhi', 'Begusarai', 'Bharatpur', 'North Dumdum', 'Ambernath',
        'Etawah', 'Karimnagar', 'Anantapur', 'Arrah', 'Hapur',
        'Ratlam', 'Saugor', 'Farrukhabad-cum-Fatehgarh', 'Rae Bareli', 'Bathinda',
        'Karnal', 'Tirupati', 'Ichalkaranji', 'Dewas', 'Aizawl',
        'Bally', 'Darbhanga', 'Panipat', 'Bihar Sharif', 'Ozhukarai',
        'Hisar', 'Tumkur', 'Parbhani', 'Nizamabad', 'Kakinada',
        'Kulti', 'Bardhaman', 'Alwar', 'Thrissur', 'Junagadh',
        'Chandrapur', 'Shivamogga', 'Rampur', 'Bijapur', 'Satara',
        'Shahjahanpur', 'Bilaspur', 'Kamarhati', 'Kadapa', 'Avadi',
        'Kollam', 'Mathura', 'Ahmednagar', 'Muzaffarpur', 'Brahmapur',
        'Bhilwara', 'Korba', 'Rohtak', 'Dhule', 'Latur',
        'Panihati', 'Bhatpara', 'Muzaffarnagar', 'Bhagalpur', 'Agartala',
        'Gopalpur', 'Patiala', 'Bellary', 'South Dumdum', 'Bokaro',
        'Rajpur Sonarpur', 'Kurnool', 'Akola', 'Kozhikode', 'Davanagere',
        'Tirupur', 'Maheshtala', 'Udaipur', 'Jalgaon', 'Gaya',
        'Malegaon', 'Tirunelveli', 'Tirupur', 'Ambattur', 'Belgaum',
        'Erode', 'Mangalore', 'Sangli-Miraj & Kupwad', 'Jammu', 'Ulhasnagar',
        'Jhansi', 'Siliguri', 'Loni', 'Ujjain', 'Jamnagar',
        'Gulbarga', 'Akola', 'Ajmer', 'Kolhapur', 'Nanded',
        'Rourkela', 'Asansol', 'Durgapur', 'Dehradun', 'Bhavnagar',
        'Nellore', 'Kochi', 'Firozabad', 'Cuttack', 'Bhilai',
        'Jamshedpur', 'Noida', 'Amravati', 'Bikaner', 'Guntur',
        'Gorakhpur', 'Saharanpur', 'Bhiwandi', 'Thiruvananthapuram', 'Mira-Bhayandar',
        'Warangal', 'Salem', 'Bhubaneswar', 'Tiruchirappalli', 'Jalandhar',
        'Aligarh', 'Gurgaon', 'Mysore', 'Moradabad', 'Bareilly',
        'Hubli-Dharwad', 'Solapur', 'Chandigarh', 'Guwahati', 'Kota',
        'Raipur', 'Madurai', 'Jodhpur', 'Vijayawada', 'Jabalpur',
        'Gwalior', 'Coimbatore', 'Howrah', 'Ranchi', 'Allahabad',
        'Amritsar', 'Dhanbad', 'Srinagar', 'Aurangabad', 'Meerut',
        'Faridabad', 'Nashik', 'Agra', 'Ludhiana', 'Vadodara',
        'Patna', 'Pimpri-Chinchwad', 'Visakhapatnam', 'Bhopal', 'Thane',
        'Indore', 'Nagpur', 'Kanpur', 'Lucknow', 'Surat',
        'Jaipur', 'Ahmedabad', 'Pune', 'Kolkata', 'Chennai',
        'Hyderabad', 'Bangalore', 'Delhi', 'Mumbai'
    ];
}

/**
 * Format GST breakdown for display
 */
function formatGSTBreakdown($gst_breakdown) {
    $total_sgst = 0;
    $total_cgst = 0;
    $total_igst = 0;
    
    foreach ($gst_breakdown as $item) {
        $total_sgst += $item['sgst'];
        $total_cgst += $item['cgst'];
        $total_igst += $item['igst'];
    }
    
    return [
        'sgst' => $total_sgst,
        'cgst' => $total_cgst,
        'igst' => $total_igst,
        'total_gst' => $total_sgst + $total_cgst + $total_igst
    ];
}

/**
 * Get GST breakdown for display purposes (admin panel)
 * This shows how GST will be calculated based on the product's GST type
 */
function getGSTBreakdownForDisplay($gst_type, $gst_rate) {
    if ($gst_type === 'sgst_cgst') {
        $half_rate = $gst_rate / 2;
        return [
            'type' => 'SGST + CGST',
            'sgst_rate' => $half_rate,
            'cgst_rate' => $half_rate,
            'igst_rate' => 0,
            'total_rate' => $gst_rate
        ];
    } else {
        return [
            'type' => 'IGST',
            'sgst_rate' => 0,
            'cgst_rate' => 0,
            'igst_rate' => $gst_rate,
            'total_rate' => $gst_rate
        ];
    }
}
?> 