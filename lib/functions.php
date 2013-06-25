<?php

/**
 * SimplifyPHP Framework
 *
 * This file is part of SimplifyPHP Framework.
 *
 * SimplifyPHP Framework is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * SimplifyPHP Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>
 * @copyright Copyright 2008 Rodrigo Rutkoski Rodrigues
 */

/**
 *
 * @return Simplify_HtmlElement
 */
function e($e = null, $attrs = array())
{
  if ($e instanceof Simplify_HtmlElement) {
    $e->attr($attrs);
    return $e;
  }

  return new Simplify_HtmlElement($e, $attrs);
}

/**
 * Register an autoload function using spl_autoload_register()
 * but make sure __autoload keeps working
 *
 * @param mixed $func valid PHP callback
 */
function sy_autoload_register($func)
{
  if (function_exists('__autoload')) {
    spl_autoload_register('__autoload');
  }

  spl_autoload_register($func);
}

/**
 * Truncate string and add ...
 *
 * @param string $string the string
 * @param string $length desired length
 * @param string $trail trailling ... or other string
 * @param int $break use -1 to break before work, 1 to break after word or 0 to break at length
 * @return string truncated string
 */
function sy_truncate($string, $length = 80, $trail = '...', $break = -1, $breakstr = ' .,;-:!?')
{
  if (strlen(utf8_decode($string)) <= $length) {
    return $string;
  }

  $string = utf8_decode($string);
  $string = strip_tags($string);

  if ($break == -1) {
    while ($length > 0 && false === strpbrk(substr($string, $length, 1), $breakstr)) {
      $length--;
    }
  }
  elseif ($break == 1) {
    while ($length < strlen($string) && false === strpbrk(substr($string, $length, 1), $breakstr)) {
      $length++;
    }
  }

  $string = substr($string, 0, $length);
  $string .= $trail;
  $string = utf8_encode($string);

  return $string;
}

/**
 * Gets the diferences beetween two arrays of objects
 *
 * @param array $a original data
 * @param array $b new data
 * @param string $key the comparison key (the id of each row in data)
 * @param boolean $fields if true, remove unmodified fields from the update array
 * @return array associative array of objects to delete, update, create and keep (update + create)
 */
function sy_data_diff(array $a, array $b, $key, $fields = false)
{
  $add = array();
  $upd = array();
  $rem = array();
  $kee = array();

  $_a = $a;
  if ($fields) {
    $_a = array();
    foreach ($a as $i) {
      $_a[$i[$key]] = $i;
    }
    $a = $_a;
  }

  $_b = array();
  foreach ($b as $i) {
    if (isset($i[$key])) {
      $_b[$i[$key]] = $i;
    }
    else {
      $add[] = $i;
      $kee[] = $i;
    }
  }
  $b = $_b;

  while (count($_a)) {
    $j = array_shift($_a);

    if (isset($b[$j[$key]])) {
      $u = $b[$j[$key]];

      if ($fields) {
        foreach ($u as $f => $v) {
          if ($f != $key && $v == $a[$u[$key]][$f]) {
            unset($u[$f]);
          }
        }
      }

      $upd[] = $u;
      $kee[] = $u;
    }
    else {
      $rem[] = $j;
    }
  }

  return array('delete' => $rem, 'update' => $upd, 'create' => $add, 'keep' => $kee);
}

function sy_random_string($length = 40, $chars = null)
{
  if (empty($chars)) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*';
  }

  $c = strlen($chars) - 1;

  $str = '';
  while (strlen($str) < $length) {
    $str .= substr($chars, rand(0, $c), 1);
  }

  return $str;
}

function sy_checkbox_to_bool($value)
{
  return (empty($value) || strtolower($value) == 'off') ? 0 : 1;
}

function sy_array_map(&$item, $key)
{
  if ($item instanceof ArrayObject) {
    $item = $item->getArrayCopy();
    array_walk_recursive($item, 'sy_array_map');
  }

  elseif ($item instanceof Simplify_DictionaryInterface) {
    $item = $item->getAll();
    array_walk_recursive($item, 'sy_array_map');
  }

  elseif ($item instanceof Simplify_URL) {
    $item = (array) $item;
  }

  elseif ($item instanceof DateTime) {
    $item = $item->format('Y-m-d h:i:s');
  }
}

function sy_array_to_options($data, $key, $value = null)
{
  $options = array();

  if (!empty($data)) {
    foreach ($data as $row) {
      $options[sy_get_param($row, $key)] = empty($value) ? $row : sy_get_param($row, $value);
    }
  }

  return $options;
}

/**
 * Recursively strips slashes from all values in an array
 */
function sy_strip_slashes_deep($value)
{
  if (is_array($value)) {
    $return = array_map('sy_strip_slashes_deep', $value);
    return $return;
  }
  else {
    $return = stripslashes($value);
    return $return;
  }
}

/**
 *
 */
function sy_trim($value)
{
  if (!is_string($value) || empty($value))
    return $value;
  return trim($value);
}

/**
 * Recursively add slashes from all values in an array
 */
function sy_add_slashes_deep($value)
{
  if (is_array($value)) {
    $return = array_map('sy_add_slashes_deep', $value);
    return $return;
  }
  else {
    $return = addslashes($value);
    return $return;
  }
}

function _pre()
{
  $args = func_get_args();
  reset($args);
  echo '<div style="text-align:left">';
  while (count($args)) {
    echo '<pre>';
    print_r(array_shift($args));
    echo '</pre>';
  }
  echo '</div>';
}

/**
 * outputs an array/object as a preformated (HTML <pre></pre>) string.
 */
if (!function_exists('pre')) {

  function pre()
  {
    static $pre;

    if (!$pre) {
      ?>
<span id="pre-debug">
  <div id="pre-toolbar">
    <a href="javascript:"
      onclick="document.getElementById('pre-debug').style.display = 'none';">fechar</a>
  </div>
  <div id="pre-content"></div>
</span>

<style>
#pre-debug {
  display: inline-block;
  position: absolute;
  top: 0;
  left: 0;
  background: #c3c3c3;
  border: 1px solid #333;
  z-index: 9999;
}

#pre-content {
  height: 400px;
  overflow: auto;
}
</style>

<?php
      $pre = true;
    }

    $args = func_get_args();

    reset($args);

    $s = '';
    while (count($args)) {
      $s .= print_r(array_shift($args), true) . "\n";
    }

    $s = preg_replace('/ /ium', '&nbsp;', $s);
    $s = nl2br($s);
    $s = preg_replace('/\r?\n/ium', '', $s);
    $s = addslashes($s);

    echo "<script>document.getElementById('pre-content').innerHTML += '$s';</script>";
  }
}

/**
 * if $param is a valid key in $source, return it's value
 * otherwise return $default, $source can be eather an array or an object
 */
function sy_get_param($source, $param, $default = null, $testEmpty = false)
{
  if ($source instanceof Simplify_DictionaryInterface) {
    return $source->get($param, $default, $testEmpty);
  }
  elseif (is_array($source) || $source instanceof ArrayAccess) {
    if ($testEmpty)
      return !empty($source[$param]) ? $source[$param] : $default;

    return isset($source[$param]) ? $source[$param] : $default;
  }
  elseif (is_object($source)) {
    if ($testEmpty) {
      $value = $source->$param;
      return !empty($value) ? $value : $default;
    }

    return isset($source->$param) ? $source->$param : $default;
  }

  return $default;
}

function sy_set_param(&$source, $param, $value)
{
  if (is_array($source)) {
    $source[$param] = $value;
  }
  elseif (is_object($source)) {
    $source->$param = $value;
  }
  else {
    throw new Exception('Parameter $source must be either array or object');
  }
}

function sy_get_data(&$source)
{
  if (is_array($source)) {
    return $source;
  }
  elseif ($source instanceof Simplify_DictionaryInterface) {
    return $source->getAll();
  }

  throw new Exception('Parameter $source must be either array or object');
}

/**
 * Fix quirks in $url
 * - strip duplicate slashes
 * - add/remove trailling slashes
 * - replace /.. references with real path
 *
 * @param string $url
 * @param boolean $traillingSlash whether to add/keep slash at the end of the url
 * @return string clean url
 */
function sy_fix_url($url, $traillingSlash = false)
{
  $url = preg_replace('#\\\+#', '/', $url);

  if (!preg_match('|\.[^/]+$|', $url)) {
    if ($traillingSlash) {
      if (!strrpos('/', $url) === 0)
        $url .= '/';
    }
    elseif (strrpos('/', $url) === 0) {
      $url = substr($url, 0, strlen($url) - 1);
    }
  }

  $url = preg_replace('/(\/[^\/]+\/\.\.)/', '', $url);

  return $url;
}

function sy_absolute_url($url, $relative = null, $base = null)
{
  if (empty($base)) {
    $base = s::config()->get('theme_url');
  }

  if (empty($relative)) {
    $relative = $base;
  }

  if (strpos($url, '/') === 0) {
    $url = sy_fix_url($base . $url);
  } elseif (! sy_url_is_absolute($url)) {
    $url = sy_fix_url($base . $relative . '/' . $url);
  } else {
    $url = sy_fix_url($url);
  }

  return $url;
}

/**
 *
 */
function sy_add_http($url, $protocol = 'http')
{
  preg_match('#^([a-z]+:/+)?(.*)#', $url, $o);
  $url = $protocol . '://' . sy_get_param($o, 2);
  return $url;
}

/**
 *
 */
function sy_strip_http($url)
{
  preg_match('#^[a-z]+://(.*)#i', $url, $o);
  $url = $o[1];
  return $url;
}

/**
 * Define a constant only it's not yet defined.
 *
 * @param string $name constant name
 * @param mixed $value constant value
 * @return void
 */
function sy_define_once($name, $value)
{
  if (!defined($name)) {
    define($name, $value);
  }
}

/**
 * Check if $path is absolute.
 *
 * @param string $path the filepath
 * @return boolean
 */
function sy_path_is_absolute($path)
{
  return PATH_SEPARATOR == ';' ? preg_match('#^([a-z]:.+)$#i', $path) : file_exists($path);
}

/**
 * Check if $url is absolute.
 *
 * @param string $url the url
 * @return boolean
 */
function sy_url_is_absolute($url)
{
  return preg_match('|^[a-z]+://|i', $url);
}

/**
 *
 */
function sy_fix_path($path, $extension = null)
{
  // remove trailling slashes, duplicate slashes
  $find = array('#(/|\\\)#', '#\\\#', '#/+$#', '#/+#', '#/+$#', '#/[^/]+/\.\.(/|$)#');
  $replace = array(DIRECTORY_SEPARATOR, '/', '/', '/', '', '/');
  $path = preg_replace($find, $replace, $path);

  // add missing extension
  if (!empty($extension) && !preg_match('#\.' . $extension . '$#', $path)) {
    $path .= '.' . $extension;
  }

  return $path;
}

/**
 *
 */
function sy_fix_extension($path, $ext)
{
  if (!preg_match('/\.' . addslashes($ext) . '$/i', $path)) {
    $path .= '.' . $ext;
  }

  return $path;
}

function br2dt($date)
{
  if (empty($date))
    return $date;
  if (preg_match('/^(\d{2})[^\d](\d{2})[^\d](\d{4})(.*)$/', $date, $parts)) {
    $date = $parts[3] . '-' . $parts[2] . '-' . $parts[1] . $parts[4];
  }
  return strtotime($date);
}

function sy_slugify($string, $ignoreCase = false)
{
  $string = preg_replace('`\[.*\]`U', "", $string);
  $string = preg_replace('`&(amp;)?#?[a-z0-9]+;`i', '-', $string);
  $string = htmlentities($string, ENT_COMPAT, 'utf-8');
  $string = preg_replace("`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i", "\\1", $string);
  $string = preg_replace(array("`[^a-z0-9/:_.]`i", "`[-]+`"), "-", $string);

  if (!$ignoreCase) {
    $string = mb_strtolower($string, 'utf8');
  }

  return trim($string, '-');
}

/**
 * Convert a flat array with inner references (such as parent_id) to hierarchical structure
 *
 * @param unknown_type $flat
 * @param unknown_type $pk
 * @param unknown_type $parent
 * @param unknown_type $children
 * @return multitype:unknown
 */
function sy_flat_to_hierarchical($flat, $pk = 'id', $parent = 'parent_id', $children = 'children')
{
  $parents = array();

  $data = array();

  $i = 0;
  while ($i < count($flat)) {
    $row = $flat[$i++];

    $node_id = $row[$pk];
    $parent_id = $row[$parent];

    if (empty($parent_id)) {
      $data[$node_id] = $row;
      $parents[$node_id] = & $data[$node_id];
    }
    elseif (!isset($parents[$parent_id])) {
      $data[$node_id] = $row;
      $parents[$node_id] = & $data[$node_id];
    }
    else {
      if (!isset($parents[$parent_id][$children])) {
        $parents[$parent_id][$children] = array();
      }

      $parents[$parent_id][$children][$node_id] = $row;

      $parents[$node_id] = & $parents[$parent_id][$children][$node_id];
    }
  }

  return $data;
}

/**
 * Find an element in an array by one of it's properties
 *
 * @param mixed[] $array
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function sy_find_item($array, $key, $value)
{
  $found = false;
  foreach ($array as $item) {
    if (sy_get_param($item, $key) === $value) {
      $found = $item;
      break;
    }
  }
  return $found;
}

/**
 * Find the key of an element in an array by one of it's properties
 *
 * @param mixed[] $array
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function sy_find_key($array, $key, $value)
{
  $found = false;
  foreach ($array as $k => $item) {
    if (sy_get_param($item, $key) === $value) {
      $found = $k;
      break;
    }
  }
  return $found;
}

/**
 *
 * Polyfill for date_parse_from_format (PHP 5.3+)
 *
 */
if (!function_exists('date_parse_from_format')) {

  function date_parse_from_format($format, $date)
  {
    // reverse engineer date formats
    $keys = array('Y' => array('year', '\d{4}'), 'y' => array('year', '\d{2}'), 'm' => array('month', '\d{2}'),
      'n' => array('month', '\d{1,2}'), 'M' => array('month', '[A-Z][a-z]{3}'), 'F' => array('month', '[A-Z][a-z]{2,8}'),
      'd' => array('day', '\d{2}'), 'j' => array('day', '\d{1,2}'), 'D' => array('day', '[A-Z][a-z]{2}'),
      'l' => array('day', '[A-Z][a-z]{6,9}'), 'u' => array('hour', '\d{1,6}'), 'h' => array('hour', '\d{2}'),
      'H' => array('hour', '\d{2}'), 'g' => array('hour', '\d{1,2}'), 'G' => array('hour', '\d{1,2}'),
      'i' => array('minute', '\d{2}'), 's' => array('second', '\d{2}'));

    // convert format string to regex
    $regex = '';
    $chars = str_split($format);
    foreach ($chars as $n => $char) {
      $lastChar = isset($chars[$n - 1]) ? $chars[$n - 1] : '';
      $skipCurrent = '\\' == $lastChar;
      if (!$skipCurrent && isset($keys[$char])) {
        $regex .= '(?P<' . $keys[$char][0] . '>' . $keys[$char][1] . ')';
      }
      else if ('\\' == $char) {
        $regex .= $char;
      }
      else {
        $regex .= preg_quote($char);
      }
    }

    $dt = array();
    $dt['error_count'] = 0;
    // now try to match it
    if (preg_match('#^' . $regex . '$#', $date, $dt)) {
      foreach ($dt as $k => $v) {
        if (is_int($k)) {
          unset($dt[$k]);
        }
      }
      if (!checkdate($dt['month'], $dt['day'], $dt['year'])) {
        $dt['error_count'] = 1;
      }
    }
    else {
      $dt['error_count'] = 1;
    }
    $dt['errors'] = array();
    $dt['fraction'] = '';
    $dt['warning_count'] = 0;
    $dt['warnings'] = array();
    $dt['is_localtime'] = 0;
    $dt['zone_type'] = 0;
    $dt['zone'] = 0;
    $dt['is_dst'] = '';
    return $dt;
  }
}

/**
 *
 * Gerador de CPF e CNPJ
 *
 */

function mod($dividendo, $divisor)
{
  return round($dividendo - (floor($dividendo / $divisor) * $divisor));
}

function sy_cpf($compontos = true, $seed = null)
{
  if (! is_null($seed)) {
    mt_srand($seed);
  }

  $n1 = mt_rand(0, 9);
  $n2 = mt_rand(0, 9);
  $n3 = mt_rand(0, 9);
  $n4 = mt_rand(0, 9);
  $n5 = mt_rand(0, 9);
  $n6 = mt_rand(0, 9);
  $n7 = mt_rand(0, 9);
  $n8 = mt_rand(0, 9);
  $n9 = mt_rand(0, 9);
  $d1 = $n9 * 2 + $n8 * 3 + $n7 * 4 + $n6 * 5 + $n5 * 6 + $n4 * 7 + $n3 * 8 + $n2 * 9 + $n1 * 10;
  $d1 = 11 - (mod($d1, 11));
  if ($d1 >= 10) {
    $d1 = 0;
  }
  $d2 = $d1 * 2 + $n9 * 3 + $n8 * 4 + $n7 * 5 + $n6 * 6 + $n5 * 7 + $n4 * 8 + $n3 * 9 + $n2 * 10 + $n1 * 11;
  $d2 = 11 - (mod($d2, 11));
  if ($d2 >= 10) {
    $d2 = 0;
  }
  $retorno = '';
  if ($compontos) {
    $retorno = '' . $n1 . $n2 . $n3 . "." . $n4 . $n5 . $n6 . "." . $n7 . $n8 . $n9 . "-" . $d1 . $d2;
  }
  else {
    $retorno = '' . $n1 . $n2 . $n3 . $n4 . $n5 . $n6 . $n7 . $n8 . $n9 . $d1 . $d2;
  }
  return $retorno;
}

function sy_cnpj($compontos = true, $seed = null)
{
  if (! is_null($seed)) {
    mt_srand($seed);
  }

  $n1 = mt_rand(0, 9);
  $n2 = mt_rand(0, 9);
  $n3 = mt_rand(0, 9);
  $n4 = mt_rand(0, 9);
  $n5 = mt_rand(0, 9);
  $n6 = mt_rand(0, 9);
  $n7 = mt_rand(0, 9);
  $n8 = mt_rand(0, 9);
  $n9 = 0;
  $n10 = 0;
  $n11 = 0;
  $n12 = 1;
  $d1 = $n12 * 2 + $n11 * 3 + $n10 * 4 + $n9 * 5 + $n8 * 6 + $n7 * 7 + $n6 * 8 + $n5 * 9 + $n4 * 2 + $n3 * 3 + $n2 * 4 +
  $n1 * 5;
  $d1 = 11 - (mod($d1, 11));
  if ($d1 >= 10) {
    $d1 = 0;
  }
  $d2 = $d1 * 2 + $n12 * 3 + $n11 * 4 + $n10 * 5 + $n9 * 6 + $n8 * 7 + $n7 * 8 + $n6 * 9 + $n5 * 2 + $n4 * 3 + $n3 * 4 +
  $n2 * 5 + $n1 * 6;
  $d2 = 11 - (mod($d2, 11));
  if ($d2 >= 10) {
    $d2 = 0;
  }
  $retorno = '';
  if ($compontos) {
    $retorno = '' . $n1 . $n2 . "." . $n3 . $n4 . $n5 . "." . $n6 . $n7 . $n8 . "/" . $n9 . $n10 . $n11 . $n12 . "-" .
      $d1 . $d2;
  }
  else {
    $retorno = '' . $n1 . $n2 . $n3 . $n4 . $n5 . $n6 . $n7 . $n8 . $n9 . $n10 . $n11 . $n12 . $d1 . $d2;
  }
  return $retorno;
}
