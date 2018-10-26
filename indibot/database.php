<?php
require_once 'medoo.php';
require_once 'Connection/connection.php';

if ($database == true) {
    echo "true";
} else {
    echo "false";
}

function insert_order($id_list_order,$order_client,$status_order,$keterangan_client)
{
    global $database;
    $last_id = $database->insert('order', [
        'id_list_order'    => $id_list_order,
        'order_client' => $order_client,
        'status_order' => $status_order,
        'keterangan_client' => $keterangan_client,
    ]);
    $kode_list_order = $database->get('list_order', 'kode_list_order', [
        'id_list_order' => $id_list_order,
    ]);
    $kode_order = $kode_list_order.$last_id;
    $database->update('order', [
        'kode_order'    => $kode_order,
    ],[
        'id_order'    => $last_id,
    ]);


    return $last_id;
}

function get_user_telegram($chatid)
{
    global $database;
    $user = $database->select('user_telegram', 
     [
        '[>]user' => ['id_user' => 'id_user']
    ], [
        'user.nik',
        'user.name',
        'user.loker'
    ],
        [
        'id_telegram' => $chatid,
    ]);
    return $user;
}

function unbind($chatid)
{
    global $database;
    $database->delete('user_telegram', [
        'id_telegram' => $chatid
    ]);

    return true;
}
