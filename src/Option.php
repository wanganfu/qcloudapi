<?php
declare(strict_types=1);

namespace annon;

/**
 * @property string $service 服务
 * @property string $version API版本
 * @property string $region 资源地区（部分接口可以不填）
 * Class Option
 * @package annon
 */
final class Option
{
    /**
     * @var string
     */
    public $service;

    /**
     * @var string
     */
    public $version;

    /**
     * @var string
     */
    public $region;

    public function __construct(string $service, string $version, string $region = "")
    {
        $this->service = $service;
        $this->version = $version;
        $this->region  = $region;
    }
}