<?php
require_once 'medoo.php';
require_once 'conn.php';

function register_user($nik, $name, $loker, $chatid)
{
    global $database;
    $user = $database->insert("user", [
        "nik" => $nik,
        "name" => $name,
        "loker" => $loker
    ]);
    $database->insert("user_telegram", [
        "id_user" => $user,
        "id_telegram" => $chatid
    ]);
}

function unbind($chatid)
{
    global $database;
    $database->delete('user_telegram', [
        'id_telegram' => $chatid
    ]);

    return true;
}

function bind($id_user, $chatid)
{
    global $database;
    $bind = $database->insert("user_telegram", [
        "id_user" => $id_user,
        "id_telegram" => $chatid
    ]);
    return $bind;
}

function get_user_by_nik($nik)
{
    global $database;
    $user = $database->select('user',
        [ 'id_user', 'name', 'loker', 'nik' ],
        [ 'nik' => $nik ]
    );
    return $user;

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
        'user.loker',
        'user.id_user'
    ], [
        'id_telegram' => $chatid,
    ]);
    return $user;
}

function get_event_by_id($id_event) {
    global $database;
    $event = $database->select('event',
        [ 'id_event', 'event_name', 'event_venue' ],
        [ 'id_event' => $id_event ]
    );
    return $event;
}

function get_invitations($id_user, $id_event){
    global $database;
    $invitations = $database->select('event_user',
        'id_user',
        [ 'id_user' => $id_user, 'id_event' => $id_event ]
    );
    if (!empty($invitations)) {
        return true;
    } else {
        return false;
    }
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

