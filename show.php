<?php
  require 'connect.php';
  require 'utils.php';

  $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

  if(!$id) {
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
  
?>

<?php $js_file = null; $css_files = ["show.css"]; include("template/header.php"); ?>

<body>
  <div class="container">
    <div class="row" id="wrapper">
      <div class="col-sm-12">
        <?php if(!$car): ?>
          <h3>There is no car with this id</h3>
        <?php else: ?>
          <?php if(userLoggedIn()): ?>
          <!-- Start buttons -->  
          <!-- Button trigger modal -->
            <div class="row">
              <div class="col-sm-12 d-flex justify-content-between" id="buttons">
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteCarModal<?= $car["Id"] ?>">
                  Delete
                </button>
                <div>
                  <a href="edit.php?id=<?= $car["Id"] ?>">
                    <button type="button" class="btn btn-primary">Edit</button>
                  </a>
                  <a href="index.php">
                    <button type="button" class="btn btn-secondary">Cancel</button>
                  </a>
                </div>
                <!-- Modal -->
                <div class="modal fade" id="deleteCarModal<?= $car["Id"] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteCarModalTitle<?= $car["Id"] ?>" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Are you sure you want to delete this car?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body d-flex justify-content-around">
                        <?php $photo = getPhoto($car["Id"], $db, "index_"); ?>
                        <img id="delete-car-photo" src="photos/<?= $photo ? $car["Id"] . "/" . $photo['Name']  : 'image-placeholder.png' ?>" alt="<?= $photo ? $photo["Name"] : 'No car image available' ?>" class='image-placeholder-size'>
                        <h5><strong><?= $car["Year"] ?> <?= $car["Make"] ?> <?= $car["Model"] ?></strong></h5>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <a href='delete.php?id=<?= $car["Id"] ?>'>
                          <button type="button" class="btn btn-danger">Delete</button>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <!-- End buttons -->
          <?php endif;?>
          <!-- Start car info and price -->
          <div class="row">
            <div class="col-sm-12 d-flex align-items-center justify-content-between" id="title">
              <h3><?= $car["Year"] ?> <?= $car["Make"] ?> <?= $car["Model"] ?></h3>
              <span><strong>Price: </strong>$<?= $car["Price"] ?></span>
            </div>
          </div>
          <!-- Start car info and price -->
          
          <!-- Start photo section -->
          <div class="row justify-content-center" id="photo-section">
            <div class="col-sm-8">
              <?php $photo = getPhoto($car["Id"], $db, "featured_"); ?>
              <?php if($photo): ?>
                <img src="photos/<?= $car["Id"] ?>/<?= $photo["Name"] ?>" alt="<?= $photo["Name"] ?>">
              <?php else: ?>
                <img src="photos/image-placeholder.png" alt="No car image available" id='image-placeholder-size'>
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
          <!-- End photo section -->

          <!-- Start mileage section -->
          <div class="row">
            <div class="col-sm-12 align-items-center" id="mileage">
              <span><strong>Mileage: </strong><?= $car["Mileage"] ?> km</span>
            </div>
          </div>
          <!-- Start mileage section -->

          <!-- Start description -->
          <div class="row" id="description">
            <div class="col-sm-12" id="description-title">
              <strong>Description</strong>
            </div>
            <div class="col-sm-12" id="description-body">
              <?php if($car["Description"]): ?>
                <p><?= $car["Description"] ?></p>
              <?php else: ?>
                <p>There is no description for this car in our records</p>
              <?php endif; ?>
            </div>
          </div>
          <!-- End description -->

          <!-- Start video review -->
          <div class="row" id="video-review">
            <div class="col-sm-12" id="video-review-title">
              <strong>Video Review</strong>
            </div>
            <div class="col-sm-12" id="video-review-body">
              <?php if($car["VideoURL"]): ?>
                <div class="embed-responsive embed-responsive-4by3">
                  <iframe class="embed-responsive-item" src="<?= $car["VideoURL"] ?>" allowfullscreen></iframe>
                </div>
              <?php else: ?>
                <p>There is no Video Review for this car in our records</p>
              <?php endif; ?>
            </div>
          </div>
          <!-- End video review -->

        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>