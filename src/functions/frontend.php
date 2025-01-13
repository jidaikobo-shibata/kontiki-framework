<?php

if (!function_exists('getData')) {
    /**
     * Sends an API request and returns the response as an array.
     *
     * @param array $request Configuration for the API request.
     *                       - url (string): The endpoint URL (required).
     *                       - method (string): The HTTP method (GET, POST, etc.). Default is 'GET'.
     *                       - data (array|null): Data to send with the request (optional).
     *                       - headers (array): Additional HTTP headers (optional).
     * @return array|null The API response decoded as an array, or null on failure.
     */
    function getData(array $request): ?array
    {
        try {
            // Validate required parameter
            if (empty($request['url'])) {
                throw new InvalidArgumentException('The "url" parameter is required.');
            }

            // Extract parameters with defaults
            $url = $request['url'];
            $method = $request['method'] ?? 'GET';
            $data = $request['data'] ?? null;
            $headers = $request['headers'] ?? [];

            // Call the API using ApiClient
            return \Jidaikobo\Kontiki\Frontend\ApiClient::request($url, $method, $data, $headers);
        } catch (Exception $e) {
            // Log the error message for debugging
            error_log('API Request Error: ' . $e->getMessage());
            return null; // Return null in case of an error
        }
    }
}

if (!function_exists('homeUrl')) {
    function homeUrl(): string
    {
        return env('BASEURL');
    }
}
