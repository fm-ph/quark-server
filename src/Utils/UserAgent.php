<?php

/**
 * UserAgent.
 */

namespace Quark\Utils;

use WhichBrowser\Parser;

/**
 * UserAgent class
 */
final class UserAgent
{
  /**
   * @var string User agent.
   */
  private $userAgent;

  /**
   * @var array Headers.
   */
  private $headers;

  /**
   * @var \WhichBrowser\Parser Parser.
   */
  private $parser;

  /**
   * Creates an instance of UserAgent.
   *
   * @param string $userAgent User agent.
   * @param array $headers Headers.
   */
  public function __construct(string $userAgent = null, array $headers = null)
  {
    $this->userAgent = isset($userAgent) ? $userAgent : (!array_key_exists('HTTP_USER_AGENT', $_SERVER) ? null : $_SERVER['HTTP_USER_AGENT']);
    $this->headers = isset($headers) ? $headers : get_all_headers();

    $this->parser = new Parser(isset($this->userAgent) ? $this->userAgent : $this->headers);
  }

  /**
   * Get browser informations.
   *
   * @return array Browser informations.
   */
  public function getBrowser()
  {
    return [
      'name' => $this->parseProperty($this->parser->browser->name),
      'channel' => $this->parseProperty($this->parser->browser->channel),
      'stock' => $this->parseProperty($this->parser->browser->stock),
      'mode' => $this->parseProperty($this->parser->browser->mode),
      'version' => $this->parseProperty($this->parser->browser->getVersion()),
      'old' => $this->isOldBrowser()
    ];
  }

  /**
   * Get engine informations.
   *
   * @return array Engine informations.
   */
  public function getEngine()
  {
    return [
      'name' => $this->parseProperty($this->parser->engine->name),
      'version' => $this->parseProperty($this->parser->engine->getVersion())
    ];
  }

  /**
   * Get operating system informations.
   *
   * @return array Operating system informations.
   */
  public function getOperatingSystem()
  {
    return [
      'name' => $this->parseProperty($this->parser->os->name),
      'version' => $this->parseProperty($this->parser->os->version->value),
      'nickname' => $this->parseProperty($this->parser->os->version->nickname)
    ];
  }

  /**
   * Get device informations.
   *
   * @return array Device informations.
   */
  public function getDevice()
  {
    return [
      'type' => $this->parseProperty($this->parser->device->type),
      'subtype' => $this->parseProperty($this->parser->device->subtype),
      'identified' => !!$this->parseProperty($this->parser->device->identified),
      'manufacturer' => $this->parseProperty($this->parser->device->manufacturer),
      'model' => $this->parseProperty($this->parser->device->model)
    ];
  }

  /**
   * Get bot informations.
   *
   * @return array Bot informations.
   */
  public function getBot()
  {
    return ($this->parseProperty($this->parser->device->type) === 'bot');
  }

  /**
   * Check old browsers.
   *
   * @return boolean True if a browser is old, false otherwise.
   */
  public function isOldBrowser()
  {
    $browsers = config('old_browser');
    $old = false;

    foreach ($browsers as $key => $browser) {
      if(isset($browser['comparison']) && isset($browser['version'])) {
        $old = $old || $this->parser->isBrowser($browser['name'], $browser['comparison'], $browser['version']);
      } else if(isset($browser['name'])) {
        $old = $old || $this->parser->isBrowser($browser['name']);
      }
    }

    return $old;
  }

  /**
   * Parse property value.
   *
   * @param mixed $value
   * 
   * @return mixed Parsed value.
   */
  private function parseProperty($value)
  {
    $result = ($value === '') ? null : $value;

    if(is_string($result)) {
      $result = strtolower($result);
    }

    return $result;
  }
}
