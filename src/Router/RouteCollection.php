<?php

/**
 * RouteCollection.
 */

namespace Quark\Router;

/**
 * RouteCollection class.
 */
class RouteCollection extends \SplObjectStorage
{
  /**
   * Attach a route object.
   *
   * @param Route $route Route.
   */
  public function attachRoute(Route $route)
  {
    parent::attach($route);
  }

  /**
   * Get all route objects.
   *
   * @return array Route objects.
   */
  public function all()
  {
    $tmp = [];

    foreach ($this as $route) {
      $tmp[] = $route;
    }

    return $tmp;
  }
}
