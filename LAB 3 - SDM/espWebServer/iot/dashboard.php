<?php
require("IOTNode.php");
$iotNode = new IOTNode();
//  get telemetry data
$limit = "20";
$type  = null;
if (!empty($_REQUEST["limit"])  &&  ctype_digit($_REQUEST["limit"])) {  // is integer
    $limit = $_REQUEST["limit"];
}
if (!empty($_REQUEST["type"])) {  // type of value: temperature or pressure
    $type =  $_REQUEST['type'];
}
$temperatureData = $iotNode->getTelemetry($limit, "temperature");
$humidityData = $iotNode->getTelemetry($limit, "humidity");

// 
$ac_state = $iotNode->actuatorGetState("1");
$dehumidifier_state = $iotNode->actuatorGetState("2");
$system_state = $iotNode->systemGetState("1");

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <div style="margin-left: 50px;">
            <h1>Dashboard</h1>
            <h2>System</h2>
            <div style="display:flex; flex-direction:row; margin-bottom:20px;">
                <p id="currentSystemStateID" style="margin-right: 10px;">Current State:<b> <?php echo $system_state; ?></b> </p>
                <button type="button" class="btn btn-success" onclick="systemButtonClick();">Switch State</button>
            </div>
            <h2>Actuators</h2>
            <div style="display:flex; flex-direction:row; margin-bottom:20px;">
                <p id="currentStateID" style="margin-right: 10px;">Current State:<b> <?php echo $ac_state; ?></b> </p>
                <button type="button" class="btn btn-success" onclick="acButtonClick();">Switch State</button>
            </div>
            <h2>Dehumidifier</h2>
            <div style="display:flex; flex-direction:row; margin-bottom:20px;">
                <p id="currentDehumidifierStateID" style="margin-right: 10px;">Current State:<b> <?php echo $dehumidifier_state; ?></b> </p>
                <button type="button" class="btn btn-success" onclick="dehumidifierButtonClick();">Switch State</button>
            </div>
            <h2>Temperature Data</h2>
            <div style="width:70%; margin-left: 10%; margin-bottom:10px;">
                <canvas id="temperature-chart" style="border:1px solid;"></canvas>
            </div>
            <table class="table table-success table-striped table-hover" style="width:70%; margin-left: 10%;">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Time Created</th>
                        <th scope="col">Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($temperatureData as $row) {
                        echo '<tr>';
                        echo '<th scope="col">' . $row["id"] .              '</th>';
                        echo '<th scope="col">' . $row["time_created"] .    '</th>';
                        echo '<th scope="col">' . $row["data_type"]     .   '</th>';
                        echo '<th scope="col">' . $row["data_value"] .      '</th>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
            <h2>Humidity Data</h2>
            <div style="width:70%; margin-left: 10%; margin-bottom:10px;">
                <canvas id="humidity-chart" style="border:1px solid;"></canvas>
            </div>
            <table class="table table-success table-striped table-hover" style="width:70%; margin-left: 10%;">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Time Created</th>
                        <th scope="col">Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($humidityData as $row) {
                        echo '<tr>';
                        echo '<th scope="col">' . $row["id"] . '</th>';
                        echo '<th scope="col">' . $row["time_created"] . '</th>';
                        echo '<th scope="col">' . $row["data_type"]     .   '</th>';
                        echo '<th scope="col">' . $row["data_value"] . '</th>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        $system_state = "<?= $system_state ?>"; //<<-- server side code.
        function systemButtonClick() {
            if ($system_state == "on") {
                $system_state = "off";
            } else {
                $system_state = "on";
            }
            $.post("system_change.php", {
                system_id: "1",
                new_state: $system_state
            }, function(data) {
                console.log(data);
                $("#currentSystemStateID").html("Current State: <b>" + data + "</b>");
            });
        }
        $ac_state = "<?= $ac_state ?>"; //<<-- server side code.
        function acButtonClick() {
            if ($ac_state == "on") {
                $ac_state = "off"
            } else {
                $ac_state = "on"
            }
            $.post("actuator_change.php", {
                actuator_id: "1",
                new_state: $ac_state
            }, function(data) {
                console.log(data);
                $("#currentStateID").html("Current State: <b>" + data + "</b>");
            });
        }
        $dehumidifier_state = "<?= $dehumidifier_state ?>"; //<<-- server side code.
        function dehumidifierButtonClick() {
            if ($dehumidifier_state == "on") {
                $dehumidifier_state = "off";
            } else {
                $dehumidifier_state = "on";
            }
            $.post("actuator_change.php", {
                actuator_id: "2", // Assuming the dehumidifier's actuator ID is 2
                new_state: $dehumidifier_state
            }, function(data) {
                console.log(data);
                $("#currentDehumidifierStateID").html("Current State: <b>" + data + "</b>");
            });
        }
        new Chart(document.getElementById("temperature-chart"), {
            type: 'line',
            data: {
                labels: [
                    <?php
                    $firstTime = true;
                    foreach (array_reverse($temperatureData) as $row) {
                        if (!$firstTime) {
                            echo ',';
                        }
                        $time = strtotime($row["time_created"]);
                        echo '"' . date('h:i:s M d', $time) . '"';
                        $firstTime = false;
                    }
                    ?>
                ],
                datasets: [{
                    data: [
                        <?php
                        $firstTime = true;
                        foreach (array_reverse($temperatureData) as $row) {
                            if (!$firstTime) {
                                echo ',';
                            }
                            echo $row["data_value"];
                            $firstTime = false;
                        }
                        ?>
                    ],
                    label: "Temperature",
                    borderColor: "#3cba9f",
                    fill: false
                }]
            },
            options: {
                title: {
                    display: true,
                    text: 'Temperature Sensor values'
                }
            }
        });

        new Chart(document.getElementById("humidity-chart"), {
            type: 'line',
            data: {
                labels: [
                    <?php
                    $firstTime = true;
                    foreach (array_reverse($humidityData) as $row) {
                        if (!$firstTime) {
                            echo ',';
                        }
                        $time = strtotime($row["time_created"]);
                        echo '"' . date('h:i:s', $time) . '"';
                        $firstTime = false;
                    }
                    ?>
                ],
                datasets: [{
                    data: [
                        <?php
                        $firstTime = true;
                        foreach (array_reverse($humidityData) as $row) {
                            if (!$firstTime) {
                                echo ',';
                            }
                            echo '"' . $row["data_value"] . '"';
                            $firstTime = false;
                        }
                        ?>
                    ],
                    label: "Humidity",
                    borderColor: "#3cba9f",
                    fill: false
                }]
            },
            options: {
                title: {
                    display: true,
                    text: 'Humidity Sensor values'
                }
            }
        });
    </script>
</body>
</html>

