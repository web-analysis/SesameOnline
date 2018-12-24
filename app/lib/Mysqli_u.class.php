<?php
/**
 * Created by PhpStorm.
 * User: @SiGool
 * Date: 2018/12/14 0014
 * Time: 09:46
 */

class Mysqli_u {

    private $host;
    private $username;
    private $password;
    private $db;

    private $mysqli;

    public function __construct($host, $username, $password, $db) {

        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->db = $db;
    }

    public function connect() {

        $this->mysqli = new mysqli($this->host, $this->username, $this->password, $this->db);

        if ($this->mysqli->connect_error) {

            $this->mysqli = null;
            return false;
        }

        $this->mysqli->set_charset('utf8');
        return true;
    }

    // simple crud
    public function query($query) {

        if (!$this->mysqli)
            return false;

        if (($r = $this->mysqli->query($query)) === false)
            return false;

        // DML
        if ($r === true)
            return $this->mysqli->affected_rows;


        // DQL
        $result = [];
        while ($row = $r->fetch_row())
            $result[] = $row;

        $r->close();
        return $result;
    }

    public function escape_string($str) {

        if (!$this->mysqli)
            return false;

        return $this->mysqli->real_escape_string($str);
    }

    public function error() {

        if (!$this->mysqli)
            return false;

        return [
            'errno' => $this->mysqli->errno,
            'error' => $this->mysqli->error
        ];
    }

    public function close() {

        if (!$this->mysqli)
            $this->mysqli->close();
    }

}