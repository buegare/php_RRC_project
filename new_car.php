<?php
  require 'utils.php';
  require 'file_upload.php';

  if(!userLoggedIn()) {
    redirectTo('index.php');
  }

  $error = false;
  
  function insertData($car, $uploadPhoto = false) {
    require 'connect.php';
    
    $query = "INSERT INTO car (Description, Make, Mileage, Model, Price, VideoUrl, Year) 
              values (:Description, :Make, :Mileage, :Model, :Price, :VideoUrl, :Year)";
    $statement = $db->prepare($query);
    $bind_values = [
      ':Description' => $car['description'], 
      ':Make' => $car['make'],
      ':Mileage' => $car['mileage'],
      ':Model' => $car['model'],
      ':Price' => $car['price'],
      ':VideoUrl' => $car['video-url'],
      ':Year' => $car['year']
    ];
    $statement->execute($bind_values);
    $car_id = $db->lastInsertId();
        
    if($uploadPhoto) {
      $sanitized_photo_name = filter_var($_FILES['photo']['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      mkdir("photos/$car_id"); // Create directory with the id of the new car
      // Move car photos to the directory with its id
      rename("photos/$sanitized_photo_name", "photos/" . $car_id . "/" . $sanitized_photo_name);

      $query_insert_photo = "INSERT INTO photo (CarId, Name) values (:CarId, :Name)";
      $statement = $db->prepare($query_insert_photo);
      $bind_values = [':CarId' => $car_id, ':Name' => $sanitized_photo_name];
      $statement->execute($bind_values);
    }
  }

  if($_POST) {
    $photo_upload_detected = isset($_FILES['photo']) && ($_FILES['photo']['error'] === 0);
    
    if($photo_upload_detected) {
      try {
        uploadPhoto();
        insertData($_POST, true);
        redirectTo('index.php');
      } catch (Exception $e) {
        $error = $e->getMessage();
      }
    } else {
      insertData($_POST);
      redirectTo('index.php');
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
  <link rel="stylesheet" href="styles/new_car.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</head>
<body>
  <div class='container'>
      <div class="row">
        <form method="post" id='new-car-form' enctype="multipart/form-data">
          <div class="form-group">
            <label for="make">Make</label>
            <input type="text" class="form-control" name="make" id="make">
          </div>
          <div class="form-group">
            <label for="model">Model</label>
            <input type="text" class="form-control" name="model" id="model">
          </div>
          <div class="form-group">
            <label for="year">Year</label>
            <input type="number" class="form-control" name="year" id="year">
          </div>
          <div class="form-group">
            <label for="mileage">Mileage</label>
            <input type="number" class="form-control" name="mileage" id="mileage">
          </div>
          <div class="form-group">
            <label for="price">Price</label>
            <input type="number" class="form-control" name="price" id="price">
          </div>
          <div class="form-group">
            <label for="video-url">Video URL</label>
            <input type="url" class="form-control" name="video-url" id="video-url">
          </div>
          <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" class="form-control" id="description" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label for="photo">Upload Photo</label>
            <input type="file" class="form-control-file" name="photo" id="photo">
            <?php if($error): ?>
              <p class='error'>Error: <?= $error ?></p>
            <?php endif; ?>
          </div>
          <button type="submit" name="submit" class="btn btn-primary">Register Car</button>
          <a href="index.php">
            <button type="button" class="btn btn-secondary float-right">Cancel</button>
          </a>
        </form>
      </div>
  </div>
</body>
</html>