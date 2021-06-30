<?php

require_once __DIR__ . "/../vendor/autoload.php";

use annon\QCloudAPI;
use annon\Secret;
use annon\Option;

$secret = new Secret("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx", "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
$option = new Option("lighthouse", "2020-03-24", "ap-guangzhou");

$api = new QCloudAPI($secret, $option);

$data = $api->action("DescribeInstances")
    ->run();

var_dump($data);
