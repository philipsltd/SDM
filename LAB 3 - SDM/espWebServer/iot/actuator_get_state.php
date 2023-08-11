<?php

require("IOTNode.php");

$iotNode = new IOTNode();

if (!empty($_REQUEST["actuator_id"])) {
    // we should sanitize first, for avoiding SQL injection    
    $actuator_id = '';
    $actuator_id = filter_var($_REQUEST["actuator_id"], FILTER_SANITIZE_NUMBER_INT);
    if ($actuator_id != false) { // if not a number, returns false
        $result = $iotNode->actuatorGetState($actuator_id);
        echo $result;
    }
}

