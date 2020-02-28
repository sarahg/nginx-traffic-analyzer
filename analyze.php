<?php

/**
 * Set a few variables based on which report was requested.
 */
switch ($_GET['type']) {
  case 'ip-list':
    $header = 'IP Address';
    $command = "awk '{print $1}' logs/nginx-access.log* | sort | uniq -c | sort -nr | head -n 25";
  break;
  case 'ua-list':
    $header = 'User Agent';
    $command = 'cat logs/nginx-access.log* | awk -F\" \'{ print $6 }\' | sort | uniq -c | sort -frn | head -n 25';
  break;
  default: exit('Error :(');
}

// Run a shell command to parse the logs.
$str = shell_exec($command);

// Break the shell_exec string output into an array for easier formatting.
$results = preg_split("#[\r\n]+#", $str);

// Send HTML back to app.js.
echo buildResultTable($header, $results);

/**
 * Build a table of results from our parsing command.
 * @param array $results
 * @return string $markup
 *   An HTML table of results (╯°□°)╯︵ ┻━┻
 */
function buildResultTable($header, $results) {
  $markup = '<table>';
  $markup .= '<thead><tr><th>'. $header .'</th><th>Visits</th></tr></thead>';
  foreach ($results as $result) {
    // Wrangle the string into usable bits.
    $count = strtok($result, ' ');
    $visitor = str_replace($count, '', $result);

    if ($count) {
      $markup .= '<tr>';
      // $markup .= '<td><input type="checkbox"></td>'; 
      // @todo ^ 
      $markup .= '<td>' . $visitor . '</td><td>' . number_format($count) . '</td>';
      // @todo it'd be nice to visualize this with a percentage bar
      $markup .= '</tr>';
    }
  }
  $markup .= '</table>';
  return $markup;
}