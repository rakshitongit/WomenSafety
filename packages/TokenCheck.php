<?php

class TokenCheck
{
    function __construct($token, $userid)
    {
        $this->token = $token;
        $this->userid = $userid;
//        $response = $this->checkToken($token, $userid);
    }

    function checkToken()
    {
        $response = array();
        $db = new database();
        $sql = "SELECT * FROM `login_log` WHERE `token` LIKE '" . $this->token . "' AND `userid` = " . $this->userid;
        $resp = $db->retrievemysql($sql);
        if ($resp["success"] && $resp["datalength"] != 0) {
            $i = 0;
            foreach ($resp["data"] as $t) {
                if ($i == 0) {
                    $response["token"] = true;
                    $response["message"] = "Valid Token";
                } else {
                    $response["token"] = false;
                    $reponse["message"] = "Logged in some other device";
                    break;
                }
                $i++;
            }
        } else {
            $response["token"] = false;
            $response["message"] = "Token Not Found";
        }

        return $response;
    }

    function findFcmId($userid)
    {
        $db = new database();
        $sql = "SELECT * FROM `fcmhash` WHERE `userid` = " . $userid . " ORDER BY `idfcmhash` DESC";
        $res = $db->retrievemysql($sql);
        if ($res["success"] && $res["datalength"] != 0) {
            $res["data"] = $res["data"][0]["hash"];
        } else {
            $res["success"] = false;
            $res["message"] = "FCM Hash not available";
        }
        return $res;
    }

    function sendnotification($deviceid, $title, $body, $icon, $data, $pageid)
    {
        // Pageid 1 means Panchadeek
        // Pageid 2 means Event

#API access key from Google API's Console
        define('API_ACCESS_KEY', FCMAPIKEY);
        $registrationIds = $deviceid;

#prep the bundle
        $msg = array
        (
            'body' => $body,
            'title' => $title,
            'icon' => $icon,
            "sound" => "default",
            "pageid" => $pageid,
            'largeIcon' => 'large_icon',
            "image" => 'https://gsbparivar.com/uploads/icon.png'
        );

        $fields = array
        (
            'to' => $registrationIds,
            'data' => $data,
            'notification' => $msg
        );


        $headers = array
        (
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );

#Send Reponse To FireBase Server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

#Echo Result Of FireBase Server
        return $result;

    }


}