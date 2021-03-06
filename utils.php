<?php
  session_start();

  function userLoggedIn() {
    return isset($_SESSION['user']) && $_SESSION['user'];
  }

  function redirectTo($location) {
    header("Location: $location");
    exit;
  }

  function getPhoto($id, $db, $prefix) {
    $select_query = 'SELECT Name FROM Photo p, Car c WHERE p.CarId = c.Id AND c.Id = ' . $id . " AND p.Name LIKE '{$prefix}%'";
    $statement = $db->prepare($select_query);
    $statement->execute();
    $photo = $statement->fetch();
    return $photo;
  }
?>