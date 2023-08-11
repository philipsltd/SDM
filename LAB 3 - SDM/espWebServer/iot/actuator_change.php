<?php

require("IOTNode.php");

$iotNode = new IOTNode();

if (!empty($_REQUEST["actuator_id"]) && !empty($_REQUEST["new_state"])) {
    // we should sanitize first, for avoiding SQL injection    
    $actuator_id = '';
    $actuator_id = filter_var($_REQUEST["actuator_id"], FILTER_SANITIZE_NUMBER_INT);
    $new_state   = filter_var($_REQUEST["new_state"], FILTER_SANITIZE_SPECIAL_CHARS);
    
    if ($actuator_id != false) { // if not a number, returns false
        $result = $iotNode->actuatorChange($actuator_id, $new_state);
        echo $result;
    }
}

