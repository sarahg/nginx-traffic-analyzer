<?php

// Load static attributes for this report. 
// Bail if we can't find any.
$report = reportAttributes($_GET['type']);
if (!is_array($report)) {
  echo('Error :(');
}

// Load the Bad User Agents list if needed.
if ($report['type'] == 'ua') {
  define('BAD_USER_AGENTS', file_get_contents('../misc/bad-user-agents.list'));
}

// Build the report.
build($report);

/**
 * Parse our logs and output HTML.
 * @param $report Array of report attributes
 * @return void
 *   Echos HTML back to index.php.
 */
function build($report) {
  $str = shell_exec($report['command']);
  $results = preg_split("#[\r\n]+#", $str);
  echo buildResultTable($report, $results);
}

/**
 * Create a table row for each IP/UA.
 */
function buildTableRow($type, $result) {
  $count = strtok($result, ' ');
  $visitor = trim(str_replace($count, '', $result));
  // Build rows.
  if ($type == 'ip') {
    $row_data = [
      '<input type="checkbox" value="'. $visitor .'">',
      $visitor,
      number_format($count),
      '<a class="text-blue-500" href="inc/whois.php?ip=' . $visitor . '" target="_blank">Lookup</a>',
    ];
  }
  elseif ($type == 'ua') {
    $row_data = [
      $visitor,
      number_format($count),
      checkUAType($visitor)
    ];
  }
  return $row_data;
}

/**
 * Build a table of results from our parsing command.
 * @param array $results
 * @return string $markup
 *   An HTML table of results (╯°□°)╯︵ ┻━┻
 */
function buildResultTable($report, $results) {
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
 * How do we feel about this user agent?
 * @param $agent string
 * @return string
 */
function checkUAType($agent) {
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
 * Static attributes for each report type.
 */
function reportAttributes($type) {
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
