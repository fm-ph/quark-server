<?php

/**
 * Router.
 */

namespace Quark\Router;

use Quark\Utils\Locale;

use Fig\Http\Message\RequestMethodInterface;

/**
 * Router class.
 */
class Router
{
  /**
   * @var array Routes.
   */
  private $routes = [];

  /**
   * @var string Base path.
   */
  private $basePath = '';

  /**
   * @var \Quark\Router\Route Current route.
   */
  private $currentRoute;

  /**
   * Creates an instance of Router.
   *
   * @param RouteCollection $collection Route collection.
   */
  public function __construct(RouteCollection $collection)
  {
    $this->routes = $collection;
  }

  /**
   * Match current request.
   *
   * @return \Quark\Router\Route|null Matched route.
   */
  public function matchCurrentRequest()
  {
    $requestMethod = (
      isset($_POST['_method'])
      && ($_method = strtoupper($_POST['_method']))
      && in_array($_method, [RequestMethodInterface::METHOD_PUT, RequestMethodInterface::METHOD_DELETE], true)
    ) ? $_method : $_SERVER['REQUEST_METHOD'];

    $requestUrl = $_SERVER['REQUEST_URI'];

    // Remove GET parameters
    if (($pos = strpos($requestUrl, '?')) !== false) {
      $requestUrl = substr($requestUrl, 0, $pos);
    }

    $defaultUrl = $requestUrl;

    // Remove locale
    if(strpos($requestUrl, locale()) === 1) {
		  $requestUrl = substr($requestUrl, strlen(locale()) + 1);
		}

    $localeUrl = join_path('/', locale(), $requestUrl);
    
    // Locale redirect
    if($defaultUrl != $localeUrl && (Locale::getCount() > 1 || config('locale.redirectIfOne'))) {
      redirect($localeUrl);
    }

    $match = $this->match($requestUrl, $requestMethod);

    // If no match, redirect to prevent exception
    if(is_null($match)) {
      redirect('/');
    }

    // If route redirect is defined, redirect
    if(!is_null($match->getRedirect())) {
      redirect($match->getRedirect());
    }

    return $match;
  }

  /**
   * Match a request URL/methods.
   *
   * @param string $requestUrl Request URL.
   * @param string $requestMethod Request method.
   * 
   * @return \Quark\Router\Route|null Matched route.
   */
  public function match(string $requestUrl, $requestMethod = RequestMethodInterface::METHOD_GET)
  {
    $currentDir = dirname($_SERVER['SCRIPT_NAME']);

    foreach ($this->routes->all() as $route) {
      if (! in_array($requestMethod, (array) $route->getMethods(), true)) {
        continue;
      }

      if ('/' !== $currentDir) {
        $requestUrl = str_replace($currentDir, '', $requestUrl);
      }

      $paramKeys = [];
      $regex = \PathToRegexp::convert($route->getPath(), $paramKeys);

      $matches = \PathToRegexp::match($regex, $requestUrl);
      
      if($matches) {
        $params = $this->parseParameters($paramKeys, $matches);
        $route->setParameters($params);

        $this->currentRoute = $route;

        return $this->currentRoute;
      }
    }

    return null;
  }

  /**
   * Parse parameters.
   *
   * @param array $keys Parameters keys.
   * @param array $values Values.
   * 
   * @return array Parameters.
   */
  public function parseParameters(array $keys, array $values)
  {
    $params = [];

    for($i = 0; $i < count($keys); $i++) {
      if($i + 1 < count($values)) {
        $value = $values[$i + 1];
      } else {
        $value = "";
      }

      $params[$keys[$i]["name"]] = $value;
    }
    
    return $params;
  }

  /**
   * Set base path.
   *
   * @param string $basePath Base path.
   */
  public function setBasePath(string $basePath)
  {
    $this->basePath = rtrim($basePath, '/');
  }

  /**
   * Parse configuration.
   *
   * @param array $config Configuration.
   * 
   * @return \Quark\Router\Router
   */
  public static function parseConfig(array $config = [])
  {
    $routeCollection = new RouteCollection();
    foreach ($config['routes'] as $routeConfig) {

      $routeOptions = [
        'name' => isset($routeConfig['name']) ? $routeConfig['name'] : null,
        'methods' => isset($routeConfig['methods']) ? $routeConfig['methods'] : RequestMethodInterface::METHOD_GET,
        'redirect' => isset($routeConfig['redirect']) ? $routeConfig['redirect'] : null
      ];

      $route = new Route($routeConfig['path'], $routeOptions);
      $routeCollection->attachRoute($route);
    }
    
    $router = new Router($routeCollection);

    if (isset($config['base_path'])) {
      $router->setBasePath($config['base_path']);
    }

    return $router;
  }
}
