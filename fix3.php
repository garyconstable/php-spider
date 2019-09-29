<?php

function d($data = [], $die = true)
{
    echo '<pre>' . print_r($data, true) . '</pre>';
    if ($die) {
        die();
    }
}

set_time_limit(0);

$con = mysqli_connect("localhost", "root", "Sarah2004!", "spider_v1");

//$limit = " limit 1000000";
$limit = "";

$db_table = 'domains';
$url_key = 'domain';
$id_key = 'id';

/*
$db_table = 'external_domain';
$url_key = 'url';
$id_key = 'id';
*/

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    die();
}

$sql = "SELECT * FROM ".$db_table." where visited = 0 order by id ".$limit;

if ($result = mysqli_query($con, $sql)) {
    $i = 0;
    while ($item = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $err = false;
        $url = $item[$url_key];
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);

        if (!curl_errno($ch)) {
            switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
                case 200:  # OK
                    break;
                default:
                    $err = true;
            }
        }

        curl_close($ch);

        if ($err) {
            mysqli_query($con, "delete from ".$db_table." where id = '" . $item[$id_key] . "' ");
        }

        /*
        d([
            $i,
            $url,
            $err ? '404 TRUE' : '404 FALSE'
        ], 0);
        */

        $i++;
    }
    mysqli_free_result($result);
}else{
    d('--> wtf..');
}

mysqli_close($con);