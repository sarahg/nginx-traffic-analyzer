<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>nginx traffic analyzer | help</title>
  <link rel="shortcut icon" href="../favicon.ico" type="image/vnd.microsoft.icon">
</head>
<body style="font-family: 'Helvetica Neue'; padding: 0 2em 2em; max-width: 960px; margin: 0 auto;">
  <?php
      require('../vendor/parsedown-1.7.4/Parsedown.php');
      $helpfile = file_get_contents('../README.md');
      $Parsedown = new Parsedown();
      echo $Parsedown->text($helpfile);
  ?>
</body>