<?php

function connect_database($server, $username, $password, $db){
    $connection = null;
    try {
        $connection = new mysqli($server, $username, $password, $db);
        if ($connection -> connect_errno){
            echo '{"error": "Database connection failed!"}';
        }

        return $connection;
    } catch (Exception $th) {
        return null;
    }
}

function get_stock_data($connection, $stock_symbol){
    try {
        $result = $connection -> query('SELECT * FROM stocks WHERE symbol = "'.$stock_symbol.'" ORDER BY timestamp ASC ;');
        if ($result){
            $data = $result -> fetch_all(MYSQLI_ASSOC);
            return $data;
        } else {
            return null;
        }

    } catch (Exception $th) {
        return null;
    }
}


// This function was added after bootcamp. It checks if the stock data of the same symbol of same 
// timestamp already exists in the database
function get_stock_data_with_timestamp($connection, $stock_symbol, $timestamp){
    try {
        $result = $connection -> query('SELECT * FROM stocks WHERE symbol = "'.$stock_symbol.'" AND timestamp = '.$timestamp.';');
        if ($result){
            $data = $result -> fetch_all(MYSQLI_ASSOC);
            return $data;
        } else {
            return null;
        }

    } catch (Exception $th) {
        return null;
    }
}

function insert_stock_data($connection, $stock_symbol, $stock_data){
    try {
        $timestamp = $stock_data["t"];
        $openingPrice = $stock_data["o"];

        // Checking if the stock data timestamp and stock symbol is already present
        if (get_stock_data_with_timestamp($connection, $stock_symbol, $timestamp)){
            // if already exists return true
            return true;
        }

        $result = $connection -> query('INSERT INTO stocks VALUES (
            '.$timestamp.' , 
            "'.$stock_symbol.'",
            '.$openingPrice.'
        );');

        if ($result){
            return true;
        }else {
            return false;
        }

    } catch (Exception $th) {
        echo $th;
        return null;
    }
}


?>