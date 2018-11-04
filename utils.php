<?php
  session_start();

  function userLoggedIn() {
    return isset($_SESSION['user']) && $_SESSION['user'];
  }
?>