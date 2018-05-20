<?php
/**
 * +----------------------------------------------------------------------
 * | 璧合科技
 * +----------------------------------------------------------------------
 * | Copyright (c) 2014-2015  All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: 李锦 <lijin@behe.com>
 * +----------------------------------------------------------------------
 * | Create Date: 2018/4/16 17:44
 * +----------------------------------------------------------------------
 */


class mysqlConnect {

    private $host, $user, $password, $db;
    private                          $port = 3306;

    private $connect;

    public function __construct($host, $user, $password, $db, $port = 3306) {
        $this->password = $password;
        $this->host = $host;
        $this->user = $user;
        $this->port = $port;
        $this->db = $db;
        $this->connect();
    }

    public function setDb($db) {
        $this->db = $db;
        mysqli_select_db($this->connect, $this->db);
        return $this;
    }

    private function connect() {
        $this->connect = mysqli_connect($this->host, $this->user, $this->password, $this->db, $this->port);
        if (!$this->connect) {
            echo 'connect db failed !!!';
            exit;
        }
        mysqli_set_charset($this->connect, 'utf8');
    }

    public function query($sql) {
        $query = mysqli_query($this->connect, $sql);

//        echo mysqli_errno($this->connect); exit;

        return mysqli_fetch_all($query);
    }

}