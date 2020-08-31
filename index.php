<?php 

require __DIR__ ."/vendor/autoload.php";

use DataParser\Handler;

define("DATA_PARSE_CONFIG", __DIR__ ."/config.php");

$a = new Handler;
$b = $a->test(["tes_name"=> 'hehe']);
print_r($b);die;