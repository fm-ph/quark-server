<?php

/**
 * Route.
 */

namespace Quark\Router;

use Fig\Http\Message\RequestMethodInterface;

/**
 * Route class.
 */
class Route
{
  /**
   * @var string Route path.
   */
  private $path;

  /**
   * @var array Route options.
   */
  private $options;
  
  /**
   * @var array Available route methods.
   */
  private $methods = [
    RequestMethodInterface::METHOD_GET,
    RequestMethodInterface::METHOD_POST,
    RequestMethodInterface::METHOD_PUT,
    RequestMethodInterface::METHOD_DELETE
  ];

  /**
   * @var string Route name.
   */
  private $name;

  /**
   * @var array Route parameters.
   */
  private $parameters = [];

  /**
   * Creates an instance of Route.
   *
   * @param string $path Route path.
   * @param array $options Route options.
   */
  public function __construct(string $path, array $options = [])
  {
    $this->path = $path;
    $this->options = $options;
    $this->methods = isset($options['methods']) ? (array) $options['methods'] : [];
    $this->name = isset($options['name']) ? $options['name'] : null;
    $this->redirect = isset($options['redirect']) ? $options['redirect'] : null;
  }

  /**
   * Get route path.
   *
   * @return string Route path.
   */
  public function getPath()
  {
    return $this->path;
  }

  /**
   * Set route path.
   *
   * @param string $path Route path.
   */
  public function setPath($path)
  {
    $path = (string) $path;

    if (substr($path, -1) !== '/') {
      $path .= '/';
    }

    $this->path = $path;
  }

  /**
   * Get route methods.
   *
   * @return string Route methods.
   */
  public function getMethods()
  {
    return $this->methods;
  }

  /**
   * Set route methods.
   *
   * @param array $methods Route methods.
   */
  public function setMethods(array $methods)
  {
    $this->methods = $methods;
  }
  
  /**
   * Get route name.
   *
   * @return string Route name.
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Set route name.
   *
   * @param string $name Route name.
   */
  public function setName(string $name)
  {
    $this->name = $name;
  }

  /**
   * Get route redirect.
   *
   * @return string Route redirect.
   */
  public function getRedirect()
  {
    return $this->redirect;
  }

  /**
   * Set route redirect.
   *
   * @param string $redirect Route redirect.
   */
  public function setRedirect(string $redirect)
  {
    $this->redirect = $redirect;
  }

  /**
   * Get route parameters.
   *
   * @return array Route parameters.
   */
  public function getParameters()
  {
    return $this->parameters;
  }

  /**
   * Set route parameters.
   *
   * @param array $parameters Route parameters.
   */
  public function setParameters(array $parameters)
  {
    $this->parameters = $parameters;
  }
}
