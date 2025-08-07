<?php

namespace SeanKndy\PlumeApi\Uprise\Api;

use SeanKndy\PlumeApi\AbstractBaseApi;
use SeanKndy\PlumeApi\ClientInterface;

class PropertiesApi extends AbstractBaseApi
{
    private string $partnerId;

    public function __construct(ClientInterface $client, string $partnerId)
    {
        parent::__construct($client);

        $this->partnerId = $partnerId;
    }

    public function create(
        string $name,
        string $address,
        string $city,
        string $state,
        string $zip,
        float $latitude,
        float $longitude,
        string $country,
        string $language = 'en',
        bool $mduOptimizationEnabled = true,
        array $additionalProperties = []
    ): array {
        return $this->resourceCreationRequest("/uprise/v1/partners/{$this->partnerId}/properties", array_merge([
            'name' => $name,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'postal_code' => $zip,
            'geoInfo' => ['lat' => $latitude, 'long' => $longitude],
            'country' => $country,
            'language' => $language,
            'mdu_optimizations_enabled' => $mduOptimizationEnabled,
        ], $additionalProperties));
    }

    public function get(int $page = 1): array
    {
        return $this->resourceGetRequest("/v1/partners/{$this->partnerId}/properties?page=$page");
    }

    public function delete(string $propertyId): void
    {
        $this->resourceDeletionRequest("/v1/partners/{$this->partnerId}/properties/{$propertyId}");
    }
}