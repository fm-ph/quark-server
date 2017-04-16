<?php

/**
 * ClientIPTest.
 */

namespace Quark\Utils;

use PHPUnit\Framework\TestCase;

/**
 * ClientIPTest class.
 * 
 * @coversDefaultClass \Quark\Utils\ClientIP
 */
class ClientIPTest extends TestCase
{
  /**
   * @covers ::get
   */
  public function testGetIP()
  {
    $ip = ClientIP::get();
    $this->assertNull($ip);
  }

  /**
   * @covers ::validate
   */
  public function testValidatePublicRange()
  {
    $ip = '193.0.0.0';
    $valid = ClientIP::validate($ip);
    $this->assertTrue($valid);
  }

  /**
   * @covers ::validate
   */
  public function testValidatePrivateRange()
  {
    $ip = '127.0.0.0';
    $valid = ClientIP::validate($ip);
    $this->assertFalse($valid);
  }
}
