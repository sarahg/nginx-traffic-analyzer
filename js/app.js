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
      updateIPBlockCode();
    } else {
      box.innerHTML = "Error :(";
    }
  };

}

/**
 * Update the "Block IPs" code snippet based on
 * values marked in the checkboxes.
 */
const updateIPBlockCode = () => {
  const element = document.querySelectorAll("td.checkbox");
  let selected = [];
  element.forEach(function(el) {
    el.addEventListener('click', ()=> {
      console.log('click');
      // @todo get values of all checked boxes
      // add that to the PHP code snippet
      // and show the code snippet box
      // hide if no boxes checked
    });
  });
}