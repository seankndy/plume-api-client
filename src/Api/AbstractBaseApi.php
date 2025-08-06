<?php

namespace SeanKndy\PlumeApi\Api;

use Psr\Http\Message\ResponseInterface;
use SeanKndy\PlumeApi\Client;

abstract class AbstractBaseApi
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    protected function getErrorMessageFromResponse(ResponseInterface $response): string
    {
        if ($body = @json_decode($response->getBody()->getContents(), true)) {
            return isset($body['error'], $body['error']['message']) ? $body['error']['message'] : '';
        }
        return '';
    }

    protected function resourceCreationRequest(string $endpoint, array $data): array
    {
        $response = $this->client->authenticatedRequest('POST', $endpoint, $data);

        if ($response->getStatusCode() !== 200 && $response->getStatusCode() !== 201) {
            throw new \RuntimeException("Failed to create resource (HTTP status code {$response->getStatusCode()}): " .
                $this->getErrorMessageFromResponse($response));
        }

        return @json_decode($response->getBody()->getContents(), true);
    }

    protected function resourceDeletionRequest(string $endpoint, array $data = []): void
    {
        $response = $this->client->authenticatedRequest('DELETE', $endpoint, $data);

        if ($response->getStatusCode() !== 200 && $response->getStatusCode() !== 204) {
            throw new \RuntimeException("Failed to delete resource (HTTP status code {$response->getStatusCode()}): " .
                $this->getErrorMessageFromResponse($response));
        }
    }

    protected function resourceGetRequest(string $endpoint): array
    {
        $response = $this->client->authenticatedRequest('GET', $endpoint);

        if ($response->getStatusCode() === 404) {
            return [];
        }

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException("Failed to fetch resource (HTTP status code {$response->getStatusCode()}): " .
                $this->getErrorMessageFromResponse($response));
        }

        return @json_decode($response->getBody()->getContents(), true);
    }

    protected function resourcePutRequest(string $endpoint, array $data = []): array
    {
        $response = $this->client->authenticatedRequest('PUT', $endpoint, $data);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException("Failed to update resource (HTTP status code {$response->getStatusCode()}): " .
                $this->getErrorMessageFromResponse($response));
        }

        return @json_decode($response->getBody()->getContents(), true);
    }

}