<?php

/**
 * Config.
 */

namespace Quark;

use Noodlehaus\AbstractConfig;

use Quark\Twig\Extensions\ManifestExtension;

/**
 * Config class.
 */
class Config extends AbstractConfig
{
  /**
   * Creates an instance of Config.
   *
   * @param array $data Data.
   */
  public function __construct($data = [])
  {
    $this->data = array_merge_deep($this->getDefaults(), $data);
  }

  /**
   * Default configuration.
   * @todo Uncomment twig 'components' path.
   *
   * @return array Default configuration.
   */
  protected function getDefaults()
  {
    return [
      'locale' => [
        'code' => 'en',
        'country' => '',
        'redirectIfOne' => false
      ],
      'paths' => [
        'locales' => base_path('locales/{{locale}}.yml'),
        'routes' => base_path('routes.yml'),
        'manifest' => base_path('manifest.json')
      ],
      'twig' => [
        'layout' => 'default',
        'extension' => '.twig',
        'cache' => base_path('cache'),
        'extraData' => [],
        'paths' => [
          'views' => base_path('views'),
          'layouts' => base_path('views/layouts'),
          'pages' => base_path('views/pages'),
          // 'components' => base_path('views/components')
        ],
        'extensions' => [
          'manifest' => new ManifestExtension(),
          'html_compress' => new \nochso\HtmlCompressTwig\Extension()
        ],
        'filters' => [],
        'globals' => [],
        'functions' => [],
        'tests' => []
      ]
    ];
  }
}