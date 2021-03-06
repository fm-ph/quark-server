<?php

/**
 * Helpers.
 */

if (!function_exists('app')) {
  /**
   * Get application instance.
   *
   * @return \Quark\Application Application instance.
   */
  function app()
  {
    return Quark\Application::getInstance();
  }
}

if (!function_exists('config')) {
  /**
   * Get configuration value.
   *
   * @param string $key Configuration key.
   * @param mixed $default Default value.
   * 
   * @return mixed Configuration value.
   */
  function config(string $key, $default = null)
  {
    if(is_null(app()->getConfig())) {
      app()->init();
    }

    return app()->getConfig()->get($key, $default);
  }
}

if (!function_exists('manifest')) {
  /**
   * Get a manifest value.
   *
   * @param string $key Manifest key.
   * @param mixed $default Default value.
   * 
   * @return mixed Manifest value.
   */
  function manifest(string $key, $default = null)
  {
    if(!is_null(app()->manifest)) {
      return app()->manifest->get($key, $default);
    }
  }
}

if (!function_exists('env')) {
  /**
   * Get environment.
   *
   * @return string Environment.
   */
  function env()
  {
    return app()->environment;
  }
}

if (!function_exists('locale')) {
  /**
   * Get locale.
   *
   * @return string Locale.
   */
  function locale()
  {
    return app()->locale;
  }
}

if (!function_exists('twig')) {
  /**
   * Get Twig instance.
   *
   * @return \Quark\Twig Twig instance.
   */
  function twig()
  {
    return app()->getTwig();
  }
}

if (!function_exists('join_path')) {
  /**
   * Join paths.
   *
   * @return string Joined paths.
   */
  function join_path()
  {
    return preg_replace('~[/\\\]+~', DIRECTORY_SEPARATOR, implode(DIRECTORY_SEPARATOR, func_get_args()));
  }
}

if (!function_exists('base_path')) {
  /**
   * Get base path.
   *
   * @param string $path Prepend base path.
   * 
   * @return string Base path.
   */
  function base_path(string $path = '')
  {
    if(!defined('BASE_PATH')) {
      define('BASE_PATH', __DIR__);
    }

    return empty($path) ? BASE_PATH : join_path(BASE_PATH, $path);
  }
}

if (!function_exists('path')) {
  /**
   * Get configuration path.
   *
   * @param string $key Path key.
   * @param mixed $default Default value.
   * 
   * @return string Configuration path.
   */
  function path(string $key, $default = null)
  {    
    return config('paths.' . $key, $default);
  }
}

if (!function_exists('debug')) {
  /**
   * Debug an expression.
   */
  function debug()
  {
    var_dump(func_get_args());
  }
}

if (!function_exists('redirect')) {
  /**
   * Redirect to an URL.
   *
   * @param string $url URL.
   * @param boolean $permanent Permanent redirection.
   */
  function redirect(string $url, bool $permanent = true)
  {
    if (headers_sent() === false) {
      header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
    }

    exit();
  }
}

if (!function_exists('array_merge_deep')) {
  /**
   * Array merge deep.
   *
   * @param array $array1 First array.
   * @param array $array2 Second array.
   * 
   * @return array Merged array.
   */
  function array_merge_deep(array $array1 = [], array $array2 = [])
  {
    $arrays = func_get_args();
    $merge = array_shift($arrays);

    foreach ($arrays as $array)
    {
      foreach ($array as $key => $val)
      {
        if (is_array($val) && array_key_exists($key, $merge)) {
          $val = array_merge_deep((array) $merge[$key], $val);
        }

        if (is_numeric($key)) {
          $merge[] = $val;
        } else {
          $merge[$key] = $val;
        }
      }
    }

    return $merge;
  }
}

if (!function_exists('get_all_headers'))  {
  /**
   * Get all headers.
   *
   * @return array Headers.
   */
  function get_all_headers()
  {
    if (!is_array($_SERVER)) {
      return [];
    }

    $headers = array();
    foreach ($_SERVER as $name => $value) {
      if (substr($name, 0, 5) == 'HTTP_') {
        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
      }
    }

    return $headers;
  }
}

if (!function_exists('starts_with'))  {
  /**
   * Starts with.
   *
   * @param string $haystack Haystack.
   * @param string $needle Needle.
   * 
   * @return bool True if it starts with, false otherwise.
   */
  function starts_with($haystack, $needle)
  {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
  }
}
