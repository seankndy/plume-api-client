<?php

namespace SeanKndy\PlumeApi;

class AccessTokenCache
{
    /**
     * @var resource
     */
    private $handle;

    public function __construct(string $tokenCacheFile)
    {
        if (!($this->handle = @fopen($tokenCacheFile, 'c+b'))) {
            throw new \RuntimeException("Unable to open token cache file '{$tokenCacheFile}'.");
        }
    }

    public function __destruct()
    {
        fclose($this->handle);
    }

    public function get(): ?string
    {
        rewind($this->handle);
        $data = stream_get_contents($this->handle);

        if (!$data || !($data = @json_decode($data, true)) || !isset($data['access_token'], $data['expires_at'])) {
            return null;
        }

        if (time() >= $data['expires_at']) {
            return null;
        }

        return $data['access_token'];
    }

    public function save(string $accessToken, int $expiresIn): void
    {
        ftruncate($this->handle, 0);
        fwrite($this->handle, json_encode([
            'access_token' => $accessToken,
            'expires_at' => time()+$expiresIn,
        ]));
    }
}