<?php
  require 'vendor/gumlet/php-image-resize/lib/ImageResize.php';

  use Gumlet\ImageResize;
  use Gumlet\ImageResizeException;

  function imageResize($original_image, $size, $new_filename) {
    echo "Original image {$original_image} <br>";
    echo "new file name {$new_filename} <br>";
    $image = new ImageResize($original_image);
    $image->resizeToHeight($size);
    $image->save('photos/' . $new_filename);
  }

  function uploadPhoto() {
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

    // Upload files only if all photos are valid
    foreach ($photos as $photo) {
      $target_file = $target_dir . basename($photo["name"]);
      // move_uploaded_file($photo["tmp_name"], $target_file);
      imageResize($photo["tmp_name"], 90, "index_" . basename($photo["name"]));
      imageResize($photo["tmp_name"], 309, "featured_" . basename($photo["name"]));
      imageResize($photo["tmp_name"], 72, "thumbnail_" . basename($photo["name"]));
    }

    // // Check file size
    // if ($_FILES["photo"]["size"] > 500000) {
    //     echo "Sorry, your file is too large.";
    //     $uploadOk = 0;
    // }
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