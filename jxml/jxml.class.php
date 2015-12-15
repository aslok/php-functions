<?php

function log_message($level, $msg, $flag)
{
  $cur_level = 'info';
  if ($level != $cur_level) {
    return false;
  }
  echo $msg . "<br /><br />";
}

class jxml {
  private $el;
  private $log_level;
  private $parents;

  public function __construct(){
    $this->el = array ();
    $this->parents = array ();
    $this->log_level = 'error';
  }

  public function get_data($xml){
    $this->el = array ();
    $this->parse($xml);
    return $this->el['chl'];
  }

  private function parse($xml){
    if (!$xml) {
      return false;
    }
    $xml_parser = xml_parser_create();
    xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
    xml_set_element_handler($xml_parser,
                            array (&$this, 'startElement'),
                            array (&$this, 'endElement'));
    xml_set_character_data_handler($xml_parser,
                                   array (&$this, 'dataElement'));
    if (!xml_parse($xml_parser, $xml))
    {
      log_message($this->log_level, 'Ошибка парсинга xml', true);
      return false;
    }
    xml_parser_free($xml_parser);
    return true;
  }

  private function startElement($parser, $name, $attrs){
    log_message($this->log_level, 'Начинаем элемент ' . $name, true);
    $this->parents[] =& $this->el;
    $count = 2;
    $orig_name = $name;
    while (isset ($this->el['chl'][$name]) && $count < 100000) {
      $name = $orig_name . '_' . $count;
      $count++;
    }
    if ($count > 99999) {
      $name .= '_' . $count;
    }
    $this->el['chl'][$name] = array ();
    $this->el =& $this->el['chl'][$name];
    $this->el['cnt'] = '';
    $this->el['atr'] = $attrs;
  }

  private function endElement($parser, $name){
    log_message($this->log_level, 'Заканчиваем элемент ' . $name, true);
    $this->el =& $this->parents[count($this->parents) - 1];
    array_pop($this->parents);
  }

  private function dataElement($parser, $content){
    log_message($this->log_level, 'Обрабатываем данные элемента', true);
    $this->el['cnt'] = trim($content);
  }
}

?>
