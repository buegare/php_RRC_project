"use strict";

const preview_images = () => {
  let total_file = document.getElementById("photo").files.length;
  for (let i = 0; i < total_file; i++) {
    $("#photo_preview").append(
      "<div><img class='img-responsive' src='" +
        URL.createObjectURL(event.target.files[i]) +
        "'></div>"
    );
  }
};

/*
 * Handles the load event of the document.
 */
function load() {
  document.getElementById("photo").addEventListener("change", preview_images);
}

// Add document load event listener
document.addEventListener("DOMContentLoaded", load, false);
