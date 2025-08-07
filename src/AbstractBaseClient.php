<?php

namespace SeanKndy\PlumeApi;

use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractBaseClient implements ClientInterface
{
    protected ClientConfiguration $clientConfiguration;

    protected AccessTokenCache $accessTokenCache;

    protected \GuzzleHttp\Client $guzzle;

    public function __construct(ClientConfiguration $clientConfiguration)
    {
        $this->clientConfiguration = $clientConfiguration;
        $this->guzzle = new \GuzzleHttp\Client([
            'http_errors' => false,
            'timeout' => $clientConfiguration->timeout,
        ]);
        $this->accessTokenCache = new AccessTokenCache($clientConfiguration->tokenCacheFile);
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
        if (is_array($body)) {
            $body = json_encode($body);

            if (!isset($headers['Content-Type'])) {
                $headers['Content-Type'] = 'application/json';
                $headers['Accept'] = 'application/json';
            }
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

        $url = rtrim($this->clientConfiguration->apiBaseUrl, '/') . '/' . ltrim($endpoint, '/');

        return $this->request($method, $url, $body, $headers);
    }

    protected function acquireAccessToken(): string
    {
        if (!($accessToken = $this->accessTokenCache->get())) {
            // get new access token from Plume api
            $response = $this->request('POST', $this->clientConfiguration->authorizeUrl, http_build_query([
                'scope' => $this->clientConfiguration->scope,
                'grant_type' => 'client_credentials',
            ]), [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->clientConfiguration->appBasicAuth),
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

}