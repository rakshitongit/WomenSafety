<?php

class database {
    /**
     * database constructor.
     * creates connection to database
     */
    function __construct() {
        try {
            $this->mysql = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSERNAME, DBPASSWORD);
            // set the PDO error mode to exception
            $this->mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }


    /**
     * database destructor
     * destructs connection to database
     */
    function __destruct() {
        $this->mysql = null;
    }


    /**
     * Retreives from database
     * @param $sql
     * @return array
     */
    function retrievemysql($sql) {
        $arr = array();
        try {
            $stmt = $this->mysql->prepare($sql);
            $stmt->execute();
            $a = array();
            // set the resulting array to associative
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
            if ($result) {
                $a = $stmt->fetchAll();
            }
            $arr["success"] = true;
            $arr["datalength"] = count($a);
            $arr["data"] = $a;
        } catch (PDOException $e) {
            $arr["success"] = false;
            $arr["message"] = $e->getMessage();
        }
        return $arr;
    }

    /**
     * runs any database query
     * @param $sql
     * @return array
     */
    function runquerymysql($sql) {
        $arr = array();
        try {
            // use exec() because no results are returned
            $this->mysql->exec($sql);
            $arr["success"] = true;
            $arr["lastinsertid"] = $this->mysql->lastInsertId();
        } catch (PDOException $e) {
            $arr["success"] = false;
            $arr["message"] = $e->getMessage();
        }
        return $arr;
    }
}