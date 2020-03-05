<?php

// Make sure we know how to run this report.
$report = reportAttributes($_GET['type']);
if (!is_array($report)) {
  echo('Error :(');
}

// Run a shell command to parse the logs.
$str = shell_exec($report['command']);

// Break the shell_exec string output into an array for easier formatting.
$results = preg_split("#[\r\n]+#", $str);

// Return HTML for the results panels.
echo buildResultTable($report, $results);

/**
 * Build a table of results from our parsing command.
 * @param array $results
 * @return string $markup
 *   An HTML table of results (╯°□°)╯︵ ┻━┻
 */
function buildResultTable($report, $results) {

  $markup = '<table>';
  $markup .= '<thead><tr><th>'. implode('</th><th>', $report['header']) .'</tr></thead>';
  foreach ($results as $result) {

    // Wrangle the string into usable bits.
    $count = strtok($result, ' ');
    $visitor = str_replace($count, '', $result);

    if ($count) {
      $markup .= '<tr>';
      /*if ($_GET['type'] == 'ip') {
        $markup .= '<td><input type="checkbox"></td>'; 
      } @todo */
      $markup .= '<td>' . $visitor . '</td>';
      $markup .= '<td>' . number_format($count) . '</td>';
      $markup .= '<td><a href="'. $report['action']['url'] . trim($visitor) . '" target="_blank">' . $report['action']['label'] . '</a></td>';

      // if UAs
      // Check against 
      // https://github.com/mitchellkrogza/nginx-ultimate-bad-bot-blocker/blob/master/_generator_lists/bad-user-agents.list

      // @todo it'd be nice to visualize this with a percentage bar
      $markup .= '</tr>';
    }
  }
  $markup .= '</table>';
  return $markup;
}

/**
 * Report attributes.
 */
function reportAttributes($type) {
  $reports = [
    'ip' => [
      'header' => [/*'',*/ 'IP Address', 'Visits', 'Lookup'],
      'command' => "awk '{print $1}' logs/nginx-access.log* | sort | uniq -c | sort -nr | head -n 25",
      'action' => ['label' => 'lookup', 'url' => 'whois.php?ip='],
    ],
    'ua' => [
      'header' => ['User Agent', 'Visits', 'Reputation'],
      'command' => 'cat logs/nginx-access.log* | awk -F\" \'{ print $6 }\' | sort | uniq -c | sort -frn | head -n 25',
      'action' => ['label' => '', 'url' => '']
    ]
  ];
  return $reports[$type];
}