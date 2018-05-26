<?php
/**
 * Created by PhpStorm.
 * User: rakshit
 * Date: 25/5/18
 * Time: 1:25 AM
 */

require __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/includes/bootstrap.php";
$db = new database(); //database to use

$response = array();
$response["success"] = false;

function insertLocation($userid, $location, $db)
{
    $sql = "INSERT INTO `user_location`(`userid`, `latitude`, `longitude`) VALUES (" . $userid . ", '" . addslashes($location["latitude"]) . "', '" . addslashes($location["longitude"]) . "')";
    $r = $db->runquerymysql($sql);
}

function fcmPushHash($db, $userid, $hash)
{
    if ($hash != '') {
        $sql = "DELETE FROM `fcmhash` WHERE `userid` = " . $userid;
        $r = $db->runquerymysql($sql);
        $sql = "INSERT INTO `fcmhash`(`hash`, `userid`) VALUES ('" . addslashes($hash) . "', " . $userid . ")";
        $r = $db->runquerymysql($sql);
    }
}

function emailLogin($db, $data, $location, $hash)
{
    $sql = "SELECT * FROM `email` WHERE `emailid` LIKE '" . $data["username"] . "'";
    $res = $db->retrievemysql($sql);
    if ($res["success"] && $res["datalength"] != 0) {
        if (password_verify($data["password"], $res["data"][0]["password"])) {
            insertLocation($res["data"][0]["userid"], $location, $db);
            $token = password_hash('token' . $res["data"][0]["userid"], PASSWORD_BCRYPT);
            $sql = "INSERT INTO `login_log`(`token`, `userid`) VALUES ('" . $token . "', " . $res["data"][0]["userid"] . ")";
            $r = $db->runquerymysql($sql);
            fcmPushHash($db, $res["data"][0]["userid"], $hash);
            $res["token"] = $token;
        } else {
            $res["success"] = false;
            $res["message"] = "Password Incorrect";
        }
    } else {
        $res["success"] = false;
        $res["message"] = "Email does not exist";
    }
    return $res;
}

function validateandGenerateOTP($email, $db)
{
    $emailsettings = new emailsettings(HOSTNAME, USERNAMESMTP, PASSWORDSMTP);       // smtp email
    $sql = "SELECT * FROM `email` WHERE `emailid` LIKE '" . $email . "'";
    $res = $db->retrievemysql($sql);
    if ($res["success"] && $res["datalength"] != 0) {
        $res["otp"] = rand(100000, 999999);
        $m = $emailsettings->sendmail('', $email, 'OTP', 'Hello, <br> Your OTP is ' . $res["otp"] . ' <br><br>Regards.<br>Women safety App', '');

    } else {
        $res["success"] = false;
        $res["message"] = "Email does not exist";
    }
    return $res;
}

function passwordenter($db, $userid, $password)
{
    $sql = "UPDATE `email` SET `password` = '" . password_hash($password, PASSWORD_BCRYPT) . "' WHERE `userid` = " . $userid;
    return $db->runquerymysql($sql);
}

if (isset($request["emailLogin"]) && $request["emailLogin"] == "yes") {
    $response = emailLogin($db, $request["logindetails"], $request["location"], $request["hash"]);
}

if (isset($request["resetPassword"]) && $request["resetPassword"] == "yes") {
    $response = validateandGenerateOTP($request["email"], $db);
}

if (isset($request["passwordenter"]) && $request["passwordenter"] == "yes") {
    $response = passwordenter($db, $request["userid"], $request["password"]);
}

/*$token = new TokenCheck('$2y$10$wzrebKDoEEK4VGfsay6C0eMiUGDzrPHFLoqr9YagesPtu/TlRn1se', '1');
$fcmid = $token->findFcmId(1);
$noti = $token->sendnotification($fcmid["data"], "PHP Notification Testing", "Body", "", array("id" => "1", "name" => "Rakshit"), "");
var_dump($noti);*/

echo json_encode($response);