<?php
function bannerButtonDefaults() {
    return ['enabled' => false, 'text' => 'Start Selling', 'url' => 'seller/login.php', 'left' => 35.1, 'top' => 71.8, 'width' => 11.3, 'height' => 14];
}

function validateBannerButton($input) {
    $button = bannerButtonDefaults();
    if (!is_array($input)) throw new InvalidArgumentException('Invalid banner button settings.');
    $button['enabled'] = !empty($input['enabled']);
    foreach (['text', 'url'] as $key) {
        if (isset($input[$key]) && !is_scalar($input[$key])) throw new InvalidArgumentException('Invalid button text or link.');
        $button[$key] = trim((string)($input[$key] ?? $button[$key]));
    }
    if (!$button['enabled']) return $button;
    if ($button['text'] === '' || strlen($button['text']) > 80) throw new InvalidArgumentException('Enter button text up to 80 characters.');
    $url = $button['url'];
    $absolute = filter_var($url, FILTER_VALIDATE_URL) && in_array(strtolower(parse_url($url, PHP_URL_SCHEME) ?? ''), ['http', 'https'], true);
    $relative = preg_match('~^/?[a-zA-Z0-9_-][a-zA-Z0-9_./?=&%#-]*$~D', $url) && strpos($url, ':') === false;
    if (strlen($url) > 1000 || (!$absolute && !$relative) || preg_match('/[\x00-\x20\\\\]/', $url)) throw new InvalidArgumentException('Enter a valid website link or a relative path such as seller/login.php.');
    foreach (['left', 'top', 'width', 'height'] as $key) {
        $value = filter_var($input[$key] ?? $button[$key], FILTER_VALIDATE_FLOAT);
        if ($value === false || !is_finite($value) || $value < 0 || $value > 100 || (in_array($key, ['width', 'height'], true) && $value < 1)) throw new InvalidArgumentException('Button placement must be between 0 and 100 percent, with width and height at least 1 percent.');
        $button[$key] = $value;
    }
    if ($button['left'] + $button['width'] > 100 || $button['top'] + $button['height'] > 100) throw new InvalidArgumentException('Keep the button inside the banner.');
    return $button;
}

function getBannerButton($json) {
    try { return validateBannerButton(json_decode($json ?? '', true) ?? []); }
    catch (InvalidArgumentException $e) { return bannerButtonDefaults(); }
}
