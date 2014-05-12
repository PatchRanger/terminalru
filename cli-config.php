<?php

// @todo Make sure it is done right.
//require_once '../../lib/vendor/doctrine-common/lib/Doctrine/Common/ClassLoader.php';
//require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/vendor/doctrine/common/lib/Doctrine/Common/ClassLoader.php';

$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\ORM', realpath(__DIR__ . '/../../lib'));
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\DBAL', realpath(__DIR__ . '/../../lib/vendor/doctrine-dbal/lib'));
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\Common', realpath(__DIR__ . '/../../lib/vendor/doctrine-common/lib'));
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('Symfony', realpath(__DIR__ . '/../../lib/vendor'));
$classLoader->register();
// @todo Do we really need it?
$classLoader = new \Doctrine\Common\ClassLoader('Entities', __DIR__);
$classLoader->register();
// @todo Do we really need it?
$classLoader = new \Doctrine\Common\ClassLoader('Proxies', __DIR__);
$classLoader->register();

$config = new \Doctrine\ORM\Configuration();
$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
// @todo Remove it.
//$driverImpl = $config->newDefaultAnnotationDriver(array(__DIR__ . "/src/Terminalru/Entity"), FALSE);
$driverImpl = new \Doctrine\ORM\Mapping\Driver\YamlDriver(array(__DIR__ . "/src/Terminalru/Entity/mapping/yml"));
$config->setMetadataDriverImpl($driverImpl);
// @todo Do we really need it?
$config->setProxyDir(__DIR__ . '/Proxies');
$config->setProxyNamespace('Proxies');

$connectionOptions = array(
  // @todo Change to real DB settings.
  'driver' => 'pdo_sqlite',
  'path' => __DIR__ . '/sqlite.db'
);

$em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);

$helpers = new Symfony\Component\Console\Helper\HelperSet(array(
  'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
  'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
));
