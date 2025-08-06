<?php

namespace SeanKndy\PlumeApi;

class AccessTokenCache
{
    private string $tokenCacheFile;

    public function __construct(string $tokenCacheFile)
    {
        $this->tokenCacheFile = $tokenCacheFile;
    }

    public function get(): ?string
    {
        if (!is_readable($this->tokenCacheFile)) {
            return null;
        }

        if (!($data = @json_decode(file_get_contents($this->tokenCacheFile), true)) || !isset($data['access_token'], $data['expires_at'])) {
            return null;
        }

        if (time() >= $data['expires_at']) {
            return null;
        }

        return $data['access_token'];
    }

    public function save(string $accessToken, int $expiresIn): void
    {
        if (!($fp = @fopen($this->tokenCacheFile, 'w'))) {
            throw new \RuntimeException('Unable to open token cache file \''.$this->tokenCacheFile.'\' for writing');
        }

        fwrite($fp, json_encode([
            'access_token' => $accessToken,
            'expires_at' => time() + $expiresIn,
        ]));

        fclose($fp);
    }
}