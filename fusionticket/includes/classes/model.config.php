<?php

/**
 *
 *
 * @version $Id: model.config.php 1836 2012-07-26 15:33:26Z nielsNL $
 * @copyright 2011
 */
/**
 *
 *
 */
class config extends model{


  protected $_idName    = 'config_field';
  protected $_tableName = 'configuration';
  protected $_columns   = null;

  /**
   * Constructor
   */
  function __construct($filldefs= false){
    global $_SHOP;

  //  if (!isset($_SESSION['CONFIG_FIELDS']) || empty($_SESSION['CONFIG_FIELDS'])) {
     $this->_columns =  $this->loadConfig();
 //   }
   // $_SESSION['CONFIG_FIELDS'];
   // var_dump($this);
    if ($filldefs) {
      include('config'.DS.'data_config.php');
      foreach ($config as $fields){
        foreach ($fields as $key => $row ){
          if (isset($_SHOP->$key)) {
            $this->$key = $_SHOP->$key;
          } else {
            $this->$key = $row[0];
          }
        }
      }
    }
  }

  static function ApplyConfig($defaults=false) {
    global $_SHOP;
    if ($defaults) {
      include('config'.DS.'data_config.php');
      foreach ($config as $fields){
        foreach ($fields as $key => $row ){
          if (!isset($_SHOP->$key)) {
            $_SHOP->$key = $row[0];
          }
        }
      }
    }
    $tmp = ShopDB::query("select config_field, config_value from configuration");
    while ($row = ShopDB::fetch_row($tmp)) {
      $val = unserialize($row[1]);
      if (!is_null($val)) {
        $_SHOP->$row[0] = $val;
      }
    }
  //  MySQL::freeResult($tmp);
  }

  static function load(){
    $config = new config(true);
    $tmp = ShopDB::query("select config_field, config_value from configuration");
    while ($row = ShopDB::fetch_row($tmp)) {
      $val = unserialize($row[1]);
      if (!is_null($val)) {
        $config->$row[0] = $val;
      }
    }
    return $config;

  }

  static function loadConfig () {
    include('config'.DS.'data_config.php');
    $_SESSION['CONFIG_FIELDS'] = array();
    foreach ($config as $fields){
      foreach ($fields as $key => $row ){
        if (!is_array($row)) { continue; }
        if (count($row)<=2) $row[2]='';
        $_SESSION['CONFIG_FIELDS'][]=$row[2].'$'.$key;
      }
    }
    return $_SESSION['CONFIG_FIELDS'];
  }

  static function LoadAsArray($group='') {
    $cnf = new Config(true);
    $cnfa = (array)$cnf;
    array_shift($cnfa);
    array_shift($cnfa);
    array_shift($cnfa);
    $cnf->fillArrays($cnfa, true);

    return $cnfa;
  }

  static function loadOrganizer() {
    global $_SHOP;
    $cnf = array();
    foreach ($_SHOP as $key => $field){
    //  echo $key,strpos($key,'organizer_'),' ';
      if (strpos($key,'organizer_')===0) {
        $cnf[$key] = $field;
      }

    }
    return $cnf;
  }

  function fillPost($nocheck=false){
    if (!$nocheck) {
      $arr = $_POST;
      $this->fillArrays($arr, false);
      return $this->_fill($arr,$nocheck);
    } else
      parent::fillPost($nocheck);
  }

  function fillArrays(&$arr, $toString= false){

    foreach($this->_columns as $fieldname){

      if (self::getFieldtype($fieldname) & self::MDL_ARRAY) {
        if ($toString && is_array(is($arr[$fieldname],false))) {
          $result = '';
          $values = $arr[$fieldname];
          foreach($values as $key =>$value) {
            if (!is_numeric($key)) {$result .= "{$key}=";}
            $result .= $value.";";
          }
          $arr[$fieldname] = $result;
        } elseif (!$toString && is_string($arr[$fieldname])) {
          $values = str_replace("\n" ,'' ,$arr[$fieldname] );
          $values = explode(';',$values);
          $result = array();
          foreach($values as $value){
            if (!empty($value)) {
              if (strpos($value, '=')===false) {
                $result[] = $value;
              } else {
                list($id,$value) = explode('=',$value);
                $result[$id] = $value;
              }
            }
          }
          $arr[$fieldname] = $result;
        }
      }
    }
  }

  function Save($id = null, $exclude=null) {
    ShopDB::Begin('Save config');
	// ksort($this->_columns);
	//var_dump($this->_columns);
    foreach ($this->_columns as $key ){
      $key1 = $key;
      $type= self::getFieldtype($key);
      if (($type & self::MDL_SKIPSAVE)){ continue;}
      if (is_array($exclude) && in_array($key,$exclude )) { continue; }
      if ($type & self::MDL_FILE) {
         if (!$this->fillFilename ($_REQUEST, $key)) {return false; }
      } elseif (($val= $this->_set($key1, '~~~'))) {

          if (!ShopDB::query("insert into configuration (config_field, config_value) VALUES ("._esc($key).",{$val})
                           on duplicate key update config_value={$val}")) {
          return self::_abort('cant_save_configvalue', $key);
        }
      }
    }
    ShopDB::commit('Config saved');
    addNotice('configuration_is_saved');
    return true;
  }

  function update($name, $value, $esc = true){
   return self::updateFile($name, $value, $esc = true);
  }

  function updateFile($name, $value, $esc = true){
    if ($esc) {
      $value = serialize($value);
      $value = _esc($value);
    }

    if (!ShopDB::query("insert into configuration (config_field, config_value) VALUES ("._esc($name).",".$value.")
                       on duplicate key update config_value=".$value)) {
      return self::_abort('cant_save_configvalue', $name);
    }

   return true;
  }

}

?>