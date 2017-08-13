<?php

namespace OCA\FreeContent\AppInfo;

use \OCP\AppFramework\App;

class Application extends App {


  /**
   * Define your dependencies in here
   */
  public function __construct(array $urlParams=array()){
    parent::__construct('free-content', $urlParams);

    $container = $this->getContainer();
  }
}