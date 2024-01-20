<?php

include("database.php");
include("api_utils.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$connection = connect_database("localhost", "root", "", "stock");


if (isset($_GET["symbol"])) {
    $symbol = $_GET["symbol"];
} else {
    echo '{"error": "No symbol provided!"}';
}

$existingData = null;
$allData = get_stock_data($connection, $symbol);
if (count($allData) == 0){
    $existingData = null;
}else {
    $lastIndex = count($allData)-1;
    $existingData = $allData[$lastIndex];
}

if (isset($_GET["history"])) {
    
    echo json_encode($allData);

    exit;
}

$refreshTime = 60 * 60 * 1; // 1 sec
if ($existingData) {
    $dataTimeStamp = 0;
    if (isset($existingData["timestamp"])){
        $dataTimeStamp = $existingData["timestamp"];
    }
    $currentTime = time();
    if ($currentTime - $dataTimeStamp > $refreshTime) {
        $newData = fetch_current_data($symbol);
        if ($newData) {
            $result = insert_stock_data($connection, $symbol, $newData);
            if ($result) {
                $databaseFormatData = ["timestamp" => $newData["t"], "price" => $newData["o"], "symbol" => $symbol];
                echo json_encode($databaseFormatData);
            } else {
                echo '{"error": "Data could not be inserted!"}';
            }
        } else {
            echo '{"error": "Data could not be fetched!"}';
            exit;
        }
    } else {
        echo json_encode($existingData);
        exit;
    }
} else {
    $newData = fetch_current_data($symbol);
    if ($newData) {
        $result = insert_stock_data($connection, $symbol, $newData);
        if ($result) {
            $databaseFormatData = ["timestamp" => $newData["t"], "price" => $newData["o"], "symbol" => $symbol];
            echo json_encode($databaseFormatData);
        } else {
            echo '{"error": "Data could not be inserted!"}';
        }
    } else {
        echo '{"error": "Data could not be fetched!"}';
        exit;
    }
}



// $data = fetch_current_data("AAPL");
// insert_stock_data($connection,"AAPL",$data);
