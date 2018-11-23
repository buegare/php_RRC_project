<?php
  require 'connect.php';
  require 'validate_form.php';

  $userExists = null;
  $error = null;
  $success = null;

  function findAllUsers($db) {
    $select_query = "SELECT Name FROM User WHERE Name != 'admin'";
    $statement = $db->prepare($select_query);
    $statement->execute();
    $users = $statement->fetchAll();
    return $users;
  }
  
   // Find user in the database
   function findUser($admin_username, $db) {
    $select_query = "SELECT Name FROM User WHERE Name = '$admin_username' LIMIT 1";
    $statement = $db->prepare($select_query);
    $statement->execute();
    $user = $statement->fetch();
    return $user;
  }

  function createUser($user = null, $pass = null, $db) {
    if(!$user || !$pass) {
      throw new Exception('Username and Password are required');
    }

    $query = "INSERT INTO User (Name, Password) VALUES (:Name, :Password)";
    $statement = $db->prepare($query);
    $bind_values = [
    ':Name' => $user,
    ':Password' => password_hash($pass, PASSWORD_DEFAULT)
    ];
    $statement->execute($bind_values);
  }

  function deleteUser($user, $db) {
    $query = "DELETE FROM User WHERE Name=?";
    $statement = $db->prepare($query);
    $statement->execute([$user]);
  }

  if($_POST) {
    if (isset($_POST['user-to-delete'])) {
      $sanitized_user = sanitizeString($_POST['user-to-delete']);
      deleteUser($sanitized_user, $db);
    } else {
      $sanitized_user = sanitizeString($_POST['username']);
      $sanitized_pass = sanitizeString($_POST['password']);
      $userExists = findUser($sanitized_user, $db);
      
      if(!$userExists) {
        try {
          createUser($sanitized_user, $sanitized_pass, $db);
          $success = "User {$sanitized_user} created successfully";
        } catch (Exception $e) {
          $error = $e->getMessage();
        }
      }
      
    }
  }
?>

<?php $js_file = null; $css_files = ["admin.css", "manage_users.css"]; include("template/header.php"); ?>

  <div class='container'>
    <div class="row">
      <form method="post" id='login-form'>
        <?php if($success): ?>
          <p class='alert alert-success'><?= $success ?></p>
        <?php endif;?>
        <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" class="form-control">
      </div>  
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" class="form-control">
      </div>  
      <button type="submit" class='btn btn-success'>Create User</button>
      <a class='btn btn-secondary' id='btn-cancel' href='index.php'>Cancel</a>
        <?php if($userExists): ?>
          <p class='error'>ERROR: Username already exists</p>
        <?php endif;?>
        <?php if($error): ?>
          <p class='error'>ERROR: <?= $error?></p>
        <?php endif;?>

        <div class="row">
          <div class="col-sm-12">
          <label>Users:</label>
            <ul id="user-list">
              <?php foreach(findAllUsers($db) as $user): ?>
                <li>
                  <?= $user['Name'] ?>
                  <button type="submit" class="btn btn-danger btn-sm" name="user-to-delete" value="<?= $user['Name'] ?>">Delete</button>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>

      </form>
    </div>
  </div>
</body>
</html>