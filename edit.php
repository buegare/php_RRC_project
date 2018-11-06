<?php
  require 'connect.php';
  require 'utils.php';
  require 'file_upload.php';

  $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
  $error = false;

  if(!userLoggedIn() || !$id) {
    redirectTo('index.php');
  }

  $select_car_query = 'SELECT * FROM Car WHERE Id = :id';
  $statement = $db->prepare($select_car_query);
  $statement->bindValue(':id', $id, PDO::PARAM_INT);
  $statement->execute(); 
  $car = $statement->fetch();

  $select_photos_query = 'SELECT Name FROM Photo p, Car c WHERE p.CarId = c.Id AND c.Id = ' . $id;
  $statement = $db->prepare($select_photos_query);
  $statement->execute();
  $photos = $statement->fetchAll();

  function updateData($car, $db, $id, $uploadPhoto = false) {
    $query = "UPDATE car SET Description=?, Make=?, Mileage=?, Model=?, Price=?, VideoUrl=?, 
                Year=? WHERE id=? LIMIT 1";
    $statement = $db->prepare($query);
    $statement->execute([
      $car['description'], $car['make'], $car['mileage'], $car['model'], $car['price'],
      $car['video-url'], $car['year'], $id
    ]);
        
    if($uploadPhoto) {
      $query_delete_photos = "DELETE FROM Photo WHERE CarId = :id";
      $statement = $db->prepare($query_delete_photos);
      $statement->bindValue(':id', $id, PDO::PARAM_INT);
      $statement->execute();
    
      // Deletes all files in directory
      array_map('unlink', glob("photos/$id/*"));
      
      foreach ($_FILES['photo']['name'] as $photo) {
        $sanitized_photo_name = filter_var($photo, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Move car photos to the directory with its id
        rename("photos/$sanitized_photo_name", "photos/" . $id . "/" . $sanitized_photo_name);
        
        $query_insert_photo = "INSERT INTO photo (CarId, Name) values (:CarId, :Name)";
        $statement = $db->prepare($query_insert_photo);
        $bind_values = [':CarId' => $id, ':Name' => $sanitized_photo_name];
        $statement->execute($bind_values);
      }
    }
  }

  if($_POST) {
    $updated_car = [];
    $updated_car["year"] = $_POST["year"];
    $updated_car["make"] = $_POST["make"];
    $updated_car["model"] = $_POST["model"];
    $updated_car["description"] = $_POST["desc"];
    $updated_car["price"] = $_POST["price"];
    $updated_car["mileage"] = $_POST["mil"];
    $updated_car["video-url"] = $_POST["video-review-url"];

    if(isset($_FILES['photo']) && $_FILES['photo']['name'][0]) {
      try {
        uploadPhoto();
        updateData($updated_car, $db, $id, true);
        redirectTo('index.php');
      } catch (Exception $e) {
        $error = $e->getMessage();
      }
    } else {
      updateData($updated_car, $db, $id);
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
  <link rel="stylesheet" href="styles/show.css">
  <link rel="stylesheet" href="styles/edit.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  <script src="js/edit.js"></script>
</head>
<body>
  <div class="container">
    <div class="row" id="wrapper">
      <div class="col-sm-12">
        <?php if(!$car): ?>
          <h3>There is no car with this id</h3>
        <?php else: ?>

          <form method="post" enctype="multipart/form-data">

            <!-- Start buttons -->
            <div class="row">
              <div class="col-sm-12 d-flex justify-content-between" id="buttons">
                <a href="update.php?id=<?= $car["Id"] ?>">
                  <button type="submit" class="btn btn-success">Update Changes</button>
                </a>
                
                <a href="show.php?id=<?= $car["Id"] ?>">
                  <button type="button" class="btn btn-secondary">Cancel</button>
                </a>
              </div>
            </div>
            <!-- End buttons -->

            <!-- Start Car info and price -->
            <div class="row">
              <div class="col-sm-12 d-flex align-items-center justify-content-between" id="title">
                <h3>
                  <input autocomplete="off" class="font-weight-500" type="text" name="year" id="year" value="<?= $car["Year"] ?>">
                  <input autocomplete="off" class="font-weight-500" type="text" name="make" id="make" value="<?= $car["Make"] ?>">
                  <input autocomplete="off" class="font-weight-500" type="text" name="model" id="model" value="<?= $car["Model"] ?>">
                </h3>

                <span>
                  <strong>Price: </strong>$<input autocomplete="off" type="number" name="price" id="price" value="<?= $car["Price"] ?>">
                </span>
              </div>
            </div>
            <!-- End Car info and price -->
            
            <!-- Start Car photos section -->
            <div class="row justify-content-center" id="photo-section">
              <div class="col-sm-8" id="car-photo-featured-section">
                <img class="img-fluid" id="car-photo-featured" src="photos/<?= $car["Id"] ?>/<?= $photos[0]["Name"] ?>" alt="<?= $photos[0]["Name"] ?>">
                <?php if($error): ?>
                  <span class='error'>Error: <?= $error ?></span>
                <?php endif; ?>
              </div>
              <div class="col-sm-4" id="photo-thumbnail">
                <?php for ($i=1; $i < count($photos); $i++): ?>
                  <img class="img-fluid car-photos" src="photos/<?= $car["Id"] ?>/<?= $photos[$i]["Name"] ?>" alt="<?= $photos[$i]["Name"] ?>">
                <?php endfor; ?>
              </div>
            </div>
            <!-- End Car photos section -->

            <!-- Start mileage -->
            <div class="row">
              <div class="col-sm-12 d-flex justify-content-between align-items-center" id="mileage">
                <span>
                  <strong>Mileage: </strong><input autocomplete="off" type="number" name="mil" id="mil" value="<?= $car["Mileage"] ?>"> km
                </span>
                <div>
                  <input type="file" class="form-control-file" name="photo[]" id="photo" multiple>
                </div>
              </div>
            </div>
            <!-- End mileage -->

            <!-- Start description section -->
            <div class="row" id="description">
              <div class="col-sm-12" id="description-title">
                <strong>Description</strong>
              </div>
              <div class="col-sm-12" id="description-body">
                  <p><textarea placeholder="There is no description for this car. Type one here" name="desc" id="desc"><?= $car["Description"] ?></textarea></p>
              </div>
            </div>
            <!-- End description section -->

            <!-- Start review video section -->
            <div class="row" id="video-review">
              <div class="col-sm-12 d-flex justify-content-between" id="video-review-title">
                <span><strong>Video Review</strong></span>
                <div>
                  <button type="button" id="apply-btn" class="btn btn-success btn-sm disabled">apply</button>
                  <input autocomplete="off" type="text" name="video-review-url" id="video-review-url" placeholder="No url found. Type one here." value="<?= $car["VideoURL"] ? $car["VideoURL"] : "" ?>">
                </div>
              </div>
              <div class="col-sm-12" id="video-review-body">
                <div class="embed-responsive embed-responsive-4by3">
                  <iframe id="video-review-iframe" class="embed-responsive-item" src="<?= $car["VideoURL"] ?>" allowfullscreen></iframe>
                </div>
              </div>
            </div>
            <!-- End review video section -->

          </form>

        <?php endif; ?>
    </div>
  </div>
</body>
</html>