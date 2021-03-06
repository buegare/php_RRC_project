<?php
  require 'connect.php';
  require 'utils.php';

  $car_photo = "";

  $select_query = 'SELECT * FROM Car';
  $statement = $db->prepare($select_query);
  $statement->execute();
  $cars = $statement->fetchAll();

?>

<?php $js_file = "index.js"; $css_files = ["index.css"]; include("template/header.php"); ?>

  <div class='container'>
    <header class='row'>
      <div class='col-sm-8'>Geske Automotive Group</div>
      <?php if(userLoggedIn()): ?>
        <div class='col-sm-1'>
          <a href='new_car.php' class='logged-user-links'>New Car</a>
        </div>
        <div class='col-sm-2'>
          <a href='manage_users.php' class='logged-user-links'>Manage Users</a>
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

        <div class="row" id="search-bar">
          <div>
            <span>Sort By: </span>
            <select id="sort-cars-select">
              <option value="default" selected>Default</option>
              <option value="price-low-to-high">Price: Low to High</option>
              <option value="price-high-to-low">Price: High to Low</option>
              <option value="km-high-to-low">Kilometres: High to Low</option>
              <option value="km-low-to-high">Kilometres: Low to High</option>
              <option value="year-old-to-new">Year: Old to New</option>
              <option value="year-new-to-old">Year: New to Old</option>
          </select>
          </div>
        </div>

        <hr>

        <div id="car-list">
          <?php foreach ($cars as $car): ?>
            <?php
              $photo = getPhoto($car["Id"], $db, "index_");
              if($photo["Name"]) {
                $car_photo = "<img src='photos/{$car["Id"]}/{$photo["Name"]}' alt='{$photo["Name"]}' class='car-photo'>";
              } else {
                $car_photo = "<img src='photos/image-placeholder.png' alt='No car image available' class='image-placeholder-size'>";
              }
            ?>
            <div class='row car-line'>
              <div class='col-sm-3 image'>
                <a href="show.php?id=<?= $car["Id"] ?>">
                  <?= $car_photo; ?>
                </a>
              </div>
              <div class='col-sm-7 car-info'>
                <h5><a href="show.php?id=<?= $car["Id"] ?>"><strong><?= $car["Year"] ?> <?= $car["Make"] ?> <?= $car["Model"] ?></strong></a></h5>
                <p>
                  <?= strlen($car["Description"]) >= 117 ? 
                  substr($car["Description"], 0, 117) . "..." : 
                  $car["Description"] 
                  ?>
                </p>
                <p><strong>Mileage</strong> <?= $car["Mileage"] ?> km</p>
              </div>
              <div class='col-sm-2 car-price'>
                <strong>$<?= $car["Price"] ?></strong>
                <?php if(userLoggedIn()): ?>
                  <!-- Button trigger modal -->
                  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteCarModal<?= $car["Id"] ?>">
                    Delete
                  </button>

                  <!-- Modal -->
                  <div class="modal fade" id="deleteCarModal<?= $car["Id"] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteCarModal<?= $car["Id"] ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Are you sure you want to delete this car?</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body d-flex justify-content-around">
                          <?= $car_photo; ?>
                          <h5><strong><?= $car["Year"] ?> <?= $car["Make"] ?> <?= $car["Model"] ?></strong></h5>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                          <!-- <a href='delete.php?id=<?= $car["Id"] ?>'>
                            <button type="button" class="btn btn-danger">Delete</button>
                          </a> -->
                          <a class="btn btn-danger" href='delete.php?id=<?= $car["Id"] ?>'>Delete</a>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endif;?>
              </div>
            </div>
          <?php endforeach;?>
        </div>
      
      </section>
    </div>
  </div>
</body>
</html>