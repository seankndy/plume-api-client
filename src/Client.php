<?php

namespace SeanKndy\PlumeApi;

use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use SeanKndy\PlumeApi\Api\CustomersApi;

class Client
{
    private AppConfiguration $appConfiguration;

    private AccessTokenCache $accessTokenCache;

    private \GuzzleHttp\Client $guzzle;

    public function __construct(AppConfiguration $appConfiguration)
    {
        $this->appConfiguration = $appConfiguration;
        $this->guzzle = new \GuzzleHttp\Client([
            'http_errors' => false,
            'timeout' => $appConfiguration->timeout,
        ]);
        $this->accessTokenCache = new AccessTokenCache($appConfiguration->tokenCacheFile);
    }

    public function getAppConfiguration(): AppConfiguration
    {
        return $this->appConfiguration;
    }

    /**
     * Send HTTP request, return response.
     *
     * @param string $method HTTP Method
     * @param string $url Request URL
     * @param array|string $body Body to send; if an array then it will be JSON-encoded
     * @param array $headers Headers to send along with the HTTP request
     * @return ResponseInterface
     */
    public function request(string $method, string $url, $body = null, array $headers = []): ResponseInterface
    {
        if (is_array($body) && !isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'application/json';
            $headers['Accept'] = 'application/json';
            $body = json_encode($body);
        }

        return $this->guzzle->request($method, $url, [
            RequestOptions::HEADERS => $headers,
            RequestOptions::BODY => $body,
        ]);
    }

    /**
     * Send HTTP request after acquiring an access token, return response.
     *
     * @param string $method HTTP Method
     * @param string $endpoint Endpoint to send to (uses the AppConfiguration's apiBaseUrl for the base of the URL)
     * @param array|string $body Body to send; if an array then it will be JSON-encoded
     * @param array $headers Headers to send along with the HTTP request
     * @return ResponseInterface
     */
    public function authenticatedRequest(string $method, string $endpoint, $body = null, array $headers = []): ResponseInterface
    {
        $headers['Authorization'] = "Bearer " . $this->acquireAccessToken();

        $url = $this->appConfiguration->apiBaseUrl . '/' . ltrim($endpoint, '/');

        return $this->request($method, $url, $body, $headers);
    }

    private function acquireAccessToken(): string
    {
        if (!($accessToken = $this->accessTokenCache->get())) {
            // get new access token from Plume api
            $response = $this->request('POST', $this->appConfiguration->authorizeUrl, http_build_query([
                'scope' => 'partnerId:' . $this->appConfiguration->partnerId . ' role:partnerIdAdmin',
                'grant_type' => 'client_credentials',
            ]), [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->appConfiguration->appBasicAuth),
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \RuntimeException("Failed to get access token from Plume (HTTP status code {$response->getStatusCode()})!");
            }

            $body = @json_decode($response->getBody()->getContents(), true);
            $accessToken = $body['access_token'];
            $expiresIn = $body['expires_in'];

            $this->accessTokenCache->save($accessToken, $expiresIn);
        }

        return $accessToken;
    }

    public function customersApi(): CustomersApi
    {
        return new CustomersApi($this);
    }
}