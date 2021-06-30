<?php
declare(strict_types=1);

namespace annon;

/**
 * @property string $secretId 密钥ID
 * @property string $secretKey 密钥
 * Class Config
 * @package annon
 */
final class Secret
{
    /**
     * @var string
     */
    public $secretId;

    /**
     * @var string
     */
    public $secretKey;

    public function __construct(string $secretId, string $secretKey)
    {
        $this->secretId = $secretId;
        $this->secretKey = $secretKey;
    }
}