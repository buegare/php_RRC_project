<?php
  require 'connect.php';
  require 'utils.php';

  $select_query = 'SELECT * FROM car';
  $statement = $db->prepare($select_query);
  $statement->execute();
  $cars = $statement->fetchAll();

  function getPhoto($id, $db) {
    $select_query = 'SELECT Name FROM Photo p, Car c WHERE p.CarId = c.Id AND c.Id = ' . $id . ' LIMIT 1';
    $statement = $db->prepare($select_query);
    $statement->execute();
    $photo = $statement->fetch();
    return $photo;
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Geske Automotive Group</title>
  <link rel="stylesheet" href="styles/index.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</head>
<body>
  <div class='container'>
    <header class='row'>
      <div class='col-sm-2'>Geske Automotive Group</div>
      <div class='col-sm-8'>
        <ul id='header-car-filter'>
          <li>Ford</li>
          <li>Honda</li>
          <li>Toyota</li>
          <li>Chevrolet</li>
        </ul>
      </div>
      <?php if(userLoggedIn()): ?>
        <div class='col-sm-1'>
          <a href='new_car.php' class='logged-user-links'>New Car</a>
        </div>
        <div class='col-sm-1'>
          <a href='logout.php' class='logged-user-links'>Logout</a>
        </div>
      <?php endif;?>
    </header>

    <div class='row' id='content'>
      <aside class='col-sm-3' id='aside'>
        <ul id='side-menu-filter'>
          <li class="d-flex align-items-center">
            <strong><?= count($cars) ?> found</strong>
            <span>
              <button class="btn btn-primary btn-sm">Clear</button>
            </span>
          </li>
          <li>Make<span>Any</span></li>
          <li>Model<span>Any</span></li>
          <li>Year<span>Any</span></li>
          <li>Price<span>Any</span></li>
          <li>Has Photos<span>Any</span></li>
        </ul>
      </aside>

      <section class='col-sm-9' id='section'>
        <?php foreach ($cars as $car): ?>
          <div class='row' id='car-line'>
            <?php $photo = getPhoto($car["Id"], $db)?>
            <div class='col-sm-3' id='image'>
              <a href="show.php?id=<?= $car["Id"] ?>">
                <?php if($photo): ?>
                  <img src="photos/<?= $car["Id"] ?>/<?= $photo["Name"] ?>" alt="<?= $photo["Name"] ?>" class='car-photo'>
                <?php else: ?>
                  <img src="photos/image-placeholder.png" alt="No car image available" class='car-photo'>
                <?php endif; ?>
              </a>
            </div>
            <div class='col-sm-7' id='car-info'>
              <h5><a href="show.php?id=<?= $car["Id"] ?>"><strong><?= $car["Year"] ?> <?= $car["Make"] ?> <?= $car["Model"] ?></strong></a></h5>
              <p>
                <?= strlen($car["Description"]) >= 117 ? 
                substr($car["Description"], 0, 117) . "..." : 
                $car["Description"] 
                ?>
              </p>
              <p><strong>Mileage</strong> <?= $car["Mileage"] ?> km</p>
            </div>
            <div class='col-sm-2' id="car-price">
              <strong>$<?= $car["Price"] ?></strong>
              <?php if(userLoggedIn()): ?>
                <!-- Button trigger modal -->
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
                        <img src="photos/<?= $car["Id"] ?>/<?= $photo ? $photo["Name"] : 'image-placeholder.png' ?>" alt="<?= $photo ? $photo["Name"] : 'No car image available' ?>" class='car-photo'>
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
              <?php endif;?>
            </div>
          </div>
        <?php endforeach;?>
      </section>
    </div>
  </div>
</body>
</html>