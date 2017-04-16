<?php

/**
 * UserAgentTest.
 */

namespace Quark\Utils;

use PHPUnit\Framework\TestCase;

/**
 * UserAgentTest class.
 * 
 * @coversDefaultClass \Quark\Utils\UserAgent
 */
class UserAgentTest extends TestCase
{
  /**
   * @covers ::__construct
   */
  public function testUserAgent()
  {
    $userAgentString = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36';
    $userAgent = new UserAgent($userAgentString);

    $this->assertInstanceOf(UserAgent::class, $userAgent);

    return $userAgent;
  }

  /**
   * @depends testUserAgent
   * @covers ::getBrowser
   */
  public function testBrowser(UserAgent $userAgent)
  {
    $browserInfos = $userAgent->getBrowser();
    $this->assertArraySubset(['name' => 'chrome', 'version' => '57.0.2987.133'], $browserInfos);
  }

  /**
   * @depends testUserAgent
   * @covers ::getOperatingSystem
   */
  public function testOperatingSystem(UserAgent $userAgent)
  {
    $operatingSystemInfos = $userAgent->getOperatingSystem();
    $this->assertArraySubset(['name' => 'os x', 'version' => '10.11.6'], $operatingSystemInfos);
  }

  /**
   * @depends testUserAgent
   * @covers ::getDevice
   */
  public function testDevice(UserAgent $userAgent)
  {
    $deviceInfos = $userAgent->getDevice();
    $this->assertArraySubset(
      [
        'name' => 'macintosh',
        'isDesktop' => true,
        'isTablet' => false,
        'isPhone' => false,
        'isMobile' => false,
        'isBot' => false
      ],
      $deviceInfos
    );
  }

  /**
   * @depends testUserAgent
   * @covers ::getBot
   */
  public function testBot(UserAgent $userAgent)
  {
    $botInfos = $userAgent->getBot();
    $this->assertArraySubset(['name' => false], $botInfos);
  }
}
