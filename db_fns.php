<?php

function db_connect() {
    $result = new mysqli('localhost', 'td_user', 'password', 'trudesign');
    if (!$result) {
        throw new Exception('Could not connect to database server');
    } else {
        return $result;
    }
}

?>