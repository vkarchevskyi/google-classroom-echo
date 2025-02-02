<?php

declare(strict_types=1);

namespace App\Services\Classroom;

use Google\Client;
use Symfony\Component\Filesystem\Filesystem;

readonly class GoogleAuthService
{
    public function __construct(private Filesystem $filesystem, private string $tokenPath)
    {
    }

    public function run(Client $client, ?string $code = null): Client|string
    {
        if ($this->filesystem->exists($this->tokenPath)) {
            $accessToken = $this->filesystem->readFile($this->tokenPath);
            $client->setAccessToken($accessToken);
        }

        if (!$client->isAccessTokenExpired()) {
            return $client;
        }

        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            if (!$code) {
                return $client->createAuthUrl();
            }

            $accessToken = $client->fetchAccessTokenWithAuthCode($code);
            $client->setAccessToken($accessToken);
        }

        $this->filesystem->appendToFile($this->tokenPath, json_encode($client->getAccessToken()), true);

        return $client;
    }
}