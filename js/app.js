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
      if (type == 'ip') {
        document.getElementById('blockBox').classList.remove('hidden');
        updateIPBlockCode();
        new ClipboardJS('.copy');
      }
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
  document.querySelectorAll('td input').forEach(function(el) {
    el.addEventListener('click', ()=> {
      let selected = [];
      let boxes = document.querySelectorAll(':checked');
      boxes.forEach(function(box) {
        selected.push('&nbsp;&nbsp;"' + box.value + '"');
      });
      document.getElementById('blockIPs').innerHTML = '\n' + selected.join(', \n') + '<br>';
      document.querySelector('.codeblock').classList.toggle('invisible', !boxes.length);
    });
  });
}