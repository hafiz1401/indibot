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
$_SESSION['state'] = '';
$_SESSION['state_input'] = '';

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
        } else {
            // gak di proses silakan dikembangkan sendiri
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
            //sendApiAction($chatid);
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
            //sendApiAction($chatid);
            $nik = $_SESSION['nik'];
            $iduser = get_user_by($nik,'nik');
            bind($iduser,$chatid);
            $text = "Unbind berhasil.";
            $inkeyboard = [
                [
                    ['text' => 'Daftar', 'callback_data' => 'daftar'],
                ],
            ];
            sendApiKeyboard($chatid, $text, $inkeyboard, true);
            break;

        case $pesan == 'unbind':
            //sendApiAction($chatid);
            unbind($chatid);
            $text = "Unbind berhasil.";
            $inkeyboard = [
                [
                    ['text' => 'Daftar', 'callback_data' => 'daftar'],
                ],
            ];
            sendApiKeyboard($chatid, $text, $inkeyboard, true);
            break;

        case $pesan == 'daftar':
            //sendApiAction($chatid);
            //$user = get_user_telegram($chatid);
            $text = "Masukkan NIK anda:";
            sendApiMsg($chatid, $text);
            $_SESSION['state'] = 'daftar';
            $_SESSION['state_input'] = 'daftar';
            break;

        case $pesan == 'aaaaa':
            //sendApiAction($chatid);
            $text = "A";
            $inkeyboard = [
                [
                    ['text' => 'Unbind', 'callback_data' => 'unbind'],
                ],
            ];
            sendApiKeyboard($chatid, $text, $inkeyboard, true);
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
    }else{
        if(get_user_telegram($chatid)){
        
        switch (true) {

        case $pesan == '/id':
            //sendApiAction($chatid);
            $text = 'ID Kamu adalah: '.$fromid;
            sendApiMsg($chatid, $text);
            break;

        case $pesan == '!keyboard':
            //sendApiAction($chatid);
            $keyboard = [
                ['tombol 1', 'tombol 2'],
                ['!keyboard', '!inline'],
                ['!hide'],
            ];
            sendApiKeyboard($chatid, 'tombol pilihan', $keyboard);
            break;

        

        case $pesan == '!hide':
            //sendApiAction($chatid);
            sendApiHideKeyboard($chatid, 'keyboard off');
            break;

        // case preg_match("/\/echo (.*)/", $pesan, $hasil):
        //     //sendApiAction($chatid);

        //     $text = '*Echo:* '.$hasil[1];
        //     sendApiMsg($chatid, $text, false, 'Markdown');
        //     break;

        default:
            //sendApiAction($chatid);
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
    }else{
        switch ($_SESSION['state']) {
            
                
            case 'daftar':
                switch ($_SESSION['state_input']) {
                    case 'daftar':
                        //sendApiAction($chatid);
                        $_SESSION['daftar_nik'] = $pesan;
                        $text = "Masukkan nama anda:";
                        sendApiMsg($chatid,$text);
                        $_SESSION['state_input'] = "nik";
                        break;
                    case 'nik':
                        //sendApiAction($chatid);
                        $_SESSION['daftar_nama'] = $pesan;
                        $text = "Masukkan loker anda:";
                        sendApiMsg($chatid,$text);
                        $_SESSION['state_input'] = "nama";
                        break;
                    case 'nama':
                        //sendApiAction($chatid);
                        $_SESSION['daftar_loker'] = $pesan;
                        $text = "NIK------: {$_SESSION['daftar_nik']}\nNama---: {$_SESSION['daftar_nama']}\nLoker---: {$_SESSION['daftar_loker']}\nApakah data diatas sudah betul?\nKetik "."*ya*/*tidak*";
                        sendApiMsg($chatid,$text, false, 'Markdown');
                        $_SESSION['state_input'] = "loker";
                        break;
                    case 'loker':
                        switch ($pesan) {
                            case 'ya':
                                $text = "yaa";
                                sendApiMsg($chatid,$text);
                                break;
                            case 'tidak':
                                ////sendApiAction($chatid);
                                $user = get_user_telegram($chatid);
                                $text = "Masukkan NIK anda:";
                                sendApiMsg($chatid, $text);
                                unset($_SESSION['daftar_nik']);
                                unset($_SESSION['daftar_nama']);
                                unset($_SESSION['daftar_loker']);
                                print_r($_SESSION);
                                $_SESSION['state'] = 'daftar';
                                $_SESSION['state_input'] = 'daftar';
                                break;
                            default:
                                $text = "Ketik *ya* untuk daftar\nKetik *tidak* untuk input ulang";
                                sendApiMsg($chatid,$text, false, 'Markdown');
                                break;
                        }
                        # code...
                        break;
                    
                    default:
                        # code...
                        break;
                }
                # code...
                break;
            
            default:
                switch (true) {

                case $pesan == '/id':
                    //sendApiAction($chatid);
                    $text = 'ID Kamu adalah: '.$fromid;
                    sendApiMsg($chatid, $text);
                    break;
                default:
                    //sendApiAction($chatid);
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
