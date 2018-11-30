"use strict";

const enableApplyButton = () => {
  let applyButton = document.getElementById("apply-btn");
  applyButton.classList.remove("disabled");
};

const applyNewURL = () => {
  let iframe = document.getElementById("video-review-iframe");
  let url = document.getElementById("video-review-url").value;
  iframe.src = url;
};

const preview_images = () => {
  let total_file = document.getElementById("photo").files.length;

  // Remove featured car photo
  document.getElementById("car-photo-featured").remove();

  // Remove thumbnail photos
  let photo_section = document.getElementById("photo-thumbnail");

  while (photo_section.firstChild) {
    photo_section.removeChild(photo_section.firstChild);
  }

  let error = document.getElementsByClassName("error")[0];

  if (error) {
    error.remove();
  }

  //Append uploaded featured car photo
  $("#car-photo-featured-section").append(
    "<img class='img-fluid' id='car-photo-featured' class='image-placeholder-size' alt='uploaded_photo' src='" +
      URL.createObjectURL(event.target.files[0]) +
      "'>"
  );

  // Append uploaded thumbnail photos
  for (let i = 1; i < total_file; i++) {
    $("#photo-thumbnail").append(
      "<img class='img-fluid car-photos' alt='uploaded_photo' src='" +
        URL.createObjectURL(event.target.files[i]) +
        "'>"
    );
  }
};

/*
 * Handles the load event of the document.
 */
function load() {
  document
    .getElementById("video-review-url")
    .addEventListener("keyup", enableApplyButton);

  document.getElementById("apply-btn").addEventListener("click", applyNewURL);
  document.getElementById("photo").addEventListener("change", preview_images);
}

// Add document load event listener
document.addEventListener("DOMContentLoaded", load, false);
