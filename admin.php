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
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>
  <pre><?php print_r($_SESSION) ?></pre>
  <div class='container'>
    <div class="row">
      <form method="post" id='login-form'>
        <label for="username">Username</label>
        <input type="text" name="username" id="username">
        <label for="password">Password</label>
        <input type="text" name="password" id="password">
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