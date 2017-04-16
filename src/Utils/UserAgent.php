<?php

/**
 * UserAgent.
 */

namespace Quark\Utils;

use Jenssegers\Agent\Agent;

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
   * @var \Jenssegers\Agent\Agent Agent.
   */
  private $agent;

  /**
   * Creates an instance of UserAgent.
   *
   * @param string $userAgent User agent.
   * @param array $headers Headers.
   */
  public function __construct(string $userAgent = null, array $headers = null)
  {
    $this->userAgent = isset($userAgent) ? $userAgent : !array_key_exists('HTTP_USER_AGENT', $_SERVER)?: $_SERVER['HTTP_USER_AGENT'];
    $this->headers = isset($headers) ? $headers : get_all_headers();

    $this->agent = new Agent();
    $this->agent->setUserAgent($this->userAgent);
    $this->agent->setHttpHeaders($this->headers);
  }

  /**
   * Get browser informations.
   *
   * @return array Browser informations.
   */
  public function getBrowser()
  {
    return [
      'name' => strtolower($this->agent->browser()),
      'version' => $this->agent->version($this->agent->browser())
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
      'name' => strtolower($this->agent->platform()),
      'version' => str_replace('_', '.', $this->agent->version($this->agent->platform()))
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
      'name' => strtolower($this->agent->device()),
      'isDesktop' => $this->agent->isDesktop(),
      'isTablet' => $this->agent->isTablet(),
      'isPhone' => $this->agent->isPhone(),
      'isMobile' => $this->agent->isMobile(),
      'isBot' => $this->agent->isRobot()
    ];
  }

  /**
   * Get bot informations.
   *
   * @return array Bot informations.
   */
  public function getBot()
  {
    return [
      'name' => $this->agent->robot()
    ];
  }
}
