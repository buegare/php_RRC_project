<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Geske Automotive Group</title>
  <?php foreach ($css_files as $css) {
    echo "<link rel='stylesheet' href='styles/{$css}'>";
  }?>
  <link rel="stylesheet" href="public/assets/bootstrap.min.css">
  <script src="public/assets/jquery-slim.min.js"></script>
  <script src="public/assets/popper.min.js"></script>
  <script src="public/assets/bootstrap.min.js"></script>
  <?php if ($js_file) {
    echo "<script src='js/{$js_file}'></script>";
  }?>
</head>
<body>