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

  $select_photos_query = 'SELECT Name FROM Photo p, Car c WHERE p.CarId = c.Id AND c.Id = ' . $id;
  $statement = $db->prepare($select_photos_query);
  $statement->execute();
  $photos = $statement->fetchAll();
  
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Geske Automotive Group</title>
  <link rel="stylesheet" href="styles/show.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</head>
<body>
  <!-- <pre><?php print_r($car);?></pre>
  <pre><?php print_r($photos);?></pre> -->
  <div class="container">
    <div class="row" id="wrapper">
      <div class="col-sm-12">
        <?php if(!$car): ?>
          <h3>There is no car with this id</h3>
        <?php else: ?>
          <?php if(userLoggedIn()): ?>
            <!-- Button trigger modal -->
            <div class="row">
              <div class="col-sm-12 d-flex justify-content-end" id="buttons">
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteCarModal<?= $car["Id"] ?>">
                  Delete
                </button>

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
                        <img id="delete-car-photo" src="photos/<?= $car["Id"] ?>/<?= $photos ? $photos[0]["Name"] : 'image-placeholder.png' ?>" alt="<?= $photos ? $photos[0]["Name"] : 'No car image available' ?>" class='car-photo'>
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
          <?php endif;?>
          <div class="row">
            <div class="col-sm-12 d-flex align-items-center justify-content-between" id="title">
              <h3><?= $car["Year"] ?> <?= $car["Make"] ?> <?= $car["Model"] ?></h3>
              <span><strong>Price: </strong>$<?= $car["Price"] ?></span>
            </div>
          </div>
          <div class="row justify-content-center" id="photo-section">
            <div class="col-sm-8">
              <img id="car-photo-featured" src="photos/<?= $car["Id"] ?>/<?= $photos[0]["Name"] ?>" alt="<?= $photos[0]["Name"] ?>">
            </div>
            <div class="col-sm-4">
              <?php for ($i=1; $i < count($photos); $i++): ?> 
                <img class="car-photos" src="photos/<?= $car["Id"] ?>/<?= $photos[$i]["Name"] ?>" alt="<?= $photos[$i]["Name"] ?>">
              <?php endfor; ?>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12 align-items-center" id="mileage">
              <span><strong>Mileage: </strong><?= $car["Mileage"] ?> km</span>
            </div>
          </div>
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
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>