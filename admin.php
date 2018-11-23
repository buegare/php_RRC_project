<?php
  require 'utils.php';

  if(isset($_SESSION['user']) && !$_SESSION['user']) {
    $_SESSION = [];
  }

  // Find user in the database
  function findUser($admin_username) {
    require 'connect.php';
    $select_query = "SELECT * FROM User WHERE Name = '$admin_username' LIMIT 1";
    $statement = $db->prepare($select_query);
    $statement->execute();
    $user = $statement->fetch();
    return $user;
  }

  if($_POST) {
    $sanitized_user = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $sanitized_pass = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $user = findUser($sanitized_user);

    if($user) {
      if (password_verify($sanitized_pass ? $sanitized_pass : "", $user['password'])) {
        $_SESSION['user'] = $user;
        redirectTo('index.php');
      } else {
        $_SESSION['user'] = false;
      }
    } else {
      $_SESSION['user'] = false;
    }
  }
?>

<?php $js_file = null; $css_files = ["admin.css"]; include("template/header.php"); ?>

  <div class='container'>
    <div class="row">
      <form method="post" id='login-form'>
        <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" class="form-control">
      </div>  
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" class="form-control">
      </div>  
      <button type="submit" class='btn btn-primary'>Login</button>
      <a href='index.php' class='btn btn-secondary' id='btn-cancel'>Cancel</a>
        <?php if(isset($_SESSION['user']) && !$_SESSION['user']): ?>
          <p class='error'>ERROR: Username not found or Password is incorrect</p>
        <?php endif;?>
      </form>
    </div>
  </div>
</body>
</html>