<?php

/**
 * Twig.
 */

namespace Quark\Twig;

use Twig_Loader_Filesystem;
use Twig_Environment;

/**
 * Twig class.
 */
final class Twig
{
  /**
   * @var \Twig_Loader_Filesystem Loader.
   */
  private $loader;

  /**
   * @var \Twig_Environment Twig.
   */
  private $twig;

  /**
   * Creates an instance of Twig.
   *
   * @param array $options Options.
   */
  function __construct($options = [])
  {
    if(config('twig.cache') && env() !== 'development') {
      $options['cache'] = config('twig.cache');
    }

    if(env() === 'development') {
      $options['debug'] = true;
    }

    $this->loader = new Twig_Loader_Filesystem();
    $this->twig = new Twig_Environment($this->loader, $options);
    
    $this->registerPaths(config('twig.paths'));
    $this->registerExtensions(config('twig.extensions'));
    $this->registerExtensions(config('twig.filters'));
    $this->registerExtensions(config('twig.globals'));
    $this->registerExtensions(config('twig.functions'));
    $this->registerExtensions(config('twig.tests'));
  }

  /**
   * Render a template.
   *
   * @param string $template Template name.
   * @param array $context Parameters to pass to the template.
   * 
   * @return string Rendered template.
   */
  public function render($template, $context = [])
  {
    return $this->twig->render($template, $context);
  }

  /**
   * Render a string template.
   *
   * @param string $template Template string.
   * @param array $context Parameters to pass to the template.
   * 
   * @return string Rendered template.
   */
  public function renderString($template, $context = [])
  {
    return $this->twig->createTemplate($template)->render($context);
  }

  /**
   * Register paths.
   *
   * @param array $paths Paths.
   */
  public function registerPaths($paths)
  {
    foreach ($paths as $pathName => $path) {
      if(file_exists($path)) {
        $this->loader->addPath($path, $pathName);
      }
    }
  }

  /**
   * Register extensions.
   *
   * @param array $extensions Extensions.
   */
  public function registerExtensions($extensions)
  {
    foreach ($extensions as $extension) {
      $this->twig->addExtension($extension);
    }
  }

  /**
   * Register filters.
   *
   * @param array $filters Filters.
   */
  public function registerFilters($filters)
  {
    foreach ($filters as $filter) {
      $this->twig->addFilter($filter);
    }
  }

  /**
   * Register globals.
   *
   * @param array $globals Globals.
   */
  public function registerGlobals($globals)
  {
    foreach ($globals as $global) {
      $this->twig->addGlobal($global);
    }
  }

  /**
   * Register functions.
   *
   * @param array $functions Functions.
   */
  public function registerFunctions($functions)
  {
    foreach ($functions as $function) {
      $this->twig->addFunction($function);
    }
  }

  /**
   * Register tests.
   *
   * @param array $tests Tests.
   */
  public function registerTests($tests)
  {
    foreach ($tests as $test) {
      $this->twig->addTest($test);
    }
  }
}
