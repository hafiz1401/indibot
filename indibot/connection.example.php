<?php
//copy isi file ini ke Connection/connection.php
    $database = new medoo([
        'database_type' => 'mysql',
        'database_name' => 'indi_banua',
        'server' => 'localhost',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8'
    ]);
?>
