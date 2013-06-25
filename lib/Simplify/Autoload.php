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
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */

/**
 *
 * Simplify PSR-0 style autoloader
 *
 */
class Simplify_Autoload
{

  protected static $path = array();

  public static function registerPath($path)
  {
    if (! sy_path_is_absolute($path)) {
      $path = SY_DIR . DIRECTORY_SEPARATOR . $path;
    }

    array_unshift(self::$path, $path);
  }

  public static function autoload($class)
  {
    $className = ltrim($class, '\\');
    $fileName = '';
    $namespace = '';
    if (false !== ($lastNsPos = strrpos($className, '\\'))) {
      $namespace = substr($className, 0, $lastNsPos);
      $className = substr($className, $lastNsPos + 1);
      $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    foreach (self::$path as $path) {
      $filename = $path . DIRECTORY_SEPARATOR . $fileName;

      if (file_exists($filename)) {
        require_once ($filename);
        return;
      }
    }
  }

}
