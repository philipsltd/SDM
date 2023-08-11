<?php

require("IOTNode.php");

$iotNode = new IOTNode();

if (!empty($_REQUEST["system_id"])) {
    // we should sanitize first, for avoiding SQL injection    
    $system_id = '';
    $system_id = filter_var($_REQUEST["system_id"], FILTER_SANITIZE_NUMBER_INT);
    if ($system_id != false) { // if not a number, returns false
        $result = $iotNode->systemGetState($system_id);
        echo $result;
    }
}

