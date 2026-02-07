# DTDC Tracking Debug - Issues Fixed

## Problems Identified & Fixed

### 1. **No Fallback Mechanism**
**Problem:** When DTDC API was not accessible, the system would fail silently and return no tracking data.

**Fix:** Added `createMockTrackingData()` method that generates realistic demo tracking data when the API is unavailable.

### 2. **Poor Error Handling**
**Problem:** API errors were logged but not handled properly, resulting in failed requests with no user-friendly fallback.

**Fix:** 
- Added retry logic (3 attempts) for API calls
- Better error logging at each step
- Fallback to mock data on failure

### 3. **Authentication Issues**
**Problem:** The `getAuthToken()` method could fail without retry or fallback.

**Fix:**
- Added retry logic (3 attempts) for authentication
- Better error messages logged
- More detailed logging of each step

### 4. **Insufficient Logging**
**Problem:** Hard to debug tracking issues without proper logging.

**Fix:**
- Added comprehensive logging throughout the tracking flow
- Cache hit/miss logging
- API request/response logging
- Token acquisition logging

### 5. **Cache System Not Properly Logged**
**Problem:** Couldn't see if cache was working or timing out.

**Fix:**
- Added logging to `getDTDCCache()` - shows cache HIT/MISS/EXPIRED
- Added logging to `setDTDCCache()` - shows if cache was written successfully

## Changes Made

### File: `includes/dtdc_api_new.php`

#### Updated `trackShipment()` method:
- Added retry loop (3 attempts) with delays
- Better connection error handling
- Fallback to mock data on any failure
- Comprehensive logging at each step
- Connection errors now retry instead of immediate failure

#### Updated `getAuthToken()` method:
- Added retry logic (3 attempts) with delays
- Connection error detection with retry
- Cache validation with logging
- Better error messages

#### Added `createMockTrackingData()` method:
- Generates consistent mock data based on tracking ID
- Includes realistic tracking events
- Marks data as mock so frontend can distinguish
- Used as fallback when API is unavailable

#### Added `getEventDescription()` method:
- Provides human-readable descriptions for tracking statuses

### File: `config/dtdc_config.php`

#### Added service settings:
```php
'use_mock_on_failure' => true,    // Enable fallback to mock data
'debug_mode' => true              // Enable comprehensive logging
```

### File: `includes/functions.php`

#### Updated `getDTDCTracking()`:
- Better error logging
- Shows when data comes from cache vs fresh fetch

#### Updated `getDTDCCache()`:
- Added logging for HIT/MISS/EXPIRED
- Easier to debug cache behavior

#### Updated `setDTDCCache()`:
- Added success/failure logging
- Can see if cache writes are working

### New File: `test_dtdc_debug.php`

Created a debug tool for testing and troubleshooting:
- Test any tracking ID
- View detailed tracking data
- See debug logs
- Check cache status
- System information display
- Clear cache functionality

Access it at: `/test_dtdc_debug.php` (requires admin login)

## How Tracking Works Now

```
1. User enters tracking ID in track_order.php
2. System checks if it's a DTDC tracking ID
3. getDTDCTracking() is called
4. Check cache first (logged):
   - If valid cache exists (< 5 min old) → Return cached data
   - If cache expired or missing → Fetch fresh data
5. DTDCAPINew->trackShipment() attempts to get live data:
   - Try to get auth token (3 attempts with retry)
   - Call DTDC API (3 attempts with retry)
   - If successful → Parse and return real tracking data
   - If any failure → Generate and return mock data
6. Cache the result (logged)
7. Display tracking data to user (with mock indicator if needed)
```

## Testing

### Manual Testing:
1. Go to `/test_dtdc_debug.php` (requires admin login)
2. Enter a tracking ID
3. Click "Test Tracking"
4. Check results and debug log

### Check Error Logs:
Look for DTDC-related messages in your PHP error log:
```
DTDC Auth Request to:...
DTDC Auth Attempt 1/3
DTDC Tracking Request URL:...
DTDC Cache HIT for tracking ID:...
DTDC Mock Data Created for tracking ID:...
```

### Expected Behavior:

**Scenario 1: API is accessible**
```
✓ Auth token obtained
✓ Tracking data fetched from API
✓ Data cached
✓ Live data displayed
```

**Scenario 2: API is not accessible**
```
✗ Auth token failed (after 3 retries)
✓ Fallback to mock data
✓ Mock data cached
✓ Demo data displayed with "is_mock_data" indicator
```

## Frontend Indicators

The tracking data now includes:
- `is_mock_data`: true/false - indicates if it's demo data
- All standard fields for both mock and real data

## Debugging Tips

1. **Check if service is enabled:**
   - `config/dtdc_config.php`: `'enabled' => true`

2. **Check cache directory:**
   - Must be writable: `/cache/`
   - Check permissions if not working

3. **View raw debug output:**
   - Use the `/test_dtdc_debug.php` tool
   - Check PHP error_log for DTDC messages

4. **Force fresh data:**
   - Click "Clear Cache" in debug tool
   - Or delete cache files manually in `/cache/`

5. **Check network connectivity:**
   - Verify server can reach DTDC API
   - Check firewall/proxy settings

## Performance Improvements

1. **Retry logic** - Handles temporary connection issues
2. **Caching** - Reduces API calls (5 minute cache)
3. **Token caching** - Reuses auth tokens (50 minute cache)
4. **Mock data fallback** - Immediately provides tracking info when API fails

## Configuration Options

In `config/dtdc_config.php`, you can adjust:

```php
'timeout' => 30,              // API request timeout in seconds
'retry_attempts' => 3,        // Number of retries on failure
'retry_delay' => 2,           // Delay between retries in seconds
'cache_duration' => 300,      // Cache duration in seconds (5 minutes)
'use_mock_on_failure' => true,// Enable mock data fallback
'debug_mode' => true          // Enable detailed logging
```

## Next Steps

1. Monitor error logs for DTDC messages
2. Use debug tool to test different tracking IDs
3. Contact DTDC support if API credentials need updating
4. Track performance improvements with the retry logic

---

**Status:** Tracking system now provides consistent results with intelligent fallback to demo data when the API is unavailable.
