<?php
namespace app\service;

use Predis\Client;

class RedisService
{
    private ?Client $client = null;

    public function client(): Client
    {
        if ($this->client) {
            return $this->client;
        }

        $config = config('redis');
        $this->client = new Client([
            'scheme' => 'tcp',
            'host' => $config['host'],
            'port' => $config['port'],
            'password' => $config['password'] ?: null,
            'database' => $config['select'],
            'timeout' => $config['timeout'],
        ], ['prefix' => $config['prefix']]);

        return $this->client;
    }
}

