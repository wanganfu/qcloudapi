<?php
declare(strict_types=1);

namespace annon;

/**
 * @property string $secretId 密钥ID
 * @property string $secretKey 密钥
 * @property string $service 指定要查询的服务
 * @property string $region 查询区域（可不填）
 * Class QCloudAPI
 * @package annon
 */
class QCloudAPI
{
    private $url = "";
    private $host = "";
    private $action = "";
    private $data = [];
    private $version = "2017-03-12";
    private $region = "";
    private $service = "";
    private $secretKey = "";
    private $secretId = "";

    /**
     * QCloudAPI constructor.
     * @param string $secretId
     * @param string $secretKey
     * @param string $service
     * @param string $region
     */
    public function __construct(string $secretId, string $secretKey, string $service, string $region = "")
    {
        $this->service = $service;
        $this->host = "$service.tencentcloudapi.com";
        $this->url = "https://{$this->host}";
        $this->region = $region;
        $this->secretId = $secretId;
        $this->secretKey = $secretKey;
    }

    /**
     * 设置查询方法
     * @param string $name
     * @return $this
     */
    public function action(string $name): QCloudAPI
    {
        $this->action = $name;
        return $this;
    }

    /**
     * 设置查询内容
     * @param array $data
     * @return $this
     */
    public function data(array $data): QCloudAPI
    {
        $this->data = $data;
        return $this;
    }

    /**
     * 执行查询操作
     * @return array
     */
    public function run(): array
    {
        $response = $this->send($this->url, $this->requestPayload(), $this->makeHeader(), 30);
        return json_decode($response, true);
    }

    /**
     * make curl header
     * @return array
     */
    private function makeHeader(): array
    {
        $timestamp = time();
        $header = [
            "Authorization" => $this->authorization($timestamp),
            "Content-Type" => "application/json; charset=utf-8",
            "Host" => $this->host,
            "X-TC-Action" => $this->action,
            "X-TC-Timestamp" => $timestamp,
            "X-TC-Version" => $this->version,
        ];
        if (!empty($this->region)) {
            $header["X-TC-Region"] = $this->region;
        }
        return $header;
    }

    /**
     * build canonical request string
     * @return string
     */
    private function buildRequest(): string
    {
        $canonicalHeaders = "content-type:application/json; charset=utf-8\nhost:{$this->host}\n";
        $hashedRequestPayload = $this->requestPayload();
        return "POST\n/\n\n$canonicalHeaders\ncontent-type;host\n$hashedRequestPayload";
    }

    /**
     * build string to sign
     * @param string $date
     * @return string
     */
    private function buildStr(string $date): string
    {
        $credentialScope = "$date/{$this->service}/tc3_request";
        $hashedCanonicalRequest = hash("SHA256", $this->buildRequest());
        return "TC3-HMAC-SHA256\n$date\n$credentialScope\n$hashedCanonicalRequest";
    }

    /**
     * sign string
     * @param string $date
     * @return string
     */
    private function signStr(string $date): string
    {
        $secretDate = hash_hmac("SHA256", $date, "TC3{$this->secretKey}", true);
        $secretService = hash_hmac("SHA256", $this->service, $secretDate, true);
        $secretSigning = hash_hmac("SHA256", "tc3_request", $secretService, true);
        return hash_hmac("SHA256", $this->buildStr($date), $secretSigning);
    }

    /**
     * build authorization
     * @param int $timestamp
     * @return string
     */
    private function authorization(int $timestamp): string
    {
        $date = gmdate("Y-m-d", $timestamp);
        $credentialScope = "$date/{$this->service}/tc3_request";
        return "TC3-HMAC-SHA256 Credential={$this->secretId}/$credentialScope, SignedHeaders=content-type;host, Signature={$this->signStr($date)}";
    }

    private function requestPayload(): string
    {
        return hash("SHA256", json_encode($this->data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param string $url
     * @param string $data
     * @param array $header
     * @param int $timeout
     * @return string
     */
    private function send(string $url, string $data, array $header = [], int $timeout = 30): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}