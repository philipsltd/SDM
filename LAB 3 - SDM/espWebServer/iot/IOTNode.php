<?php

require("PDOConnection.php");

class IOTNode
{
    private $pdo;
    function __construct()
    {
        $this->pdo = new PDOConnection();
    }
    public function telemetryInsert()
    {
        $telemetry = array();
        if (!empty($_REQUEST["temperature"])) {
            // we should sanitize first, for avoiding SQL injection    
            //$telemetry["temperature"] = filter_var($_REQUEST["temperature"], FILTER_SANITIZE_NUMBER_FLOAT);
            $telemetry["temperature"] = $_REQUEST["temperature"];
        }
        if (!empty($_REQUEST["humidity"])) {
            //$telemetry["humidity"] = filter_var($_REQUEST["humidity"], FILTER_SANITIZE_NUMBER_FLOAT);    
            $telemetry["humidity"] = $_REQUEST["humidity"];
        }
        try {
            // prepare and bind
            $query_string = "INSERT INTO telemetry (data_type, data_value) VALUES (:data_type, :data_value)";
            if (!empty($telemetry["temperature"])) {
                $statement = $this->pdo->prepare($query_string);
                $statement->execute(array(
                    //"time_created"  => $telemetry["time_created"] ,
                    "data_type"     => "temperature",
                    "data_value"    => $telemetry["temperature"]
                ));
            }
            if (!empty($telemetry["humidity"])) {
                $statement = $this->pdo->prepare($query_string);
                $statement->execute(array(
                    //"time_created"  => $telemetry["time_created"] ,
                    "data_type"     => "humidity",
                    "data_value"    => $telemetry["humidity"]
                ));
            }
            return "ok";
        } catch (Exception $e) {
            return  "error inserting telmetry: " . $e->getMessage();
        }
    }
    public function getTelemetry($limit, $type=null)
    {
        $where = '';
        if (!empty($type)) {  // type of value: temperature or pressure
            $where = ' where data_type = "' . $type . '" ';
        }
        try {
            $query_string = 'select * from telemetry ' . $where . ' order by time_created desc limit ' . $limit;
            $statement = $this->pdo->query($query_string,  PDO::FETCH_ASSOC);
            $data = $statement->fetchAll();
            return $data;
        } catch (Exception $e) {
            echo  "error inserting telmetry: " . $e->getMessage();
            return null;
        }
    }
    public function actuatorChange($actuator_id, $new_state)
    {
        try {
            // get the latest state
            $query_string = 'select * from actuators_values where actuator_id ="' . $actuator_id . '" order by time_created desc limit 1';
            $statement = $this->pdo->query($query_string,  PDO::FETCH_ASSOC);
            $current_state = $statement->fetchAll();
            //print_r($current_state);
            $old_state = 'off';  // the default state
            if (count($current_state) > 0) { // has a previous state
                $old_state   = $current_state[0]["state_value"];
            }
            if ($new_state != $old_state) { // there is a new state
                $query_string = "INSERT INTO actuators_values (actuator_id, state_value) VALUES (:actuator_id, :state_value)";
                $statement = $this->pdo->prepare($query_string);
                $statement->execute(array(
                    "actuator_id"   => $actuator_id,
                    "state_value"   => $new_state
                ));
            }
            return $new_state;
        } catch (Exception $e) {
            return "error inserting new actuator_state: " . $e->getMessage();
        }
    }
    public function actuatorGetState($actuator_id)
    {
        try {
            // get the latest state
            $query_string = 'select * from actuators_values where actuator_id ="' . $actuator_id . '" order by time_created desc limit 1';
            $statement = $this->pdo->query($query_string,  PDO::FETCH_ASSOC);
            $current_state = $statement->fetchAll();
            //print_r($current_state);
            $state_value = 'off';           // the default state when the SQL table is empty
            if (count($current_state) > 0) { // has a previous state
                $state_value = $current_state[0]["state_value"];
            }
            return $state_value;
        } catch (Exception $e) {
            echo  "error inserting new actuator_state: " . $e->getMessage();
        }
    }
    public function systemGetState($system_id)
    {
        try {
            // get the latest state
            $query_string = 'select * from system_values where system_id ="' . $system_id . '" order by time_created desc limit 1';
            $statement = $this->pdo->query($query_string,  PDO::FETCH_ASSOC);
            $current_state = $statement->fetchAll();
            //print_r($current_state);
            $state_value = 'off';           // the default state when the SQL table is empty
            if (count($current_state) > 0) { // has a previous state
                $state_value = $current_state[0]["state_value"];
            }
            return $state_value;
        } catch (Exception $e) {
            echo  "error inserting new system_state: " . $e->getMessage();
        }
    }
    public function systemChange($system_id, $new_state)
    {
        try {
            // get the latest state
            $query_string = 'select * from system_values where system_id ="' . $system_id . '" order by time_created desc limit 1';
            $statement = $this->pdo->query($query_string,  PDO::FETCH_ASSOC);
            $current_state = $statement->fetchAll();
            //print_r($current_state);
            $old_state = 'off';  // the default state
            if (count($current_state) > 0) { // has a previous state
                $old_state   = $current_state[0]["state_value"];
            }
            if ($new_state != $old_state) { // there is a new state
                $query_string = "INSERT INTO system_values (system_id, state_value) VALUES (:system_id, :state_value)";
                $statement = $this->pdo->prepare($query_string);
                $statement->execute(array(
                    "system_id"   => $system_id,
                    "state_value"   => $new_state
                ));
            }
            return $new_state;
        } catch (Exception $e) {
            return "error inserting new system_state: " . $e->getMessage();
        }
    }
}

