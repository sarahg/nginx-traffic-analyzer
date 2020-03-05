"use strict";

/**
 * AJAX handler
 * @param {string} type 
 */
const runReport = type => {
  let box = document.getElementById(type);
  let path = document.getElementById("path").value;

  var xmlhttp = new XMLHttpRequest();
  xmlhttp.open("GET", "inc/analyze.php?path=" + path + '&type=' + type);
  xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xmlhttp.send();

  box.innerHTML = "Loading..."; // @todo GIF this up

  xmlhttp.onreadystatechange = function() {
    if (this.readyState === 4 && this.status === 200) {
      box.innerHTML = this.responseText;
    } else {
      box.innerHTML = "Error :(";
    }
  };
}

/**
 * Update the "Block IPs" code snippet based on
 * values marked in the checkboxes.
 * 
 * @todo
 */
const updateBlockArray = () => {}