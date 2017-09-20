<?php

/**
 * GeocoderTest.
 */

namespace Quark\Utils;

use PHPUnit\Framework\TestCase;

/**
 * GeocoderTest class.
 * 
 * @coversDefaultClass \Quark\Utils\Geocoder
 * @todo Add missing WAN tests.
 */
class GeocoderTest extends TestCase
{
  static $lanIP = '127.0.0.1';
  static $wanIP = '138.197.194.7';
  static $badIP = '0.0.0.0.0';

  /**
   * @covers ::__construct
   */
  public function testGeocoderLAN()
  {
    $geocoder = new Geocoder(self::$lanIP);
    $this->assertInstanceOf(Geocoder::class, $geocoder);

    return $geocoder;
  }

  /**
   * @covers ::_construct
   */
  public function testGeocoderWAN()
  {
    $geocoder = new Geocoder(self::$wanIP);
    $this->assertInstanceOf(Geocoder::class, $geocoder);

    return $geocoder;
  }
  
  /**
   * @depends testGeocoderLAN
   * @covers ::getGeocode
   */
  public function testGeocodeLAN(Geocoder $geocoder)
  {
    $geocode = $geocoder->getGeocode();
    $this->assertInstanceOf(\Geocoder\Model\AddressCollection::class, $geocode);
  }

  /**
   * @depends testGeocoderLAN
   * @covers ::getLocale
   */
  public function testLocaleLAN(Geocoder $geocoder)
  {
    $locale = $geocoder->getLocale();
    $this->assertEquals(locale(), $locale);
  }

  /**
   * @depends testGeocoderLAN
   * @covers ::getCountry
   */
  public function testCountryLAN(Geocoder $geocoder)
  {
    $country = $geocoder->getCountry();
    $this->assertEquals('localhost', $country);
  }

  /**
   * @depends testGeocoderWAN
   * @covers ::getCountry
   */
  public function testLocaleWAN(Geocoder $geocoder)
  {
    $locale = $geocoder->getLocale();
    $this->assertEquals('en', $locale);
  }

  /**
   * @depends testGeocoderWAN
   * @covers ::getCountry
   */
  public function testCountryWAN(Geocoder $geocoder)
  {
    $country = $geocoder->getCountry();
    $this->assertEquals('united states', $country);
  }
}
