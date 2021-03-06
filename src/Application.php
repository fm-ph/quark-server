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
    $this->createConfig($config);
    $this->createManifest();

    if(env() === 'development') {
      $this->createWhoops();
    }

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
   *
   * @return string Rendered HTML.
   */
  public function render()
  {
    $currentRoute = $this->router->matchCurrentRequest();

    $data = [
      'environment' => $this->environment,
      'hash' => $this->hash,
      'route' => [
        'name' => $currentRoute->getName(),
        'parameters' => $currentRoute->getParameters(),
        'isFirstRoute' => true,
        'lastRoute' => null
      ],
      'locale' => [
        'code' => $this->locale,
        'available' => (array) Locale::getAvailableLocales(),
        'country' => $this->geocoder instanceof Geocoder ? $this->geocoder->getCountry() : config('locale.country')
      ],
      'l10n' => $this->l10n,
      'browser' => $this->userAgent->getBrowser(),
      'engine' => $this->userAgent->getEngine(),
      'operatingSystem' => $this->userAgent->getOperatingSystem(),
      'device' => $this->userAgent->getDevice(),
      'bot' => $this->userAgent->getBot()
    ];

    $data = array_merge_deep($data, config('twig.extraData'));
    $html = $this->twig->render('@layouts/'. $this->getLayoutName($currentRoute) . config('twig.extension'), $data);

    $doc = new \DOMDocument();
    @$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

    return $this->parseComponentsRecursive($doc, $data, true);
  }

  /**
   * Parse components recursively.
   *
   * @param \DOMDocument $doc DOMDocument.
   * @param array $data Data.
   * @param bool $first First parse.
   * 
   * @return string Parsed HTML.
   */
  private function parseComponentsRecursive($doc, $data, $first = false)
  {
    $xpath = new \DOMXpath($doc);
    $nodeList = $xpath->query('//*[@data-component]');

    $count = $nodeList->length;

    if($count > 0) {
      foreach ($nodeList as $node) {
        if(!empty($node->getAttribute('data-component'))) {
          $componentName = $node->getAttribute('data-component');

          $attributesBlacklist = ['data-component', 'data-prerendered', 'data-ref', 'data-el'];

          // Parse all attributes and inject them in data Twig
          foreach ($node->attributes as $attrName => $attrNode) {
            if(!in_array($attrName, $attributesBlacklist) && starts_with($attrName, 'data-')) {
              $data[str_replace('data-', '', $attrName)] = $attrNode->nodeValue;
            }
          }

          $componentRenderedString = $this->twig->render('@components/'. $componentName . '/template' . config('twig.extension'), $data);

          $element = $this->createElementFromString($doc, $componentRenderedString);

          $element->removeAttribute('data-component');
          $element->setAttribute('data-prerendered', $componentName);

          // Proxy all 'data-' except 'data-component'
          foreach ($node->attributes as $attrName => $attrNode) {
            if($attrName !== 'data-component' && starts_with($attrName, 'data-')) {
              $element->setAttribute($attrName, $attrNode->nodeValue);
            }
          }

          $node->parentNode->replaceChild($element, $node);
        }
      }

      $this->parseComponentsRecursive($doc, $data, false);
    }

    return html_entity_decode($doc->saveHTML());
  }

  /**
   * Create DOMElement from HTML string.
   *
   * @param \DOMDocument $doc Document.
   * @param string $html HTML to convert.
   * 
   * @return \DOMNode Converted DOMNode.
   */
  private function createElementFromString($doc, $html)
  {
    $d = new \DOMDocument();
    @$d->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

    return $doc->importNode($d->documentElement->firstChild->firstChild, true);
  }

  /**
   * Get twig layout name.
   *
   * @param \Quark\Router\Route $currentRoute Current route.
   * 
   * @return string Twig layout name.
   */
  private function getLayoutName($currentRoute)
  {
    $name = config('twig.layouts.default');

    if($this->userAgent->isOldBrowser()) {
      $name = config('twig.layouts.old_browser');
    } else if (!is_null($currentRoute->getLayout())) {
      $name = $currentRoute->getLayout();
    }

    return $name;
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
