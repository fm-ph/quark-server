<?php

/**
 * Config.
 */

namespace Quark;

use Noodlehaus\AbstractConfig;

use Quark\Twig\Extensions\QuarkExtension;

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
   *
   * @return array Default configuration.
   */
  protected function getDefaults()
  {
    return [
      'old_browser' => [
        [
          'name' => 'Internet Explorer',
          'comparison' => '<=',
          'version' => '10'
        ]
      ],
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
        'layouts' => [
          'default' => 'default',
          'old_browser' => 'old'
        ],
        'extension' => '.twig',
        'cache' => base_path('cache'),
        'extraData' => [],
        'paths' => [
          'views' => base_path('views'),
          'layouts' => base_path('views/layouts'),
          'pages' => base_path('views/pages'),
          'components' => base_path('views/components')
        ],
        'extensions' => [
          'quark' => new QuarkExtension(),
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
