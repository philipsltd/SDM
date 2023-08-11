<?php

require("IOTNode.php");

$iotNode = new IOTNode();

$result = $iotNode->telemetryInsert();

echo $result;

