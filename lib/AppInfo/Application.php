<?php

namespace OCA\NextRCP\AppInfo;

require_once __DIR__ . '/../../vendor/autoload.php';

use \OCP\AppFramework\App;
use \OCP\AppFramework\IAppContainer;


use \OCA\NextRCP\Controller\AjaxController;
//use \OCA\NextRCP\Service\AuthorService;
//use \OCA\NextRCP\Db\AuthorMapper;
use OCA\NextRCP\Db\WorkIntervalMapper;
use OCA\NextRCP\Controller\PageController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use OCP\IL10N;

class Application extends App {


  /**
   * Define your dependencies in here
   */
  public function __construct(array $urlParams=array()){
    parent::__construct('nextrcp', $urlParams);

    if (!\class_exists('\OCA\NextRCP\AppFramework\Db\CompatibleMapper')) {
        if (\class_exists(\OCP\AppFramework\Db\Mapper::class)) {
            \class_alias(\OCP\AppFramework\Db\Mapper::class, 'OCA\NextRCP\AppFramework\Db\CompatibleMapper');
        } else {
            \class_alias(\OCA\NextRCP\AppFramework\Db\OldNextcloudMapper::class, 'OCA\NextRCP\AppFramework\Db\CompatibleMapper');
        }
    }

    $container = $this->getContainer();
    /**
     * Controllers
     */
    
    

    $container->registerService('WorkIntervalMapper', function($c){
      return new WorkIntervalMapper(
        $c->query('ServerContainer')->getDatabaseConnection()
      );
    });
    $container->registerService('ReportItemMapper', function($c){
      return new WorkIntervalMapper(
        $c->query('ServerContainer')->getDatabaseConnection()
      );
    });

  }
}
