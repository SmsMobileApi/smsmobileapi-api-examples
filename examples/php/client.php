<?php

declare(strict_types=1);

const BASE_URL = 'https://api.smsmobileapi.com';

function envRequired(string $name): string
{
    $value = trim((string) getenv($name));
    if ($value === '') {
        fwrite(STDERR, "Missing environment variable: {$name}\n");
        exit(2);
    }
    return $value;
}

function request(string $method, string $path, array $parameters): array
{
    $method = strtoupper($method);
    $url = BASE_URL . $path;
    $curl = curl_init();
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
    ];
    if ($method === 'GET') {
        $url .= '?' . http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
    } else {
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
        $options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/x-www-form-urlencoded';
    }
    $options[CURLOPT_URL] = $url;
    curl_setopt_array($curl, $options);
    $body = curl_exec($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $error = curl_error($curl);
    curl_close($curl);
    if ($body === false || $error !== '') {
        throw new RuntimeException($error ?: 'Request failed');
    }
    $decoded = json_decode($body, true);
    if ($status < 200 || $status >= 300) {
        throw new RuntimeException("HTTP {$status}: {$body}");
    }
    return is_array($decoded) ? $decoded : ['raw' => $body];
}

$apiKey = envRequired('SMSMOBILEAPI_API_KEY');
$command = $argv[1] ?? 'sms:send';

$result = match ($command) {
    'sms:send' => request('POST', '/sendsms/', [
        'apikey' => $apiKey,
        'recipients' => envRequired('SMSMOBILEAPI_RECIPIENT'),
        'message' => 'Hello from the SMSMobileAPI PHP example.',
    ]),
    'sms:received' => request('GET', '/getsms/', ['apikey' => $apiKey, 'onlyunread' => 'yes']),
    'calls:missed' => request('GET', '/call/missed/list/', ['apikey' => $apiKey, 'limit' => 20]),
    'devices:list' => request('GET', '/gateway/mobile/list/', ['apikey' => $apiKey]),
    'whatsapp:synchronize' => request('GET', '/getwa/synchronisation/', ['apikey' => $apiKey]),
    default => throw new InvalidArgumentException("Unknown command: {$command}"),
};

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
