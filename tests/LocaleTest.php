<?php

/**
 * LocaleTest.
 */

namespace Quark\Utils;

use PHPUnit\Framework\TestCase;

/**
 * LocaleTest class.
 * 
 * @coversDefaultClass \Quark\Utils\Locale
 */
class LocaleTest extends TestCase
{
  protected function setUp()
  {
    app()->init([
      'paths' => [
        'locales' => join_path(__DIR__, 'fixtures/locales/{{locale}}.yml')
      ]
    ]);
  }

  /**
   * @covers ::getPreferedBrowserLocale
   */
  public function testPreferedBrowserLocale()
  {
    $acceptedLanguages = 'en,en-US,en-AU;q=0.8,fr;q=0.6,en-GB;q=0.4';

    $sortedPreferedBrowserLocales = Locale::getPreferedBrowserLocale(true, $acceptedLanguages);
    $preferedBrowserLocales = Locale::getPreferedBrowserLocale(false, $acceptedLanguages);

    $this->assertCount(5, $sortedPreferedBrowserLocales);
    $this->assertArraySubset(['en-US' => 1], $sortedPreferedBrowserLocales);

    $this->assertEquals('en-US', $preferedBrowserLocales);
  }

  /**
   * @covers ::getAvailableLocales
   */
  public function testAvailableLocales()
  {
    $availableLocales = Locale::getAvailableLocales();
    $this->assertCount(2, $availableLocales);
    $this->assertArraySubset([0 => 'en', 1 => 'fr'], $availableLocales);
  }

  /**
   * @covers ::getCount
   */
  public function testCount()
  {
    $availableLocalesCount = Locale::getCount();
    $this->assertEquals(2, $availableLocalesCount);
  }
}
