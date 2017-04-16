<?php

/**
 * ManifestExtension.
 */

namespace Quark\Twig\Extensions;

use Quark\Manifest;

/**
 * ManifestExtension class.
 */
final class ManifestExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
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
}
