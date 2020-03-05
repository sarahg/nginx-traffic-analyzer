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
  echo buildResultPanel($report, $results);
}

/**
 * Create a table row for each IP/UA.
 */
function buildTableRow($type, $result) {
  
  // Wrangle the result string into usable bits.
  $count = strtok($result, ' ');
  $visitor = trim(str_replace($count, '', $result));

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
      checkRep($visitor)
    ];
  }

  return $row_data;
}

/**
 * How do we feel about this user agent?
 * @param $agent string
 * @return string
 */
function checkRep($agent) {
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

/**
 * Build a table of results from our parsing command.
 * @param array $results
 * @return string $markup
 *   An HTML table of results (╯°□°)╯︵ ┻━┻
 *   and a dynamic IP blocking code snippet for the IP report.
 */
function buildResultPanel($report, $results) {
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
  if ($report['type'] == 'ip') {
    $markup .= IPBlockCodeSnippet();
  }
  return $markup;
}

/**
 * Returns markup for the IP Block code snippet.
 */
function IPBlockCodeSnippet() {
  $markup  = '<div class="block-snippet mt-8">';
  $markup .= '<h3 class="text-xl">Block IPs</h3>';
  $markup .= '<p class="mt-1">Select IPs above to generate PHP code.</p>';
  $markup .= '<pre class="invisible mt-3 border p-3">';
  $markup .= '$deny = array(<span id="blockIPs"></span>);' . "\n";
  $markup .= 'if (in_array ($_SERVER["REMOTE_ADDR"], $deny)) {' . "\n";
  $markup .= '  die("Forbiden");' . "\n";
  $markup .= '}';
  $markup .= '</pre>';
  //$markup .= '<p><a href="inc/help.php#what">What do I do with this?</a></p>'; //@todo build help.php
  $markup .= '</div>';
  return $markup;
}