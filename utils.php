<?php
  function userLoggedIn() {
    return isset($_SESSION['user']) && $_SESSION['user'];
  }
?>