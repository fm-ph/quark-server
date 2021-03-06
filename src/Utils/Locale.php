<?php

/**
 * Locale.
 */

namespace Quark\Utils;

/**
 * Locale class.
 */
final class Locale
{
  /**
   * Get prefered browser locale(s).
   *
   * @param boolean $getSortedList True to sort the list, false otherwise.
   * @param boolean $acceptedLanguages Force accept languages.
   * 
   * @return string|array Prefered browser locale(s).
   */
  static public function getPreferedBrowserLocale($getSortedList = false, $acceptedLanguages = '')
  {
    if (empty($acceptedLanguages) && array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
      $acceptedLanguages = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    }

    preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})*)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $acceptedLanguages, $lang_parse);
    $langs = $lang_parse[1];
    $ranks = $lang_parse[4];

    $lang2pref = [];
    for($i = 0; $i < count($langs); $i++) {
      $lang2pref[$langs[$i]] = (float) (!empty($ranks[$i]) ? $ranks[$i] : 1);
    }

    $cmpLangs = function ($a, $b) use ($lang2pref) {
      if ($lang2pref[$a] > $lang2pref[$b])
        return -1;
      elseif ($lang2pref[$a] < $lang2pref[$b])
        return 1;
      elseif (strlen($a) > strlen($b))
        return -1;
      elseif (strlen($a) < strlen($b))
        return 1;
      else
        return 0;
    };

    // Sort the languages by prefered language and by the most specific region
    uksort($lang2pref, $cmpLangs);

    if ($getSortedList) {
      return $lang2pref;
    }

    // Return the first value's key
    reset($lang2pref);

    return key($lang2pref);
  }

  /**
   * Get available locales.
   *
   * @return array Available locales.
   */
  static public function getAvailableLocales()
  {
    $availableLocales = [];

    $localeDir = pathinfo(path('locales'))['dirname'];
    $localePath = join_path($localeDir, '*.*');

    foreach(glob($localePath) as $path) {
      $pathInfos = pathinfo($path);
      $availableLocales[] = $pathInfos['filename'];
    }

    return $availableLocales;
  }

  /**
   * Get locale count.
   *
   * @return int Locale count.
   */
  static public function getCount()
  {
    return count(self::getAvailableLocales());
  }
}
