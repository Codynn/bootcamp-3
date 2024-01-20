<?php

function fetch_current_data($symbol){
    try {
        $url = 'https://finnhub.io/api/v1/quote?symbol='.$symbol.'&token=cmjv6j1r01qi6gquic0gcmjv6j1r01qi6gquic10';
        $dataString = file_get_contents($url);

        $data = json_decode($dataString,true);
        return $data;
    } catch (Exception $th) {
        return null;
    }
}


?>