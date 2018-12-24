<?php
/**
 * Created by PhpStorm.
 * User: @SiGool
 * Date: 2018/12/19 0019
 * Time: 17:10
 */

require_once './lib/Mysqli_u.class.php';
require_once './config/DB.php';
require_once './config/msgReporter.php';

header('Content-Type: application/json');

if (!isset($_POST['reqFrom']) || $_POST['reqFrom'] !== 'sesameOl')
    exit(json_encode([
        'msgCode' => 1001,
        'msg' => MSG[1001],
        'data' => []
    ]));

function validate() {

    $errItems = [];

    if (!isset($_POST['fileID']) || empty($_POST['fileID']) || strlen($_POST['fileID']) > 15)
        $errItems[] = 0;

    if (!isset($_POST['url']) || !filter_var($_POST['url'], FILTER_VALIDATE_URL) || strlen($_POST['url']) > 80)
        $errItems[] = 1;

    if (!isset($_POST['ticket']) || !preg_match('/^(?!\s*$)[\S\s]{2,12}$/', $_POST['ticket']))
        $errItems[] = 2;

    if (!isset($_POST['token']) || empty($_POST['token']))
        $_POST['token'] = '---'; // no token
    else if (!preg_match('/^\w{4,18}$/', $_POST['token']))
        $errItems[] = 3;         // incorrect token format
    else
        $_POST['token'] = password_hash($_POST['token'], PASSWORD_DEFAULT);

    return $errItems;
}


function createMapping($ticket, $token, $fileID, $url) {

    global $sesameOl_DB;
    $mysqli = new Mysqli_u($sesameOl_DB['host'], $sesameOl_DB['username'], $sesameOl_DB['password'], $sesameOl_DB['db']);

    if (!$mysqli->connect())
        return 1002;

    // escape
    if (($fileID = $mysqli->escape_string($fileID)) === false
            || ($url = $mysqli->escape_string($url)) === false || ($ticket = $mysqli->escape_string($ticket)) === false)
        return 1103;

    // query
    $sql = "INSERT INTO mapping (ticket, token, fileID, url) VALUES ('$ticket', '$token', '$fileID', '$url')";

    $res = $mysqli->query($sql);
    if ($res === false) {

        $err = $mysqli->error();

        if ($err['errno'] === 1062) // already exist
            return 1102;

        return 1103;
    }

    if (is_int($res) && $res > 0)
        return 1104;

    return 1103;
}


// validate
if (!empty($errItems = validate()))
    exit(json_encode([
        'msgCode' => 1101,
        'msg' => MSG[1101],
        'data' => [
            'errItems' => $errItems
        ]
    ]));

// create
$msgCode = createMapping($_POST['ticket'], $_POST['token'], $_POST['fileID'], $_POST['url']);

echo json_encode([
    'msgCode' => $msgCode,
    'msg' => MSG[$msgCode],
    'data' => []
]);
