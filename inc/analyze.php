<?php

init();

/**
 * Set up and build the report.
 * Echos HTML back to index.php via app.js.
 */
function init() {
  $report = getReportAttributes($_GET['type']);
  if (!is_array($report)) {
    die('Error :('); // not a valid report type
  }
  if ($report['type'] == 'ua') {
    define('BAD_USER_AGENTS', file_get_contents('../misc/bad-user-agents.list'));
  }
  // Run our designated command on the log files and push that out into an array.
  $results = preg_split("#[\r\n]+#", shell_exec($report['command']));
  echo buildResultTable($report, $results);
}

/**
 * Build a table of results from our parsing command.
 * @param array $results
 * @return string $markup
 *   An HTML table of results (╯°□°)╯︵ ┻━┻
 */
function buildResultTable(array $report, array $results) {
  $markup = '<table>';
  $markup .= '<thead><tr class="border"><th class="text-left px-2 py-2">'. implode('</th><th class="text-left px-4 py-2">', $report['header']) .'</tr></thead>';
  foreach ($results as $result) {
    if ($result) {
      $markup .= '<tr class="border">';
      $markup .= '<td class="text-left px-4 py-2">' . implode('</td><td class="text-left px-4 py-2">', buildTableRow($report['type'], $result));
      $markup .= '</tr>';
    }
  }
  $markup .= '</table>';
  return $markup;
}

/**
 * Create a table row for each IP/UA.
 * @param string $type
 * @param string $result
 * @return array
 *   Presentable data for each column in the row.
 */
function buildTableRow(string $type, string $result) {
  $count = strtok($result, ' ');
  $visitor = trim(str_replace($count, '', $result));

  if ($type == 'ip') {
    $row_data = [
      '<input type="checkbox" value="'. $visitor .'">',
      $visitor, number_format($count),
      '<a class="text-blue-500" href="inc/whois.php?ip=' . $visitor . '" target="_blank">Lookup</a>',
    ];
  }
  elseif ($type == 'ua') {
    $row_data = [$visitor, number_format($count), checkUAType($visitor)];
  }
  return $row_data;
}

/**
 * How do we feel about this user agent?
 * 
 * @param string $agent 
 * @return string
 */
function checkUAType(string $agent) {
  $icon = '🤷‍♀️';
  if (strpos($agent, BAD_USER_AGENTS)) {
    $icon = '👹'; // @todo this one isn't working
  }
  if (strpos($agent, 'bot')) {
    $icon = '🤖';
  }
  return '<span class="text-2xl">' . $icon . '</span>';
}

/**
 * Get static attributes for each report type.
 * 
 * @param $type string
 *   The report type (ip or ua).
 * @return array
 */
function getReportAttributes(string $type) {
  $reports = [
    [
      'type' => 'ip',
      'command' => "awk '{print $1}' ../logs/nginx-access.log* | sort | uniq -c | sort -nr | head -n 25",
      'header' => ['', 'IP Address', 'Visits', 'Who is this?']
    ],
    [
      'type' => 'ua',
      'command' => 'cat ../logs/nginx-access.log* | awk -F\" \'{ print $6 }\' | sort | uniq -c | sort -frn | head -n 25',
      'header' => ['User Agent', 'Visits', 'Type']
    ]
  ];
  $key = array_search($type, array_column($reports, 'type'));
  return $reports[$key];
}
