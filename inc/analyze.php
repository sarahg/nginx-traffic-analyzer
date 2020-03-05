<?php

// Make sure we know how to run this report.
$report = reportAttributes($_GET['type']);
if (!is_array($report)) {
  echo('Error :(');
}

define('BLOCK_LIST', file_get_contents('../misc/bad-user-agents.list'));

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
 * 
 * @todo refactor this, the two reports have diverged too much, too much logic in here
 * @todo make the table less ugly https://tailwindcss.com/docs/table-layout/
 */
function buildResultTable($report, $results) {

  $markup = '<table>';
  $markup .= '<thead><tr class="border"><th class="text-left px-2 py-2">'. implode('</th><th class="text-left px-4 py-2">', $report['header']) .'</tr></thead>';

  foreach ($results as $result) {

    // Wrangle the string into usable bits.
    $count = strtok($result, ' ');
    $visitor = str_replace($count, '', $result);

    if ($count) {
      $markup .= '<tr class="border">';
      if ($report['type'] == 'ip') {
        $markup .= '<td><input type="checkbox"></td>'; 
      }

      $markup .= '<td>' . $visitor . '</td>';
      // @todo it'd be nice to visualize $count with a percentage bar
      $markup .= '<td>' . number_format($count) . '</td>';

      if ($report['type'] == 'ip') {
        $markup .= '<td><a class="text-blue-500" href="'. $report['link']['url'] . trim($visitor) . '" target="_blank">' . $report['link']['label'] . '</a></td>';
      }
      else {
        $markup .= '<td class="text-2xl">' . checkRep($visitor) . '</td>';
      }

      $markup .= '</tr>';
    }
  }
  $markup .= '</table>';

  if ($report['type'] == 'ip') {
    $markup .= '<div class="block-snippet invisible">';
    $markup .= '<p class="mt-5">Select IPs to generate blocking code</p>';

    // @todo show when boxes are checked; populate array() with checked item values
    $markup .= '<pre>';
    $markup .= htmlspecialchars('$deny = array(); if (in_array ($_SERVER["REMOTE_ADDR"], $deny)) { die("Forbiden"); }');
    $markup .= '</pre>';
    $markup .= '<p><a href="inc/help.php#what">What do I do with this?</a></p>'; //@todo build help.php

    $markup .= '</div>';
  }

  return $markup;
}

/**
 * Report attributes.
 */
function reportAttributes($type) {
  $reports = [
    [
      'type' => 'ip',
      'header' => ['', 'IP Address', 'Visits', 'Who is this?'],
      'command' => "awk '{print $1}' ../logs/nginx-access.log* | sort | uniq -c | sort -nr | head -n 25",
      'link' => ['url' => 'inc/whois.php?ip=', 'label' => 'lookup']
    ],
    [
      'type' => 'ua',
      'header' => ['User Agent', 'Visits', 'Type'],
      'command' => 'cat ../logs/nginx-access.log* | awk -F\" \'{ print $6 }\' | sort | uniq -c | sort -frn | head -n 25',
    ]
  ];
  $key = array_search($type, array_column($reports, 'type'));
  return $reports[$key];
}

/**
 * How do we feel about this user agent?
 * 
 * @param $agent string
 * @return string
 */
function checkRep($agent) {
  // @todo this one isn't working
  if (strpos($agent, BLOCK_LIST)) {
    return '👹';
  }
  if (strpos($agent, 'bot')) {
    return '🤖';
  }
  return '🤷‍♀️';
}