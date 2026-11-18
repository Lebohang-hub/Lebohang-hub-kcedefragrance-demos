<?php
/**
 * PayFast custom checkout signature helper.
 * IMPORTANT: Keep the field order supplied by checkout.php / pay-registration.php.
 * PayFast custom checkout signatures are NOT alphabetically sorted.
 */
function payfast_signature(array $data, string $passphrase = ''): string {
    $parts = [];
    foreach ($data as $key => $value) {
        if ($key === 'signature' || $value === null || trim((string)$value) === '') {
            continue;
        }
        // PayFast requires URL-encoded, trimmed values and the documented field order.
        $parts[] = $key . '=' . urlencode(trim((string)$value));
    }

    $query = implode('&', $parts);
    if (trim($passphrase) !== '') {
        $query .= '&passphrase=' . urlencode(trim($passphrase));
    }

    return strtolower(md5($query));
}

function payfast_form(array $data): never {
    // Exclude blank values from BOTH the signature and submitted form so that the
    // payload PayFast receives is exactly the payload used to create the signature.
    foreach ($data as $key => $value) {
        if ($value === null || trim((string)$value) === '') {
            unset($data[$key]);
        } else {
            $data[$key] = trim((string)$value);
        }
    }

    $data['signature'] = payfast_signature($data, PAYFAST_PASSPHRASE);

    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Redirecting to PayFast</title></head><body style="font-family:Arial,sans-serif;padding:40px;text-align:center"><h2>Redirecting you to PayFast securely…</h2><p>Please wait. Do not refresh this page.</p><form id="payfast" action="' . htmlspecialchars(PAYFAST_PROCESS_URL, ENT_QUOTES, 'UTF-8') . '" method="post">';
    foreach ($data as $key => $value) {
        echo '<input type="hidden" name="' . htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') . '">';
    }
    echo '<noscript><button type="submit">Continue to PayFast</button></noscript></form><script>document.getElementById("payfast").submit();</script></body></html>';
    exit;
}

function payfast_validate_itn(array $data): bool {
    if (empty($data['signature'])) return false;

    $signature = (string)$data['signature'];
    unset($data['signature']);

    // ITN uses the same ordered, URL-encoded custom checkout signature convention.
    if (!hash_equals(payfast_signature($data, PAYFAST_PASSPHRASE), $signature)) return false;
    if (!function_exists('curl_init')) return false;

    $fields = [];
    foreach ($data as $key => $value) {
        if ($value === null || trim((string)$value) === '') continue;
        $fields[] = $key . '=' . urlencode(trim((string)$value));
    }

    $ch = curl_init(PAYFAST_VALIDATE_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => implode('&', $fields),
    ]);
    $response = curl_exec($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return trim((string)$response) === 'VALID' && $http >= 200 && $http < 300;
}
