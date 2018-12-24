<?php
/**
 * Created by PhpStorm.
 * User: @SiGool
 * Date: 2018/12/20 0020
 * Time: 20:38
 */

require_once './lib/Mysqli_u.class.php';
require_once './config/DB.php';
require_once './config/msgReporter.php';

header('Content-Type: application/json');

if (!isset($_POST['reqFrom']) || $_POST['reqFrom'] !== 'sesameOl')
    _exit(1001);

function validate() {

    if (!isset($_POST['ticket']) || empty($_POST['ticket']))
        return 1201;

    if (!preg_match('/^(?!\s*$)[\S\s]{2,12}$/', $_POST['ticket']))
        return 1203;

    if (!isset($_POST['token']) || empty($_POST['token']))
        $_POST['token'] = '---'; // no token
    else if (!preg_match('/^\w{4,18}$/', $_POST['token']))
        return 1203;             // incorrect token format

    return true;
}

function _exit($msgCode) {

    exit(json_encode([
        'msgCode' => $msgCode,
        'msg' => MSG[$msgCode],
        'data' => []
    ]));
}


$mysqli = null;
$e_ticket = null;
function getFileInfo($ticket) {

    global $sesameOl_DB, $mysqli, $e_ticket;

    $mysqli = new Mysqli_u($sesameOl_DB['host'], $sesameOl_DB['username'], $sesameOl_DB['password'], $sesameOl_DB['db']);

    if (!$mysqli->connect())
        return 1002;

    // escape
    if (($e_ticket = $mysqli->escape_string($ticket)) === false)
        return 1202;

    // query
    $sql = "SELECT fileID, url, realUrl, token FROM mapping WHERE ticket = '$e_ticket'";

    $res = $mysqli->query($sql);
    if ($res === false)
        return 1202;
    else if (empty($res))
        return 1203;

    return $res[0];
}

function getRemoteData($url) {

    if (($ch = curl_init()) === false)
        return false;

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if (($res = curl_exec($ch)) === false)
        return false;

    curl_close($ch);

    if (!is_array($res = json_decode($res, true)))
        return false;

    if ($res['status'] !== 200)
        return false;

    return $res['result'];
}


// start
if (($msgCode = validate()) !== true)
    _exit($msgCode);

$fileInfo = getFileInfo($_POST['ticket']);
if (!is_array($fileInfo))
    _exit($fileInfo);

if ($_POST['token'] !== $fileInfo[3] && !password_verify($_POST['token'], $fileInfo[3]))
    _exit(1203);
else
    unset($fileInfo[3]);


if (empty($fileInfo[2])) {

    // try to get real url
    $res = getRemoteData('https://api.openload.co/1/file/dlticket?file=' . $fileInfo[0]);

    if (is_array($res) && $res['captcha_url'] === false) {

        $res = getRemoteData('https://api.openload.co/1/file/dl?file=' . $fileInfo[0] . '&ticket=' . $res['ticket']);
        if (is_array($res)) {

            if ($realUrl = $mysqli->escape_string($res['url']))
                $mysqli->query("UPDATE mapping SET realUrl = '$realUrl' WHERE ticket = '$e_ticket'");
        }
    }
}

echo json_encode([
    'msgCode' => 1204,
    'msg' => MSG[1204],
    'data' => [
        'info' => $fileInfo
    ]
]);











