<?php
  require 'connect.php';
  require 'utils.php';
  require 'file_upload.php';
  require 'validate_form.php';

  $id = validateInt($_GET["id"]);
  $submission_errors = [];
  $photo_error = false;

  if(!userLoggedIn() || !$id) {
    redirectTo('index.php');
  }

  $select_car_query = 'SELECT * FROM Car WHERE Id = :id';
  $statement = $db->prepare($select_car_query);
  $statement->bindValue(':id', $id, PDO::PARAM_INT);
  $statement->execute(); 
  $car = $statement->fetch();

  $select_photos_query = "SELECT Name FROM Photo p, Car c WHERE p.CarId = c.Id AND c.Id = {$id} AND p.Name LIKE 'thumbnail_%'";
  $statement = $db->prepare($select_photos_query);
  $statement->execute();
  $photos = $statement->fetchAll();

  function deleteAllPhotos($db, $id) {
    $query_delete_photos = "DELETE FROM Photo WHERE CarId = :id";
    $statement = $db->prepare($query_delete_photos);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();
  }

  function updateData($car, $db, $id, $photos = null, $uploadPhoto = false) {
    $query = "UPDATE car SET Description=?, Make=?, Mileage=?, Model=?, Price=?, VideoUrl=?, 
                Year=? WHERE id=? LIMIT 1";
    $statement = $db->prepare($query);
    $statement->execute([
      $car['description'], $car['make'], $car['mileage'], $car['model'], $car['price'],
      $car['video-url'], $car['year'], $id
    ]);

    if(isset($_POST["delete_photos"])) {
      deleteAllPhotos($db, $id);

      // Deletes all files in directory
      array_map('unlink', glob("photos/$id/*"));
      // Deletes directory
      rmdir("photos/$id");
    }
        
    if($uploadPhoto) {
      if($photos) { // Check if car has photos
        deleteAllPhotos($db, $id);

        // Deletes all files in directory
        array_map('unlink', glob("photos/$id/*"));
      }
    
      uploadPhoto($id);
    }
  }

  if($_POST) {
    array_push($submission_errors, validateField($_POST["make"]));
    array_push($submission_errors, validateField($_POST["mil"], false,
      array("options" => array("min_range" => 0))));
    array_push($submission_errors, validateField($_POST["model"]));
    array_push($submission_errors, validateField($_POST["price"], false,
      array("options" => array("min_range" => 0))));
    array_push($submission_errors, validateField($_POST["year"], false,
      array("options" => array("min_range" => 1950, "max_range" => date("Y") + 1))));

    if (empty(array_filter($submission_errors))) {
      $updated_car = [];
      $updated_car["year"] = $_POST["year"];
      $updated_car["make"] = $_POST["make"];
      $updated_car["model"] = $_POST["model"];
      $updated_car["description"] = sanitizeString($_POST["desc"]);
      $updated_car["price"] = $_POST["price"];
      $updated_car["mileage"] = $_POST["mil"];
      $updated_car["video-url"] = sanitizeString($_POST["video-review-url"]);

      try {
        if(isset($_FILES['photo']) && $_FILES['photo']['name'][0]) {
          updateData($updated_car, $db, $id, $photos, true);
        } else {
          updateData($updated_car, $db, $id);
        }
        redirectTo("show.php?id={$id}");
      } catch (Exception $e) {
        $photo_error = $e->getMessage();
      }

    }
  }  
?>

<?php $js_file = "edit.js"; $css_files = ["show.css", "edit.css"]; include("template/header.php"); ?>

<body>
  <div class="container">
    <div class="row" id="wrapper">
      <div class="col-sm-12">
        <?php if(!$car): ?>
          <h3>There is no car with this id</h3>
        <?php else: ?>

          <form method="post" enctype="multipart/form-data">

            <!-- Start errors panel -->
            <?php if(!empty(array_filter($submission_errors))): ?>
              <div class="row" id="errors-panel">
                <div class="col-sm-12">
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>There was a problem with your form submission!</strong>
                    <hr>
                    <p>These are the requirements for some of the fields:</p>
                    <ul>
                      <li>Year is required and should be between 1950 and next year.</li>
                      <li>Make is required.</li>
                      <li>Model is required.</li>
                      <li>Price is required and should be greater than 0.</li>
                      <li>Mileage is required and should be greater or equal 0.</li>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                </div>
              </div>
            <?php endif; ?>
            <!-- End errors panel -->

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
                <?php $photo = getPhoto($car["Id"], $db, "featured_"); ?>
                <?php if($photo): ?>
                  <img class="img-fluid" id="car-photo-featured" src="photos/<?= $car["Id"] ?>/<?= $photo["Name"] ?>" alt="<?= $photo["Name"] ?>">
                <?php else: ?>
                  <img src="photos/image-placeholder.png" alt="No car image available" id="car-photo-featured" class='image-placeholder-size'>
                <?php endif; ?>
                <?php if($photo_error): ?>
                  <span class='error'>Error: <?= $photo_error ?></span>
                <?php endif; ?>
              </div>
              <div class="col-sm-4" id="photo-thumbnail">
                <?php if($photos): ?>
                  <?php for ($i=0; $i < count($photos); $i++): ?>
                    <img class="img-fluid" src="photos/<?= $car["Id"] ?>/<?= $photos[$i]["Name"] ?>" alt="<?= $photos[$i]["Name"] ?>">
                  <?php endfor; ?>
                <?php endif; ?>
              </div>
            </div>
            <!-- End Car photos section -->

            <!-- Start mileage -->
            <div class="row">
              <div class="col-sm-12 d-flex justify-content-between align-items-center" id="mileage">
                <span>
                  <strong>Mileage: </strong><input autocomplete="off" type="number" name="mil" id="mil" value="<?= $car["Mileage"] ?>"> km
                </span>
                <?php if($photos): ?>
                  <div>
                    <input type="checkbox" name="delete_photos" value="true"> Delete all photos<br>
                  </div>
                <?php endif; ?>
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