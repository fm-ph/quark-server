<?php

/**
 * QuarkExtension.
 */

namespace Quark\Twig\Extensions;

use Quark\Manifest;

/**
 * QuarkExtension class.
 */
final class QuarkExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
  /**
   * Get globals.
   * 
   * @return array Globals.
   */
  public function getGlobals()
  {
    return [
      'hash' => manifest('hash'),
      'env' => env(),
      'environment' => env()
    ];
  }

  /**
   * Get functions.
   *
   * @return array Functions.
   */
  public function getFunctions()
  {
    return [
      new \Twig_Function('hash_path', function($path) {
        return Manifest::getPath($path);
      })
    ];
  }

  /**
   * Get filters.
   *
   * @return array Filters.
   */
  public function getFilters()
  {
    return [
      new \Twig_SimpleFilter('kebab_to_pascal', function($string) {
        $string = strtolower($string);
        $string = explode('-', $string);

        for ($i = 0; $i < count($string); $i++) { 
          $string[$i] = ucfirst($string[$i]);
        }

        return implode('', $string);
      })
    ];
  }
}
