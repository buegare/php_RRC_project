<?php
  require 'connect.php';
  require 'utils.php';
  require 'validate_form.php';

  $id = validateInt($_GET["id"]);

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