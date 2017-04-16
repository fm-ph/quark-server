<?php

/**
 * Application.
 */

namespace Quark;

use Noodlehaus\Config as NConfig;
use Noodlehaus\AbstractConfig;

use Quark\Router\Router;
use Quark\Router\Route;
use Quark\Router\RouteCollection;

use Quark\Twig\Twig;

use Quark\Utils\ClientIP;
use Quark\Utils\Geocoder;
use Quark\Utils\UserAgent;
use Quark\Utils\Locale;

/**
 * Application class.
 * 
 * @license MIT License
 * 
 * @author Fabien Motte <contact@fabienmotte.com>
 * @author Patrick Heng <hengpatrick.pro@gmail.com>
 */
class Application
{
  /**
   * @var Application Application instance.
   */
  private static $instance;

  /**
   * @var \Whoops\Run Whoops instance.
   */
  private $whoops;

  /**
   * @var \Quark\Config Configuration instance.
   */
  private $config;

  /**
   * @var \Quark\Utils\Geocoder Geocoder instance.
   */
  private $geocoder;

  /**
   * @var string IP address.
   */
  private $ip = '127.0.0.1';

  /**
   * @var \Quark\Utils\UserAgent UserAgent instance.
   */
  private $userAgent;

  /**
   * @var \Quark\Twig\Twig Twig instance.
   */
  private $twig;

  /**
   * @var \Quark\Router\Router Router instance.
   */
  private $router;

  /**
   * @var array Routes.
   */
  private $routes;

  /**
   * @var \Quark\Manifest Manifest.
   */
  public $manifest;

  /**
   * @var string Environment.
   */
  public $environment = 'development';

  /**
   * @var string Hash.
   */
  public $hash;

  /**
   * @var string Locale.
   */
  public $locale;

  /**
   * @var array Localization.
   */
  public $l10n;

  /**
   * Private constructor (singleton).
   */
  private function __construct()
  {}

  /**
   * Init.
   *
   * @param array $config Configuration.
   */
  public function init(array $config = [])
  {
    if(env() === 'development') {
      $this->createWhoops();
    }
    
    $this->createConfig($config);
    $this->createManifest();
    $this->createTwig();
    $this->createLocale();
    $this->createL10n();
    $this->createUserAgent();
    $this->createRouter();
  }

  /**
   * Create Whoops.
   */
  private function createWhoops()
  {
    $this->whoops = new \Whoops\Run;
    $this->whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $this->whoops->register();
  }

  /**
   * Create config.
   *
   * @param array $config Configuration.
   */
  private function createConfig($config)
  {
    $this->config = new Config($config);
  }

  /**
   * Create manifest.
   */
  private function createManifest()
  {
    if(file_exists(path('manifest'))) {
      $this->manifest = NConfig::load(path('manifest'));
      $this->environment = $this->manifest->get('environment');
      $this->hash = $this->manifest->get('hash');
    }
  }

  /**
   * Create Twig.
   */
  private function createTwig()
  {
    $this->twig = new Twig();
  }

  /**
   * Create locale.
   */
  private function createLocale()
  {
    if(Locale::getCount() === 1) {
      $this->locale = Locale::getAvailableLocales()[0];
    } else if(Locale::getCount() > 1 && env() !== 'development') {
      $this->ip = ClientIP::get();
      $this->geocoder = new Geocoder($this->ip);

      $this->locale = $this->geocoder->getLocale();
    } else {
      $this->locale = config('locale.code');
    }
  }

  /**
   * Create L10n.
   */
  private function createL10n()
  {
    $l10nFilePath = $this->twig->renderString(path('locales'), ['locale' => $this->locale]);

    if(file_exists($l10nFilePath)) {
      $this->l10n = NConfig::load($l10nFilePath)->all();
    }
  }

  /**
   * Create user agent.
   */
  private function createUserAgent()
  {
    $this->userAgent = new UserAgent();
  }

  /**
   * Create router.
   */
  private function createRouter()
  {
    if(file_exists(path('routes'))) {
      $routesConfig = NConfig::load(path('routes'));
      $this->routes = $routesConfig->all();

      $this->router = Router::parseConfig($this->routes);
    }
  }

  /**
   * Render.
   */
  public function render()
  {
    $currentRoute = $this->router->matchCurrentRequest();

    $data = [
      'environment' => $this->environment,
      'hash' => $this->hash,
      'route' => [
        'name' => $currentRoute->getName(),
        'parameters' => $currentRoute->getParameters()
      ],
      'locale' => [
        'code' => $this->locale,
        'available' => (array) Locale::getAvailableLocales(),
        'country' => $this->geocoder instanceof Geocoder ? $this->geocoder->getCountry() : config('locale.country'),
        'l10n' => $this->l10n
      ],
      'browser' => $this->userAgent->getBrowser(),
      'operatingSystem' => $this->userAgent->getOperatingSystem(),
      'device' => $this->userAgent->getDevice(),
      'bot' => $this->userAgent->getBot()
    ];

    $data = array_merge_deep($data, config('twig.extraData'));
    return $this->twig->render('@layouts/'. config('twig.layout') . config('twig.extension'), $data);
  }

  /**
   * Get configuration.
   */
  public function getConfig()
  {
    return $this->config;
  }

  /**
   * Set configuration.
   *
   * @param AbstractConfig $config Configuration.
   */
  public function setConfig(AbstractConfig $config)
  {
    $this->config = $config;
  }

  /**
   * Get Twig instance.
   */
  public function getTwig()
  {
    return $this->twig;
  }

  /**
   * Get application instance (singleton).
   */
  public static function getInstance()
  {
    if (!(self::$instance instanceof self)) {
      self::$instance = new self();
    }
    return self::$instance;
  }
}
