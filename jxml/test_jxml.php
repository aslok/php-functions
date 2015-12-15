<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
  </head>
  <body>
    <pre>
<?php
define('BASE_PATH', dirname(__FILE__) . '/');
require_once BASE_PATH . 'jxml.class.php';

$data = &file_get_contents('test.xml');
$xml = &new jxml();
$xml_result = &$xml->get_data($data);

$tables_content = &$xml_result['Workbook']['chl'];

$result_arr = array ();
$result_pos = 0;

// Обходим теги Worksheet
foreach ($tables_content as $table_name => &$table) {
  if (0 !== strpos($table_name, 'Worksheet')) {
    continue;
  }

  $result_arr[$table['atr']['ss:Name']] = array ();
  $table_content = &$table['chl']['Table']['chl'];

  // Обходим теги Row
  foreach ($table_content as $tag => &$tag_arr) {
    if (0 !== strpos($tag, 'Row')) {
      continue;
    }

    if (isset ($tag_arr['chl']['Cell']['atr']['ss:StyleID'])) {
      // Сбрасываем индексы
      $index_flag = true;
      $index_arr = array ();
    }

    // Значения строки - если они есть
    $row_values = array ();
    $result_pos = 0;

    foreach ($tag_arr['chl'] as $cell_tag => &$cell_tag_arr) {
      if (0 !== strpos($cell_tag, 'Cell')) {
        continue;
      }
      $value = &$cell_tag_arr['chl']['Data']['cnt'];
      if (!$index_flag) {
        $row_values[$index_arr[$result_pos]] = &$value;
      } else {
        $index_arr[$result_pos] = $value;
      }
      $result_pos++;
    }

    $index_flag = false;

    if (count ($row_values)) {
      $result_arr[$table['atr']['ss:Name']][] = &$row_values;
    }
  }
}

var_dump($result_arr);

?>
    </pre>
  </body>
</html>
