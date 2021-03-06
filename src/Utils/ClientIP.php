<?php

/**
 * ClientIP.
 */

namespace Quark\Utils;

/**
 * ClientIP class.
 */
final class ClientIP
{
  /**
   * @var array Server keys.
   */
  private static $keys = [
    'REMOTE_ADDR',
    'HTTP_CLIENT_IP',
    'HTTP_X_FORWARDED_FOR',
    'HTTP_X_FORWARDED',
    'HTTP_FORWARDED_FOR',
    'HTTP_FORWARDED',
    'HTTP_X_CLUSTER_CLIENT_IP'
  ];

  /**
   * Get client IP address.
   *
   * @param boolean $validate Validate IP address range.
   * 
   * @return string|null Client IP address.
   */
  public static function get($validate = true)
  {
    if(env() === 'development') {
      return null;
    }

    foreach(self::$keys as $keyword) {
      if(isset($_SERVER[$keyword])) {
        if($validate) {
          if(self::validate($_SERVER[$keyword])) {
            return $_SERVER[$keyword];
          }
        } else {
          return $_SERVER[$keyword];
        }
      }
    }

    return null;
  }

  /**
   * Validate an IP address.
   *
   * @param string $ip IP address to validate.
   * 
   * @return bool True if it's a valid IP address, false otherwise.
   */
  public static function validate($ip)
  {
    if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
      return true;
    } else {
      return false;
    }
  }
}
