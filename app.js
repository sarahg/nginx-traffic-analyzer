"use strict";

function runReport(type) {
  let box = document.getElementById(type);
  let path = document.getElementById("path").value;

  var xmlhttp = new XMLHttpRequest();
  xmlhttp.open("GET", "analyze.php?path=" + path + '&type=' + type);
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