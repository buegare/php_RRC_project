<?php
    $url = "";
    $server = "";
    $username = "";
    $password = "";
    $db = "";
    
    if(getenv("CLEARDB_DATABASE_URL")) {
      // Production
      $url = parse_url(getenv("CLEARDB_DATABASE_URL"));
  
      $server = $url["host"];
      $username = $url["user"];
      $password = $url["pass"];
      $db = substr($url["path"], 1);
    } else {
      // Development
      $server = 'localhost';
      $username = 'admin';
      $password = 'admin';
      $db = 'gagroup';
    }

    if (!defined('DB_DSN')) {
      define('DB_DSN',"mysql:host={$server};dbname={$db};charset=utf8");
    }

    if (!defined('DB_USER')) {
      define('DB_USER',$username);
    }

    if (!defined('DB_PASS')) {
      define('DB_PASS',$password);
    }    

    try {
      $db = new PDO(DB_DSN, DB_USER, DB_PASS);
    } catch (PDOException $e) {
      print "Error: " . $e->getMessage();
      die();
    }
?>