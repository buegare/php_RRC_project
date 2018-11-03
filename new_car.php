<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Geske Automotive Group</title>
  <link rel="stylesheet" href="styles/new_car.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

</head>
<body>
  <div class='container'>
      <div class="row">
        <form method="post">
          <label for="make">Make</label>
          <input type="text" name="make" id="make">
          <label for="model">Model</label>
          <input type="text" name="model" id="model">
          <label for="year">Year</label>
          <input type="number" name="year" id="year">
          <label for="mileage">Mileage</label>
          <input type="number" name="mileage" id="mileage">
          <label for="price">Price</label>
          <input type="number" name="price" id="price">
          <label for="video-url">Video URL</label>
          <input type="url" name="video-url" id="video-url">
          <input type="image" src="" alt="">
          <label for="description">Description</label>
          <textarea name="description" id="description" cols="30" rows="10"></textarea>
        </form>
      </div>
  </div>
</body>
</html>