<?php
  require 'connect.php';
  require 'utils.php';

  $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

  if(!userLoggedIn() || !$id) {
    redirectTo('index.php');
  }

  $query = "DELETE FROM car WHERE id = :id";
  $statement = $db->prepare($query);
  $statement->bindValue(':id', $id, PDO::PARAM_INT);
  $statement->execute();

  // Deletes all files in directory
  array_map('unlink', glob("photos/$id/*"));
  // Deletes directory
  rmdir("photos/$id");

  redirectTo('index.php');
?>