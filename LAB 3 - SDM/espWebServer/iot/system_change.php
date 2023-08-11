<?php

require("IOTNode.php");

$iotNode = new IOTNode();

if (!empty($_REQUEST["system_id"]) && !empty($_REQUEST["new_state"])) {
    // we should sanitize first, for avoiding SQL injection    
    $system_id = '';
    $system_id = filter_var($_REQUEST["system_id"], FILTER_SANITIZE_NUMBER_INT);
    $new_state   = filter_var($_REQUEST["new_state"], FILTER_SANITIZE_SPECIAL_CHARS);
    
    if ($system_id != false) { // if not a number, returns false
        $result = $iotNode->systemChange($system_id, $new_state);
        echo $result;
    }
}