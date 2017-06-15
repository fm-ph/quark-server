<?php

/**
 * Manifest.
 */

namespace Quark;

/**
 * Manifest class.
 */
final class Manifest {
  /**
   * Get a cache breaked path.
   *
   * @param string $path Path.
   *
   * @return string Cache breaked path.
   */
  public static function getPath(string $path)
  {
    // Clean path
    $path = rtrim($path, '/\\');

    if(is_null(manifest('hash'))) {
      return $path;
    }

    $pathInfo = pathinfo($path);
    $isDir = !pathinfo($path, PATHINFO_EXTENSION);

    // Directory or file
    if ($isDir) {
      return $path . '-' . manifest('hash');
    } else {
      return join_path($pathInfo['dirname'], $pathInfo['filename'] . '-' . manifest('hash') . '.' . $pathInfo['extension']);
    }
  }
}
