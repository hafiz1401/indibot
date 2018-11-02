<?php

define('HA', true);


require_once 'bot-api-config.php';
require_once 'bot-api-fungsi.php';

require_once 'bot-api-proses.php';
require_once 'database.php';


function myloop()
{
    global $debug;

    $idfile = 'botposesid.txt';
    $update_id = 0;

    if (file_exists($idfile)) {
        $update_id = (int) file_get_contents($idfile);
        echo '-';
    }
    $updates = getApiUpdate($update_id);

    foreach ($updates as $message) {
        $update_id = prosesApiMessage($message);
        echo '+';
    }
    file_put_contents($idfile, $update_id + 1);
}
date_default_timezone_set('Asia/singapore');
$now = date("Y-m-d H:i:s");
while (true) {
    if ($now == strtotime('2018-10-30 09:25:00')){
        sendApiMsg('453727557', 'hello brow');
    }
    myloop();
}