<?php

require_once __DIR__.'/vendor/autoload.php';

// Just to avoid verbose warnings.
date_default_timezone_set('Europe/Moscow');

$app = new \Terminalru\App();

// @todo Comment when deploying to production.
// Enable error reporting for debug purposes.
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
$app['debug'] = true;

// Register Doctrine DBAL service.
// @see http://silex.sensiolabs.org/doc/providers/doctrine.html
$app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
  'db.options' => array(
    /* @todo Switch to real database.
    'driver' => 'pdo_mysql',
    'dbname' => 'terminalru',
    'host' => '127.0.0.1',
    'user' => 'root',
    'password' => 'root',
    */
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/sqlite.db',
    //'memory' => TRUE,
    'charset' => 'utf8'
  )
));

// Register Doctrine ORM service.
/* @todo Implement APC caching if available.
use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ArrayCache;
*/
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
$app->register(new DoctrineOrmServiceProvider(), array(
  /* @todo Implement proxies.
  'orm.proxies_dir' => __DIR__.'/../cache/doctrine/proxy',
  'orm.proxies_namespace' => 'DoctrineProxy',
  'orm.auto_generate_proxies' => TRUE,
  */
  /* @todo Implement APC caching if available.
  'db.orm.cache' =>
    !$app['debug'] && extension_loaded('apc') ? new ApcCache() : new ArrayCache(),
  */
  'orm.em.options' => array(
    'mappings' => array(
      /* @todo Remove it.
      array(
        'type' => 'annotation',                       // Entity definition.
        'namespace' => 'Terminalru\Entity',           // Namespace for our classes.
        'path' => __DIR__ . '/src/Terminalru/Entity', // Path to our entity classes.
        'use_simple_annotation_reader' => FALSE,
      ),
      */
      array(
        'type' => 'yml',
        'namespace' => 'Terminalru\Entity',
        'path' => __DIR__ . '/src/Terminalru/Entity/mapping/yml',
      ),
    ),
  ),
));

// Register Twig service.
$app->register(new Silex\Provider\TwigServiceProvider(), array(
  'twig.path' => __DIR__.'/views',
));

// Category page together with homepage.
$app->get('/{category_name}', function ($category_name = NULL) use ($app) {
  $em = $app['orm.em'];
  if (!empty($category_name)) {
    // Check category name for existence.
    try {
      $category = $em->getRepository('Terminalru\Entity\Category')->findOneBy(array('name' => $category_name));
    } catch (Exception $e) {
      $app->abort(500, $e->getMessage());
    }
    if (empty($category)) {
      $app->abort(404, "No category with name \"{$category_name}\"");
    }
  }
  // Get all categories.
  //$categories = $em->createQueryBuilder('Terminalru\Entity\Category')->select('name')->getQuery()->execute();
  $categories = $app['db']->fetchAll("SELECT name FROM category");
  $categories_names = array();
  foreach ($categories as $category) {
    $categories_names[] = $category['name'];
  }
  // Get list of products for category.
  //$qb = $em->createQueryBuilder('Terminalru\Entity\Product')->select('name');
  $sql = "SELECT p.name FROM product p";
  // Prepare parameters for rendering.
  $parameters = array();
  if (!empty($category_name)) {
    //$qb->field('categories.name')->equals($category_name);
    $sql = "SELECT DISTINCT p.*
      FROM product p, category c, product_category pc
      WHERE pc.product_id = p.id
      AND pc.category_id = c.id
      AND c.name = :category_name";
    $template = 'category.twig';
    $parameters['category_name'] = $category_name;
  }
  else {
    $template = 'homepage.twig';
  }
  //$products = $qb->getQuery()->execute();
  $products = $app['db']->fetchAll($sql, array('category_name' => $category_name));
  $products_names = array();
  foreach ($products as $product) {
    $products_names[] = $product['name'];
  }
  $parameters['categories'] = $categories_names;
  $parameters['products'] = $products_names;
  // @todo Add pagination.
  // @see https://gist.github.com/SimonSimCity/4594748
  return $app['twig']->render($template, $parameters);
})->value('category_name', NULL);

return $app;
