<?php

  session_start();
  if(isset($_SESSION['user']) && !$_SESSION['user']) {
    $_SESSION = [];
  }

  // Find user in the database
  function findUser($admin_username) {
    require 'connect.php';
    $select_query = "SELECT Name FROM User WHERE Name = '$admin_username' LIMIT 1";
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

<?php $js_file = null; $css_files = ["admin.css"]; include("template/header.php"); ?>

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