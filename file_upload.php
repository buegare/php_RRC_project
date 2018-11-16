<?php
  require 'vendor/gumlet/php-image-resize/lib/ImageResize.php';
  require 'vendor/gumlet/php-image-resize/lib/ImageResizeException.php';

  use Gumlet\ImageResize;
  use Gumlet\ImageResizeException;

  function imageResize($original_image, $size, $new_filename, $car_id) {
    $image = new ImageResize($original_image);
    $image->resizeToHeight($size);
    $image->save("photos/{$car_id}/{$new_filename}");
  }

  function uploadPhoto($car_id) {
    require 'connect.php';

    $target_dir = "photos/";
    $allowed_file_extensions = ['jpg', 'jpeg', 'png'];

    $photos = reArrayFiles($_FILES["photo"]);

    foreach ($photos as $photo) {
      $target_file = $target_dir . basename($photo["name"]);
      $image_file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

      // Check if image file is a actual image or fake image
      $check = getimagesize($photo["tmp_name"]);
      if($check === false) {
        throw new Exception('File ' . $photo["name"] . ' is not an image.');
      }

      // Allow certain file formats
      if(!in_array($image_file_type, $allowed_file_extensions)) {
        throw new Exception('Sorry, only JPG, JPEG or PNG files are allowed.');
      }

      // Check if there's is any other errors
      if($photo["error"] > 0) {
        throw new Exception('Sorry, there was an error uploading your file. File: ' . $photo["name"]);
      }
    }

    // When creating a new car, create directory with the id of the car
    if(!file_exists("photos/$car_id")) {
      mkdir("photos/$car_id");
    }
    
    // Upload files only if all photos are valid
    $photo_basename_sanitized = sanitizeString(basename($photos[0]["name"]));
    $photo_temp_name_sanitized = sanitizeString($photos[0]["tmp_name"]);
    imageResize($photo_temp_name_sanitized, 90, "index_" . $photo_basename_sanitized, $car_id);
    insertIntoDatabase("index_" . $photo_basename_sanitized, $car_id, $db);
    imageResize($photo_temp_name_sanitized, 309, "featured_" . $photo_basename_sanitized, $car_id);
    insertIntoDatabase("featured_" . $photo_basename_sanitized, $car_id, $db);

    // upload thumbnail photos
   for ($i=1; $i < count($photos); $i++) { 
    $photo_basename_sanitized = sanitizeString(basename($photos[$i]["name"]));
    $photo_temp_name_sanitized = sanitizeString($photos[$i]["tmp_name"]);
    imageResize($photo_temp_name_sanitized, 72, "thumbnail_" . $photo_basename_sanitized, $car_id);
    insertIntoDatabase("thumbnail_" . $photo_basename_sanitized, $car_id, $db);
   }
  }

  function insertIntoDatabase($photo, $car_id, $db) {
    $query_insert_photo = "INSERT INTO photo (CarId, Name) values (:CarId, :Name)";
    $statement = $db->prepare($query_insert_photo);
    $bind_values = [':CarId' => $car_id, ':Name' => $photo];
    $statement->execute($bind_values);
  }

  function reArrayFiles($file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
  }
?>