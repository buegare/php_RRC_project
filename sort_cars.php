<?php
  require 'connect.php';
  require 'utils.php';
  require 'validate_form.php';

  $sanitized_sort_option = sanitizeString($_GET["sort_by"]);
  $select_query = "";
  $car_photo = "";

  switch ($sanitized_sort_option) {
    case "price-low-to-high":
      $select_query = 'SELECT * FROM Car ORDER BY Price';
      break;
    case "price-high-to-low":
      $select_query = 'SELECT * FROM Car ORDER BY Price DESC';
      break;
    case "km-high-to-low":
      $select_query = 'SELECT * FROM Car ORDER BY Mileage';
      break;
    case "km-low-to-high":
      $select_query = 'SELECT * FROM Car ORDER BY Mileage DESC';
      break;
    case "year-old-to-new":
      $select_query = 'SELECT * FROM Car ORDER BY Year';
      break;
    case "year-new-to-old":
      $select_query = 'SELECT * FROM Car ORDER BY Year DESC';
      break;
    default:
      $select_query = 'SELECT * FROM Car';
  }

  $statement = $db->prepare($select_query);
  $statement->execute();
  $cars = $statement->fetchAll();

  foreach ($cars as $car) {
    $photo = getPhoto($car["Id"], $db, "index_");
    if($photo["Name"]) {
      $car_photo = "<img src='photos/{$car["Id"]}/{$photo["Name"]}' alt='{$photo["Name"]}' class='car-photo'>";
    } else {
      $car_photo = "<img src='photos/image-placeholder.png' alt='No car image available' class='car-photo image-placeholder-size'>";
    }
    echo "<div class='row car-line'>";
    echo "<div class='col-sm-3 image'>";
    echo "<a href='show.php?id={$car["Id"]}'>";
    echo $car_photo;
    echo "</a>";
    echo "</div>";
    echo "<div class='col-sm-7 car-info'>";
    echo "<h5><a href='show.php?id={$car["Id"]}'><strong>{$car["Year"]} {$car["Make"]} {$car["Model"]}</strong></a></h5>";
    echo "<p>";
    echo strlen($car["Description"]) >= 117 ? substr($car["Description"], 0, 117) . "..." : $car["Description"];
    echo "</p>";
    echo "<p><strong>Mileage</strong> {$car["Mileage"]} km</p>";
    echo "</div>";
    echo "<div class='col-sm-2' id='car-price'>";
    echo "<strong>$ {$car["Price"]}</strong>";
    if(userLoggedIn()) {
      echo "<!-- Button trigger modal -->";
      echo "<button type='button' class='btn btn-danger' data-toggle='modal' data-target='#deleteCarModal{$car["Id"]}'>Delete</button>";
      echo "<!-- Modal -->";
      echo "<div class='modal fade' id='deleteCarModal{$car["Id"]}' tabindex='-1' role='dialog' aria-labelledby='deleteCarModalTitle{$car["Id"]}' aria-hidden='true'>";
      echo "<div class='modal-dialog modal-dialog-centered' role='document'>";
      echo "<div class='modal-content'>";
      echo "<div class='modal-header'>";
      echo "<h5 class='modal-title' id='exampleModalLongTitle'>Are you sure you want to delete this car?</h5>";
      echo "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>";
      echo "<span aria-hidden='true'>&times;</span>";
      echo "</button>";
      echo "</div>";
      echo "<div class='modal-body d-flex justify-content-around'>";
      echo $car_photo;
      echo "<h5><strong>{$car["Year"]} {$car["Make"]} {$car["Model"]}</strong></h5>";
      echo "</div>";
      echo "<div class='modal-footer'>";
      echo "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>";
      echo "<a href='delete.php?id={$car["Id"]}'>
                <button type='button' class='btn btn-danger'>Delete</button>
              </a>
            </div>
          </div>
        </div>
      </div>";

    }
    echo "</div>";
    echo "</div>";
  }
?>