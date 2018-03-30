<?php

require 'config.php';

/**
 * Позволяет выполнять запросы к базе дынных
 */

class db {
    private static $instance;
    private static $mysqli;
    private static $error;

    private function __construct($db_host, $db_user, $db_password, $db_name = null) {
        self::$mysqli = new mysqli($db_host, $db_user, $db_password);
		if (self::$mysqli->connect_errno) {
            return $this->new_error("Connection to the database failed.");
        }
        else if ($db_name && !self::$mysqli->query("use ".$db_name)) {
            return $this->new_error(self::$mysqli->error);
        }
        self::$mysqli->query("SET NAMES utf-8");
        self::$mysqli->query("SET CHARACTER SET utf8");
        
        return true;
    }
    function __destruct() {
        if (!self::$mysqli->connect_errno) {
            self::$mysqli->close();
        }
    }
    private function new_error($error_msg) {
        self::$error = $error_msg;
        return false;
    }

    // Публичный интерфейс
    public static function connect($db_name = null) {
        if (is_null(self::$instance)){
            self::$instance = new self(DB_HOST, DB_USER, DB_PASSWORD, $db_name);
        }
    }
    public static function query($string, $limiter) {
        if (self::is_error()) return;
        else {
            if ($result = self::$mysqli->query("SELECT meaning FROM dic WHERE word REGEXP '^$string' LIMIT $limiter")) {
                $tmp = array();
                while ($row = $result->fetch_row()) {
                    array_push($tmp, $row[0]);
                }
                $result->close();

                return $tmp;
            }
            else return false;
        }
    }
    public static function is_error() {
        if (!isset(self::$instance) || !is_null(self::$error)) return true;
        else return false;
    }
    public static function error_msg() {
        if (self::is_error()) return self::$error;
        else if (is_null(self::$instance)) return "Need connection to the database.";
        else return "No errors.";
    }    
}