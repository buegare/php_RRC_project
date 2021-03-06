<?php
  require 'utils.php';
  require 'file_upload.php';
  require 'validate_form.php';

  if(!userLoggedIn()) {
    redirectTo('index.php');
  }

  $submission_errors = [];
  $photo_error = false;

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
      try {
        if(isset($_FILES['photo']) && $_FILES['photo']['name'][0]) {
          insertData($_POST, true);
        } else {
          insertData($_POST);
        }
        redirectTo('index.php');
      } catch (Exception $e) {
        $photo_error = $e->getMessage();
      }
    }

  }
  
  function insertData($car, $uploadPhoto = false) {
    require 'connect.php';
    
    $query = "INSERT INTO Car (Description, Make, Mileage, Model, Price, VideoUrl, Year) 
              VALUES (:Description, :Make, :Mileage, :Model, :Price, :VideoUrl, :Year)";
    $statement = $db->prepare($query);
    $bind_values = [
      ':Description' => sanitizeString($car['desc']), 
      ':Make' => $car['make'],
      ':Mileage' => $car['mil'],
      ':Model' => $car['model'],
      ':Price' => $car['price'],
      ':VideoUrl' => sanitizeString($car['video-review-url']),
      ':Year' => $car['year']
    ];
    $statement->execute($bind_values);
    $car_id = $db->lastInsertId();
        
    if($uploadPhoto) {
      uploadPhoto($car_id);
    }
  }
?>

<?php $js_file = "new_car.js"; $css_files = ["show.css", "edit.css"]; include("template/header.php"); ?>


  <div class="container">
    <div class="row" id="wrapper">
      <div class="col-sm-12">

        <form method="post" id='new-car-form' enctype="multipart/form-data">
          
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
              <button type="submit" id="submit" class="btn btn-success">Register Car</button>
              
              <a class="btn btn-secondary" href="index.php">Cancel</a>
            </div>
          </div>
          <!-- End buttons -->
        
          <!-- Start Car info and price -->
          <div class="row">
            <div class="col-sm-12 d-flex align-items-center justify-content-between" id="title">
              <h3>
                <input autocomplete="off" class="font-weight-500" type="text" name="year" id="year" placeholder="Year" autofocus>
                <input autocomplete="off" class="font-weight-500" type="text" name="make" id="make"  placeholder="Make">
                <input autocomplete="off" class="font-weight-500" type="text" name="model" id="model"  placeholder="Model">
              </h3>

              <span>
                <strong>Price: </strong>$<input autocomplete="off" type="number" name="price" id="price" value="0">
              </span>
            </div>
          </div>
          <!-- End Car info and price -->
        
          <!-- Start Car photos section -->
          <div class="row justify-content-center" id="photo-section">
            <div class="col-sm-8" id="car-photo-featured-section">
              <img src="photos/image-placeholder.png" alt="No car image available" id='car-photo-featured' class="image-placeholder-size">
              <?php if($photo_error): ?>
                <span class='error'>Error: <?= $photo_error ?></span>
              <?php endif; ?>
            </div>
            <div class="col-sm-4" id="photo-thumbnail">
            </div>
          </div>
          <!-- End Car photos section -->

          <!-- Start mileage -->
          <div class="row">
            <div class="col-sm-12 d-flex justify-content-between align-items-center" id="mileage">
              <span>
                <strong>Mileage: </strong><input autocomplete="off" type="number" name="mil" id="mil" value="0"> km
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
                <p><textarea placeholder="Enter a description for the car" name="desc" id="desc"></textarea></p>
            </div>
          </div>
          <!-- End description section -->

          <!-- Start review video section -->
          <div class="row" id="video-review">
            <div class="col-sm-12 d-flex justify-content-between" id="video-review-title">
              <span><strong>Video Review</strong></span>
              <div>
                <button type="button" id="apply-btn" class="btn btn-success btn-sm disabled">apply</button>
                <input autocomplete="off" type="text" name="video-review-url" id="video-review-url" placeholder="Enter a video review URL">
              </div>
            </div>
            <div class="col-sm-12" id="video-review-body">
              <div class="embed-responsive embed-responsive-4by3">
                <iframe id="video-review-iframe" class="embed-responsive-item" src="about:blank" allowfullscreen></iframe>
              </div>
            </div>
          </div>
          <!-- End review video section -->

        </form>
      
      </div>
    </div>
  </div>
</body>
</html>