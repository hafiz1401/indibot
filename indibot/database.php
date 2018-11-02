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
        [ 'id_event', 'event_name', 'event_venue', 'event_start', 'event_end', 'bot_absen', 'radius', 'id_lokasi' ],
        [ 'id_event' => $id_event ]
    );
    return $event;
}

function get_lokasi($id_lokasi) {
    global $database;
    $lokasi = $database->select('lokasi',
        [ 'latitude', 'longitude' ],
        [ 'id_lokasi' => $id_lokasi]
    );
    return $lokasi;
}

function is_invitation($id_user, $id_event){
    global $database;
    $invitation = $database->select('event_user',
        [ 'id_user', 'id_event'] ,
        [ "AND" => ['id_user' => $id_user, 'id_event' => $id_event ] ]
    );
    return (!empty($invitation)) ? true : false;
}

function insert_participant($id_user, $id_event, $status)
{
    global $database;
    $participant = $database->insert("event_participant", [
        "id_user" => $id_user,
        "id_event" => $id_event,
        "status" => $status
    ]);
}

function insert_participant_guest($id_event, $nik, $name, $instansi, $status)
{
    global $database;
    $guest = $database->insert('guest', [
        "name" => $name,
        "nik" => $nik,
        "instansi" => $instansi
    ]);

    $database->insert('event_participant', [
        "id_guest" => $guest,
        "id_event" => $id_event,
        "status" => $status
    ]);

}

function is_participant($id_user, $id_event){
    global $database;
    $participant = $database->select('event_participant',
        ['id_user', 'id_event'] ,
        [ "AND" => [ 'id_user' => $id_user, 'id_event' => $id_event ] ]
    );
    return (!empty($participant)) ? true : false;
}

function check_radius($user_lat, $user_long, $event_lat, $event_long)
{
    $earth_radius = 6371;

    $dLat = deg2rad( $event_lat - $user_lat );  
    $dLon = deg2rad( $event_long - $user_long );  

    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($user_lat)) * cos(deg2rad($event_lat)) * sin($dLon/2) * sin($dLon/2);  
    $c = 2 * asin(sqrt($a));  
    $d = $earth_radius * $c;  
    return $d;
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

