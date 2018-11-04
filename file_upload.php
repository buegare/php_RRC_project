<?php
  function uploadPhoto() {
    $target_dir = "photos/";
    $target_file = $target_dir . basename($_FILES["photo"]["name"]);
    $image_file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if($check === false) {
      throw new Exception('File is not an image.');
    }

    // // Check if file already exists
    // if (file_exists($target_file)) {
    //     echo "Sorry, file already exists.";
    //     $uploadOk = 0;
    // }
    // // Check file size
    // if ($_FILES["photo"]["size"] > 500000) {
    //     echo "Sorry, your file is too large.";
    //     $uploadOk = 0;
    // }

    // Allow certain file formats
    $allowed_file_extensions = ['jpg', 'jpeg', 'png'];
    if(!in_array($image_file_type, $allowed_file_extensions)) {
      throw new Exception('Sorry, only JPG, JPEG or PNG files are allowed.');
    }

    if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
      throw new Exception('Sorry, there was an error uploading your file.');
    }
  }
?>