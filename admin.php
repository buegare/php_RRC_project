<?php

  session_start();
  if(isset($_SESSION['user']) && !$_SESSION['user']) {
    $_SESSION = [];
  }

  // Find user in the database
  function findUser($username) {
    require 'connect.php';
    $select_query = "SELECT Name FROM User WHERE Name = '$username' LIMIT 1";
    $statement = $db->prepare($select_query);
    $statement->execute();
    $user = $statement->fetch();
    return $user;
  }

  if($_POST) {
    $sanitized_user = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  
    $user = findUser($sanitized_user);

    if($user) {
      $_SESSION['user'] = $user;
      header('Location: index.php');
      exit;
    } else {
      $_SESSION['user'] = false;
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Geske Automotive Group</title>
  <link rel="stylesheet" href="styles/admin.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</head>
<body>
  <div class='container'>
    <div class="row">
      <form method="post" id='login-form'>
        <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" class="form-control">
      </div>  
      <div class="form-group">
        <label for="password">Password</label>
        <input type="text" name="password" id="password" class="form-control">
      </div>  
      <button type="submit" class='btn btn-primary'>Login</button>
      <a href='index.php'>
        <button type='button' class='btn btn-secondary' id='btn-cancel'>Cancel</button>
      </a>
        <?php if(isset($_SESSION['user']) && !$_SESSION['user']): ?>
          <p class='error'>ERROR: Username not found</p>
          <p class='error'>ERROR: Password is incorrect</p>
        <?php endif;?>
      </form>
    </div>
  </div>
</body>
</html>