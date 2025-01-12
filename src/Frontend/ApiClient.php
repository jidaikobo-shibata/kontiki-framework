<?php

namespace Jidaikobo\Kontiki\Frontend;

class ApiClient
{
    /**
     * Send an HTTP request to a given URL and return the decoded response as an array.
     *
     * @param string $url The endpoint URL.
     * @param string $method The HTTP method (GET, POST, PUT, DELETE, etc.).
     * @param array|null $data Data to be sent with the request (optional).
     * @param array $headers Additional headers to include in the request (optional).
     * @return array The response data as an array.
     * @throws \Exception If the request fails or the response is invalid.
     */
    public static function request(string $url, string $method = 'GET', ?array $data = null, array $headers = []): array
    {
        // Initialize cURL session
        $ch = curl_init();

        // Set base cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // Configure HTTP method and data
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if ($data !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method === 'GET' && $data !== null) {
            $url .= '?' . http_build_query($data);
            curl_setopt($ch, CURLOPT_URL, $url);
        }

        // Set HTTP headers
        $defaultHeaders = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($defaultHeaders, $headers));

        // Execute the request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Handle cURL errors
        if ($response === false) {
            throw new \Exception("cURL error: $error");
        }

        // Handle HTTP status errors
        if ($httpCode < 200 || $httpCode >= 300) {
            throw new \Exception("HTTP error: $httpCode, Response: $response");
        }

        // Decode the JSON response
        $decoded = json_decode($response, true);
        if ($decoded === null) {
            throw new \Exception("Invalid JSON response: $response");
        }

        return $decoded;
    }
}
