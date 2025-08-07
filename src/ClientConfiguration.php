<?php

namespace SeanKndy\PlumeApi;

class ClientConfiguration
{
    public string $scope;

    public string $authorizeUrl;

    public string $appBasicAuth;

    public string $apiBaseUrl;

    public string $tokenCacheFile;

    public int $timeout;

    public function __construct(
        string $scope,
        string $authorizeUrl,
        string $appBasicAuth,
        string $apiBaseUrl,
        string $tokenCacheFile = 'php://memory',
        int    $timeout = 10
    ) {
        $this->scope = $scope;
        $this->authorizeUrl = $authorizeUrl;
        $this->appBasicAuth = $appBasicAuth;
        $this->apiBaseUrl = $apiBaseUrl;
        $this->tokenCacheFile = $tokenCacheFile;
        $this->timeout = $timeout;
    }
}