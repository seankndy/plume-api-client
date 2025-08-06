<?php

namespace SeanKndy\PlumeApi\Api;

class CustomersApi extends AbstractBaseApi
{
    public function register(
        string $email,
        string $name,
        string $accountId,
        string $profile = 'auto',
        string $acceptLanguage = 'en-US',
        ?string $onboardingCheckpoint = null
    ): array {
        return $this->resourceCreationRequest('/Customers/register', array_merge([
            'email' => $email,
            'name' => $name,
            'partnerId' => $this->client->getAppConfiguration()->partnerId,
            'accountId' => $accountId,
            'profile' => $profile,
            'acceptLanguage' => $acceptLanguage,
        ], $onboardingCheckpoint ? ['onboardingCheckpoint' => $onboardingCheckpoint] : []));
    }

    public function delete(string $customerId): void
    {
        $this->resourceDeletionRequest("/Customers/$customerId");
    }

    public function search(string $field, string $value): array
    {
        return $this->resourceGetRequest("/Partners/customers/search/" . urlencode($value) . "?field=$field");
    }

    public function claimNode(string $customerId, string $locationId, string $serialNumber): array
    {
        return $this->resourceCreationRequest("/Customers/$customerId/locations/$locationId/nodes", [
            'serialNumber' => $serialNumber,
        ]);
    }

    public function unclaimNode(string $customerId, string $locationId, string $nodeId, bool $preservePackId = false, bool $removeAccountId = false): void
    {
        $this->resourceDeletionRequest("/Customers/$customerId/locations/$locationId/nodes/$nodeId", [
            'preservePackId' => $preservePackId,
            'removeAccountId' => $removeAccountId
        ]);
    }

    public function createWifiNetwork(string $customerId, string $locationId, string $ssid, string $encryptionKey): array
    {
        return $this->resourceCreationRequest("/Customers/$customerId/locations/$locationId/wifiNetwork", [
            'ssid' => $ssid,
            'encryptionKey' => $encryptionKey,
        ]);
    }

    public function getNodes(string $customerId, string $locationId): array
    {
        $data = $this->resourceGetRequest("/Customers/$customerId/locations/$locationId/nodes");
        if (!isset($data['nodes'])) {
            return [];
        }
        return $data['nodes'];
    }

    public function getNode(string $customerId, string $locationId, string $nodeId): array
    {
        return $this->resourceGetRequest("/Customers/$customerId/locations/$locationId/nodes/$nodeId");
    }

    public function renameNode(string $customerId, string $locationId, string $nodeId, string $newName): array
    {
        return $this->resourcePutRequest("/Customers/$customerId/locations/$locationId/nodes/$nodeId", [
            'nickname' => $newName,
        ]);
    }

    public function getWanConfiguration(string $customerId, string $locationId): array
    {
        return $this->resourceGetRequest("/Customers/$customerId/locations/$locationId/wanConfiguration");
    }

    public function updateWanConfiguration(string $customerId, string $locationId, array $params): void
    {
        $this->resourcePutRequest("/Customers/$customerId/locations/$locationId/wanConfiguration", $params);
    }
}