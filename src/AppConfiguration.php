<?php

namespace SeanKndy\PlumeApi;

class AppConfiguration
{
    public string $partnerId;

    public string $authorizeUrl;

    public string $appBasicAuth;

    public string $apiBaseUrl;

    public string $tokenCacheFile;

    public int $timeout;

    public function __construct(
        string $partnerId,
        string $authorizeUrl,
        string $appBasicAuth,
        string $apiBaseUrl,
        string $tokenCacheFile,
        int    $timeout = 10
    ) {
        $this->partnerId = $partnerId;
        $this->authorizeUrl = $authorizeUrl;
        $this->appBasicAuth = $appBasicAuth;
        $this->apiBaseUrl = $apiBaseUrl;
        $this->tokenCacheFile = $tokenCacheFile;
        $this->timeout = $timeout;
    }
}