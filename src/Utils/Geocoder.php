<?php

/**
 * Geocoder.
 */

namespace Quark\Utils;

use Geocoder\ProviderAggregator;
use Geocoder\Provider\Chain;
use Geocoder\Provider\GeoPlugin;
use Geocoder\Provider\FreeGeoIp;
use Geocoder\Provider\HostIp;

use Http\Adapter\Guzzle6\Client as HTTPClient;

/**
 * Geocoder class.
 */
final class Geocoder
{
  /**
   * @var string IP address.
   */
  private $ip;

  /**
   * @var \Geocoder\ProviderAggregator Geocoder.
   */
  private $geocoder;

  /**
   * @var \Geocoder\Model\AddressCollection Geocode.
   */
  private $geocode = null;

  /**
   * Creates an instance of Geocoder.
   *
   * @param string $ip IP address.
   */
  public function __construct($ip)
  {
    $this->ip = $ip;

    $this->geocoder = new ProviderAggregator();
    $adapter = new HTTPClient();

    $chain = new Chain([
      new GeoPlugin($adapter),
      new FreeGeoIp($adapter),
      new HostIp($adapter)
    ]);

    $this->geocoder->registerProvider($chain);

    $this->geocode();
  }

  /**
   * Geocode an IP address.
   * 
   * @throws \Geocoder\Exception\Exception
   */
  private function geocode()
  {
    try {
      $this->geocode = $this->geocoder->geocode($this->ip);
    } catch (Exception $e) {
      if(env() === 'development') {
        echo $e->getMessage();
      }
    }
  }

  /**
   * Get geocode.
   *
   * @return \Geocoder\Model\AddressCollection
   */
  public function getGeocode()
  {
    return $this->geocode;
  }

  /**
   * Get locale.
   *
   * @return string Locale.
   */
  public function getLocale()
  {
    if($this->geocode->count()) { // Try to get country code from geocode
      $locale = strtolower($this->geocode->first()->getCountry()->getCode());
    } else if(array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) { // Fallback with accept language server variable
      $locale = substr(Locale::getPreferedBrowserLocale(), 0, 2);
    }

    // Check that locale exists
    $availableLocales = Locale::getAvailableLocales();
    if(!in_array($locale, $availableLocales)) {
      $locale = config('locale.code');
    }

    return $locale;
  }

  /**
   * Get country.
   *
   * @return string Country.
   */
  public function getCountry()
  {
    $country = config('locale.country');

    if($this->geocode->count()) {
      $country = strtolower($this->geocode->first()->getCountry()->getName());
    }

    return $country;
  }
}
