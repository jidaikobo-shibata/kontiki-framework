<?php

namespace Jidaikobo\Kontiki\Frontend;

class ApiClient
{
    /**
     * Send an HTTP request and return the decoded JSON response as an array.
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
        $ch = self::initializeCurl($url, $method, $data, $headers);

        // Execute the cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Handle errors
        self::handleCurlError($response, $error);
        self::handleHttpError($httpCode, $response);

        // Decode the JSON response
        return self::decodeJsonResponse($response);
    }

    /**
     * Initialize and configure a cURL session.
     *
     * @param string $url The endpoint URL.
     * @param string $method The HTTP method.
     * @param array|null $data Data to send with the request (optional).
     * @param array $headers Additional HTTP headers (optional).
     * @return resource|CurlHandle The initialized cURL handle.
     */
    private static function initializeCurl(string $url, string $method, ?array $data, array $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // Set method-specific options
        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if ($data !== null) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'GET':
                if ($data !== null) {
                    $url .= '?' . http_build_query($data);
                }
                break;
            default:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                if ($data !== null) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
        }

        // Set headers
        $defaultHeaders = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($defaultHeaders, $headers));

        return $ch;
    }

    /**
     * Handle cURL execution errors.
     *
     * @param mixed $response The cURL response.
     * @param string $error The cURL error message, if any.
     * @throws \Exception If a cURL error occurred.
     */
    private static function handleCurlError($response, string $error): void
    {
        if ($response === false) {
            throw new \Exception("cURL error: $error");
        }
    }

    /**
     * Handle HTTP errors based on the status code.
     *
     * @param int $httpCode The HTTP status code.
     * @param string $response The HTTP response body.
     * @throws \Exception If an HTTP error occurred.
     */
    private static function handleHttpError(int $httpCode, string $response): void
    {
        if ($httpCode < 200 || $httpCode >= 300) {
            throw new \Exception("HTTP error: $httpCode, Response: $response");
        }
    }

    /**
     * Decode a JSON response and return it as an array.
     *
     * @param string $response The JSON response body.
     * @return array The decoded JSON data.
     * @throws \Exception If the response cannot be decoded as JSON.
     */
    private static function decodeJsonResponse(string $response): array
    {
        $decoded = json_decode($response, true);
        if ($decoded === null) {
            throw new \Exception("Invalid JSON response: $response");
        }
        return $decoded;
    }
}
