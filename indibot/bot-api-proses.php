<?php

if (!defined('HA')) {
    die('Tidak boleh diakses langsung.');
}



/*

Contoh penggunaan :
~~~~~~~~~~~~~~~~~~~~~

Kirim Aksi
----------
(typing, upload_photo, record_video, upload_video, record_audio, upload_audio, upload_document, find_location) :

    //sendApiAction($chatid);
    //sendApiAction($chatid, 'upload_photo');


Kirim Pesan :
----------
    sendApiMsg($chatid, 'pesan');
    sendApiMsg($chatid, 'pesan *tebal*', false, 'Markdown');


Kirim Markup Keyboard :
----------
    $keyboard = [
        [ 'tombol 1', 'tombol 2' ],
        [ 'tombol 3', 'tombol 4' ],
        [ 'tombol 5' ]
    ];

    sendApiKeyboard($chatid, 'tombol pilihan', $keyboard);


Kirim Inline Keyboard
----------
    $inkeyboard = [
        [
            ['text'=>'tombol 1', 'callback_data' => 'data 1'],
            ['text'=>'tombol 2', 'callback_data' => 'data 2']
        ],
        [
            ['text'=>'tombol akhir', 'callback_data' => 'data akhir']
        ]
    ];

    sendApiKeyboard($chatid, 'tombol pilihan', $inkeyboard, true);


editMessageText
----------
    editMessageText($chatid, $message_id, $text, $inkeyboard, true);



Menyembunyikan keyboard :
----------
    sendApiHideKeyboard($chatid, 'keyboard off');


kirim sticker
----------

    sendApiSticker($chatid, 'BQADAgADUAADxKtoC8wBeZm11cjsAg')


Dan Lain-lain :-D
~~~~~~~~~~~~~~~~~~~~~

*/

session_start();
$_SESSION['state'] = array();
$_SESSION['state_input'] = array();
$_SESSION['guest'][$chatid] = false;

function prosesApiMessage($sumber)
{
    $updateid = $sumber['update_id'];

   // if ($GLOBALS['debug']) mypre($sumber);

    if (isset($sumber['message'])) {
        $message = $sumber['message'];

        if (isset($message['text'])) {
            prosesPesanTeks($message);
        } elseif (isset($message['sticker'])) {
            prosesPesanSticker($message);
        } elseif (isset($message['location'])) {
            prosesPesanLocation($message);
        }
    }

    if (isset($sumber['callback_query'])) {
        prosesCallBackQuery($sumber['callback_query']);
    }

    return $updateid;
}

function prosesPesanSticker($message)
{
    // if ($GLOBALS['debug']) mypre($message);
}

function prosesPesanLocation($message)
{
    $message['text'] = $message['location']['latitude'] . ';' . $message['location']['longitude'];
    prosesPesanTeks($message);
}

function prosesCallBackQuery($message)
{
    // if ($GLOBALS['debug']) mypre($message);

    $message_id = $message['message']['message_id'];
    $chatid = $message['message']['chat']['id'];
    $data = $message['data'];

    // $inkeyboard = [
    //             [
    //                 ['text' => 'Update 1', 'callback_data' => 'data update 1'],
    //                 ['text' => 'Update 2', 'callback_data' => 'data update 2'],
    //             ],
    //             [
    //                 ['text' => 'keyboard on', 'callback_data' => '!keyboard'],
    //                 ['text' => 'keyboard inline', 'callback_data' => '!inline'],
    //             ],
    //             [
    //                 ['text' => 'keyboard off', 'callback_data' => '!hide'],
    //             ],
    //         ];

    // $text = '*'.date('H:i:s').'* data baru : '.$data;

    // editMessageText($chatid, $message_id, $text, $inkeyboard, true);

    $messageupdate = $message['message'];
    $messageupdate['text'] = $data;

    prosesPesanTeks($messageupdate,$callback=true);
}


function prosesPesanTeks($message,$callback=false)
{
    // if ($GLOBALS['debug']) mypre($message);

    $pesan = $message['text'];
    $chatid = $message['chat']['id'];
    $fromid = $message['from']['id'];

    if($callback){
        switch (true) {

        case $pesan == 'lihatdata':
            $user = get_user_telegram($chatid);
            $text = "NIK------: ".$user[0]["nik"]."\nNama---: ".$user[0]["name"]."\nLoker---: ".$user[0]["loker"];
            $inkeyboard = [
                [
                    ['text' => 'Unbind', 'callback_data' => 'unbind'],
                    ['text' => 'Absen', 'callback_data' => 'absen'],
                    ['text' => 'Order', 'callback_data' => 'order'],
                ],
            ];
            sendApiKeyboard($chatid, $text, $inkeyboard, true);
            break;
        
        case $pesan == 'bind':
            $nik = $_SESSION['nik'][$chatid];
            $id_user = get_user_by_nik($nik);
            var_dump($nik)  ;

            bind($id_user[0]['id_user'],$chatid);
            $text = "Bind akun berhasil.";
            $inkeyboard = [
                [
                    ['text' => 'Unbind', 'callback_data' => 'unbind'],
                    ['text' => 'Absen', 'callback_data' => 'absen'],
                    ['text' => 'Order', 'callback_data' => 'order'],
                ],
            ];
            sendApiKeyboard($chatid, $text, $inkeyboard, true);
            break;

        case $pesan == 'unbind':
            unbind($chatid);
            $text = "Unbind berhasil.";
            $inkeyboard = [
                [
                    ['text' => 'Daftar', 'callback_data' => 'daftar'],
                ],
            ];
            sendApiKeyboard($chatid, $text, $inkeyboard, true);
            $_SESSION['state'][$chatid] = '';
            $_SESSION['state_input'][$chatid] = '';
            break;

        case $pesan == 'daftar':
            $text = "Masukkan NIK anda :";
            sendApiMsg($chatid, $text);
            $_SESSION['state'][$chatid] = 'daftar';
            $_SESSION['state_input'][$chatid] = 'daftar';
            break;

        case $pesan == 'absen':
            $text = "Masukkan kode event :";
            sendApiMsg($chatid, $text);
            $_SESSION['state'][$chatid] = 'absen';
            $_SESSION['state_input'][$chatid] = 'absen';
            break;



        default:
            // //sendApiAction($chatid);
            // $inkeyboard = [
            //     [
            //         ['text' => 'Lihat Data', 'callback_data' => 'lihatdata'],
            //         ['text' => 'Absen', 'callback_data' => 'absen'],
            //         ['text' => 'Order', 'callback_data' => 'order'],
            //     ],
            // ];
            // sendApiKeyboard($chatid, 'Silahkan kaka ...', $inkeyboard, true);
            sendApiMsg($chatid, '😎');
            break;
        }
    } else {
        if (get_user_telegram($chatid) || $_SESSION['guest'][$chatid] == true){
            switch (true) {
            case $pesan == '/id':
                $text = 'ID Kamu adalah: '.$fromid;
                sendApiMsg($chatid, $text);
                break;
            case $pesan == '!keyboard':
                $keyboard = [
                    ['tombol 1', 'tombol 2'],
                    ['!keyboard', '!inline'],
                    ['!hide'],
                ];
                sendApiKeyboard($chatid, 'tombol pilihan', $keyboard);
                break;
            case $pesan == '!hide':
                sendApiHideKeyboard($chatid, 'keyboard off');
                break;

            // case preg_match("/\/echo (.*)/", $pesan, $hasil):
            //     //sendApiAction($chatid);

            //     $text = '*Echo:* '.$hasil[1];
            //     sendApiMsg($chatid, $text, false, 'Markdown');
            //     break;

            case 'absen' :
                switch ($_SESSION['state_input'][$chatid]) {
                    case 'absen':
                        $user = get_user_telegram($chatid);
                        $current_user_input['kode_event'] = $pesan;
                        $event = get_event_by_id($current_user_input['kode_event']);
                        $is_include_invitations = is_invitation($user[0]['id_user'], $current_user_input['kode_event']);
                        date_default_timezone_set('Asia/singapore');
                        $now = date("Y-m-d H:i:s");
                        $is_ongoing_event = ($event[0]['event_start'] <= $now && $event[0]['event_end'] >= $now);
                        if (!empty($event)){
                            if ($is_include_invitations) {
                                if ($is_ongoing_event == true) {
                                    $text = $event[0]['event_name'] . "\n"
                                            . $event[0]['event_venue'] . "\n"
                                            . $event[0]['event_start'] . " "
                                            . "s/d "
                                            . $event[0]['event_end'] . " Wita"; 
                                    sendApiMsg($chatid, $text);
                                    if ($event[0]['bot_absen'] == 1) {
                                        if (!is_participant($user[0]['id_user'], $current_user_input['kode_event'])) {
                                            $keyboard = [
                                                    [
                                                        ['text' => 'Hadir', 'request_location' => true],
                                                        ['text' => 'Izin'],
                                                    ],
                                                ];
                                            sendApiKeyboard($chatid, 'Klik hadir', $keyboard);
                                            $event_loc = get_lokasi($event[0]['id_lokasi']);
                                            $_SESSION['id_user'][$chatid] = $user[0]['id_user'];
                                            $_SESSION['id_event'][$chatid] = $current_user_input['kode_event'];
                                            $_SESSION['radius'][$chatid] = $event[0]['radius'];
                                            $_SESSION['event_latitude'][$chatid] = $event_loc[0]['latitude'];
                                            $_SESSION['event_longitude'][$chatid] = $event_loc[0]['longitude'];
                                            $_SESSION['state_input'][$chatid] = 'proses_presensi';
                                        } else {
                                            sendApiMsg($chatid, 'Anda telah absen.');
                                            $_SESSION['state'][$chatid] = '';
                                            $_SESSION['state_input'][$chatid] = '';
                                        }
                                    } else {
                                        sendApiMsg($chatid, 'Tidak bisa absen lewat bot.');
                                    }
                                } else {
                                    sendApiMsg($chatid, 'Tidak bisa absen, perhatikan waktu acara.');
                                }
                            } else {
                                sendApiMsg($chatid, 'Maaf anda tidak termasuk dalam undangan.');
                            }
                        } else {
                            sendApiMsg($chatid, 'Event tidak dikenali.');
                        }
                        break;

                    case 'proses_presensi' :
                        var_dump($pesan);
                        var_dump('masuk');
                        print_r($_SESSION);
                        if (strtoupper($pesan) == 'IZIN') {
                            var_dump('masuk izin');
                            $status = "Izin";
                            if (isset($_SESSION['guest_kode_event'])) {
                                var_dump('mask izin query');
                                insert_participant_guest($_SESSION['guest_kode_event'], $_SESSION['guest_absen_nik'], $_SESSION['guest_absen_name'], $_SESSION['guest_absen_instansi'], $status);
                            } else {
                                insert_participant($_SESSION['id_user'][$chatid], $_SESSION['id_event'][$chatid], $status);
                            }
                            $text = "Anda berhasil izin.";
                        } elseif (count(explode(';', $pesan) == 2))  {
                            $lokasi = explode(';', $pesan);
                            $lat = $lokasi[0];
                            $long = $lokasi[1];
                            $radius = check_radius($lat, $long, $_SESSION['event_latitude'][$chatid], $_SESSION['event_longitude'][$chatid]);
                            if ($radius < $_SESSION['radius'][$chatid]) {
                                $status = "Hadir";
                                insert_participant($_SESSION['id_user'][$chatid], $_SESSION['id_event'][$chatid], $status);
                                $text = 'Behasil absen.';
                            } else {
                                $status = "Diluar lokasi";
                                insert_participant($_SESSION['id_user'][$chatid], $_SESSION['id_event'][$chatid], $status);
                                $text = 'Lokasi anda terlalu jauh dari event. Anda harus berada dalam radius ' . $_SESSION['radius'][$chatid] . ' km. Untuk saat ini data anda tersimpan dengan status Di Luar Lokasi.';
                            }
                        }
                        sendApiMsg($chatid, $text);
                        unset($_SESSION['id_user'][$chatid]);
                        unset($_SESSION['id_event'][$chatid]);
                        unset($_SESSION['radius'][$chatid]);
                        unset($_SESSION['event_latitude'][$chatid]);
                        unset($_SESSION['event_longitude'][$chatid]);
                        $_SESSION['state_input'][$chatid] = '';
                        $_SESSION['state'][$chatid] = '';
                        print_r($_SESSION);
                        break;
                    
                    default:
                        break;
                }

            default:
                $inkeyboard = [
                    [
                        ['text' => 'Lihat Data', 'callback_data' => 'lihatdata'],
                        ['text' => 'Absen', 'callback_data' => 'absen'],
                        ['text' => 'Order', 'callback_data' => 'order'],
                    ],
                ];
                sendApiKeyboard($chatid, 'Silahkan kaka ...', $inkeyboard, true);
                break;
            }
        } else {
            switch ($_SESSION['state'][$chatid]) {
                case 'daftar':
                    switch ($_SESSION['state_input'][$chatid]) {
                        case 'daftar':
                            $user = get_user_by_nik($pesan);
                            if (!empty($user)) {
                                $inkeyboard = [
                                    [
                                        ['text' => 'Bind', 'callback_data' => 'bind'],
                                    ],
                                ];
                                sendApiKeyboard($chatid, 'Akun anda telah terdaftar.' , $inkeyboard, true);
                                $_SESSION['nik'][$chatid] = $pesan;
                            } else {
                                $_SESSION['daftar_nik'][$chatid] = $pesan;
                                $text = "Masukkan nama anda : ";
                                sendApiMsg($chatid,$text);
                                $_SESSION['state_input'][$chatid] = "nik";
                            }
                            break;
                        case 'nik':
                            $_SESSION['daftar_nama'][$chatid] = $pesan;
                            $text = "Masukkan loker anda:";
                            sendApiMsg($chatid,$text);
                            $_SESSION['state_input'][$chatid] = "nama";
                            break;
                        case 'nama':
                            $_SESSION['daftar_loker'][$chatid] = $pesan;
                            $text = "NIK------: {$_SESSION['daftar_nik'][$chatid]}\nNama---: {$_SESSION['daftar_nama'][$chatid]}\nLoker---: {$_SESSION['daftar_loker'][$chatid]}\nApakah data diatas sudah betul?\nKetik "."*ya*/*tidak*";
                            sendApiMsg($chatid,$text, false, 'Markdown');
                            $_SESSION['state_input'][$chatid] = "loker";
                            break;
                        case 'loker':
                            switch ($pesan) {
                                case strtolower($pesan) == 'ya':
                                    register_user($_SESSION['daftar_nik'][$chatid], $_SESSION['daftar_nama'][$chatid], $_SESSION['daftar_loker'][$chatid], $chatid);
                                    $text = "Register berhasil.";
                                    sendApiMsg($chatid,$text);
                                    break;
                                case strtolower($pesan) == 'tidak':
                                    $user = get_user_telegram($chatid);
                                    $text = "Masukkan NIK anda:";
                                    sendApiMsg($chatid, $text);
                                    unset($_SESSION['daftar_nik'][$chatid]);
                                    unset($_SESSION['daftar_nama'][$chatid]);
                                    unset($_SESSION['daftar_loker'][$chatid]);
                                    print_r($_SESSION);
                                    $_SESSION['state'][$chatid] = 'daftar';
                                    $_SESSION['state_input'][$chatid] = 'daftar';
                                    break;
                                default:
                                    $text = "Ketik *ya* untuk daftar\nKetik *tidak* untuk input ulang";
                                    sendApiMsg($chatid,$text, false, 'Markdown');
                                    break;
                            }
                            break;
                        default:
                            break;
                    }
                    break;
                    
                case substr($pesan, 0, 5) == '/guest':
                    $kode_event = explode(' ', $pesan)[1];
                    $_SESSION['guest_kode_event'][$chatid] = $kode_event;
                    $text = "Masukkan nik anda :";
                    sendApiMsg($chatid, $text);
                    $_SESSION['state'][$chatid] = 'guest_absen';
                    $_SESSION['state_input'][$chatid] = 'guest_absen';
                    break;

                case 'guest_absen' :
                    switch($_SESSION['state_input'][$chatid]) {
                        case 'guest_absen' :
                            $_SESSION['guest_absen_nik'][$chatid] = $pesan;
                            $text = "Masukkan nama anda : ";
                            sendApiMsg($chatid,$text);
                            $_SESSION['state_input'][$chatid] = "guest_nik";
                            break;
                        case 'guest_nik' :
                            $_SESSION['guest_absen_name'][$chatid] = $pesan;
                            $text = "Masukkan asal instansi anda :";
                            sendApiMsg($chatid, $text);
                            $_SESSION['state_input'][$chatid] = "proses_guest";
                            break;
                        case 'proses_guest' :
                            $_SESSION['guest_absen_instansi'][$chatid] = $pesan;
                            $keyboard = [
                                [
                                    ['text' => 'Hadir', 'request_location' => true],
                                    ['text' => 'Izin'],
                                ],
                            ];
                            sendApiKeyboard($chatid, 'Klik hadir', $keyboard);
                            $_SESSION['guest'][$chatid] = true;
                            $_SESSION['state_input'][$chatid] = 'proses_presensi';
                            break;
                    }
                
                default:
                    switch (true) {

                    case $pesan == '/id':
                        $text = 'ID Kamu adalah: '.$fromid;
                        sendApiMsg($chatid, $text);
                        break;
                    default:
                        $inkeyboard = [
                            [
                                ['text' => 'Daftar', 'callback_data' => 'daftar'],
                            ],
                        ];
                        sendApiKeyboard($chatid, 'Silahkan daftar terlebih dahulu.', $inkeyboard, true);
                        break;
                    }
                    break;
            }
        }
    }
}
