"use strict";

const sortCars = () => {
  let select = document.getElementById("sort-cars-select");

  let xmlhttp = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");

  let carList = document.getElementById("car-list");
  while (carList.lastElementChild && carList.children.length > 1) {
    carList.lastElementChild.remove();
  }

  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      carList.innerHTML = this.responseText;
    }
  };

  xmlhttp.open("GET", "sort_cars.php?sort_by=" + select.value, true);
  xmlhttp.send();
};

/*
 * Handles the load event of the document.
 */
function load() {
  document
    .getElementById("sort-cars-select")
    .addEventListener("change", sortCars);
}

// Add document load event listener
document.addEventListener("DOMContentLoaded", load, false);
