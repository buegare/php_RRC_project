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
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>
  <pre><?php print_r($_SESSION) ?></pre>
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
              <a href="">
                <img src="photos/<?= $photo ? $photo["Name"] : 'image-placeholder.png' ?>" alt="<?= $photo ? $photo["Name"] : 'No car image available' ?>" class='car-photo'>
              </a>
            </div>
            <div class='col-sm-7'>
              <h5><a href=""><strong><?= $car["Year"] ?> <?= $car["Make"] ?> <?= $car["Model"] ?></strong></a></h5>
              <p><?= $car["Description"] ?></p>
              <p><strong>Mileage</strong> <?= $car["Mileage"] ?> km</p>
            </div>
            <div class='col-sm-2'>
              <strong>$<?= $car["Price"] ?></strong>
              <?php if(userLoggedIn()): ?>
                <button type="submit" class='btn btn-danger'>Delete</button>
              <?php endif;?>
            </div>
          </div>
        <?php endforeach;?>
      </section>
    </div>
  </div>
</body>
</html>