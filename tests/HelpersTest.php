<?php

/**
 * HelpersTest.
 */

use PHPUnit\Framework\TestCase;

define('BASE_PATH', __DIR__);

/**
 * HelpersTest class.
 */
class HelpersTest extends TestCase
{
  protected function setUp()
  {
    app()->init([
      'paths' => [
        'manifest' => join_path(__DIR__, 'fixtures/manifest.json')
      ]
    ]);
  }

  /**
   * @covers ::app
   */
  public function testApp()
  {
    $this->assertInstanceOf(\Quark\Application::class, app());
  }

  /**
   * @covers ::config
   */
  public function testConfig()
  {
    $this->assertEquals('default', config('twig.layouts.default'));
    $this->assertEquals('bar', config('foo', 'bar'));
  }

  /**
   * @covers ::manifest
   */
  public function testManifest()
  {
    $this->assertEquals('7d84331fb533058c4fe3b077e8adc74f', manifest('hash'));
    $this->assertEquals('development', manifest('environment'));
  }

  /**
   * @covers ::env
   */
  public function testEnv()
  {
    $this->assertEquals('development', env());
  }

  /**
   * @covers ::locale
   */
  public function testLocale()
  {
    $this->assertEquals('en', locale());
  }

  /**
   * @covers ::twig
   */
  public function testTwig()
  {
    $this->assertInstanceOf(\Quark\Twig\Twig::class, twig());
  }

  /**
   * @covers ::join_path
   */
  public function testJoinPath()
  {
    $this->assertEquals('foo/bar', join_path('foo', 'bar'));
    $this->assertEquals('foo', join_path('foo'));
  }

  /**
   * @covers ::base_path
   */
  public function testBasePath()
  {
    $this->assertEquals(__DIR__, base_path());
  }

  /**
   * @covers ::path
   */
  public function testPath()
  {
    $this->assertEquals(join_path(__DIR__, 'fixtures/manifest.json'), path('manifest'));
  }

  /**
   * @covers ::array_merge_deep
   */
  public function testArrayMergeDeep()
  {
    $array1 = [
      'foo' => [
        'bar' => 'foo'
      ],
      'bar' => true,
    ];

    $array2 = [
      'foo' => [
        'foo' => 'bar'
      ],
      'bar' => 'foo',
      'foobar' => 'foobar'
    ];

    $merged = [
      'foo' => [
        'bar' => 'foo',
        'foo' => 'bar'
      ],
      'bar' => 'foo',
      'foobar' => 'foobar'
    ];

    $this->assertEquals($merged, array_merge_deep($array1, $array2));
  }

  /**
   * @covers ::get_all_headers
   */
  public function testGetAllHeaders()
  {
    $_SERVER['HTTP_HOST'] = '127.0.0.1';
    $this->assertEquals(['Host' => '127.0.0.1'], get_all_headers());
  }
}
