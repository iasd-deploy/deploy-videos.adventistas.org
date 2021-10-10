<?php

for ($i = 1; $i <= 1000; $i++) { }
$data["Server_IP"] = $_SERVER['SERVER_ADDR'];
$data["Host"] = $_SERVER['SERVER_NAME'];
$data["Time"] = (microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]);
$data["Status"] = "ok";
print_r($data);

?>
