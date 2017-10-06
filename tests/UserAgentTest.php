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
   * @covers ::__construct
   */
  public function testUserAgentBot()
  {
    $userAgentBotString = 'Googlebot/2.1 (+http://www.googlebot.com/bot.html)';
    $userAgentBot = new UserAgent($userAgentBotString);

    $this->assertInstanceOf(UserAgent::class, $userAgentBot);

    return $userAgentBot;
  }

  /**
   * @depends testUserAgent
   * @covers ::getBrowser
   */
  public function testBrowser(UserAgent $userAgent)
  {
    $browserInfos = $userAgent->getBrowser();
    $this->assertArraySubset(['name' => 'chrome', 'channel' => null, 'stock' => false, 'mode' => null, 'version' => '57', 'old' => false], $browserInfos);
  }

  /**
   * @depends testUserAgent
   * @covers ::getEngine
   */
  public function testEngine(UserAgent $userAgent)
  {
    $engineInfos = $userAgent->getEngine();
    $this->assertArraySubset(['name' => 'blink', 'version' => null], $engineInfos);
  }

  /**
   * @depends testUserAgent
   * @covers ::getOperatingSystem
   */
  public function testOperatingSystem(UserAgent $userAgent)
  {
    $operatingSystemInfos = $userAgent->getOperatingSystem();
    $this->assertArraySubset(['name' => 'os x', 'version' => 'el capitan 10.11'], $operatingSystemInfos);
  }

  /**
   * @depends testUserAgent
   * @covers ::getDevice
   */
  public function testDevice(UserAgent $userAgent)
  {
    $deviceInfos = $userAgent->getDevice();
    $this->assertArraySubset(['type' => 'desktop', 'subtype' => null, 'identified' => true, 'manufacturer' => 'apple', 'model' => 'macintosh'], $deviceInfos);
  }

  /**
   * @depends testUserAgentBot
   * @covers ::getBot
   */
  public function testBot(UserAgent $userAgent)
  {
    $bot = $userAgent->getBot();
    $this->assertTrue($bot);
  }
}
