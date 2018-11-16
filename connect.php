<?php
    if (!defined('DB_DSN')) {
      define('DB_DSN','mysql:host=localhost;dbname=gagroup;charset=utf8');
    }

    if (!defined('DB_USER')) {
      define('DB_USER','admin');
    }

    if (!defined('DB_PASS')) {
      define('DB_PASS','admin');
    }    

    try {
      $db = new PDO(DB_DSN, DB_USER, DB_PASS);
    } catch (PDOException $e) {
      print "Error: " . $e->getMessage();
      die();
    }
?>