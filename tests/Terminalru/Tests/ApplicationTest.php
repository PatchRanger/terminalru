<?php


namespace Terminalru\Tests;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Terminalru\App;

/**
 * Application test cases.
 *
 * @author Dmitry Danilson <patchranger@gmail.com>
 */
// @todo Replace it with just \PHPUnit_Framework_TestCase as we don't need testing using datasets.
//class ApplicationTest extends \PHPUnit_Framework_TestCase
class ApplicationTest extends \PHPUnit_Extensions_Database_TestCase
{
  // Only instantiate pdo once for test clean-up/fixture load.
  static private $pdo = null;

  // Only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test.
  private $conn = null;

  public function getConnection()
  {
    if ($this->conn === null) {
      if (self::$pdo == null) {
        // @todo Switch to real database.
        //self::$pdo = new PDO('mysql:host=localhost;dbname=cinema_cashier', 'root', 'root');
        //self::$pdo = new PDO('sqlite::memory:');
        self::$pdo = new \PDO('sqlite:' . __DIR__ . '/../../../sqlite.db');
      }
      //$this->conn = $this->createDefaultDBConnection(self::$pdo, ':memory:');
      $this->conn = $this->createDefaultDBConnection(self::$pdo, 'cinema_cashier');
    }
    return $this->conn;
  }

  public function getDataSet()
  {
    // Clone database from file-based to memory-based.
    $ds = new \PHPUnit_Extensions_Database_DataSet_QueryDataSet($this->getConnection());
    $ds->addTable('cinema');
    return $ds;
  }

  public function setUp()
  {
    // Init the application for common usage.
    $this->app = require __DIR__.'/../../../app.php';
    // Fill the database with default content.
    $this->app->defaultContent();
  }

  protected function checkResponse($url, $method = 'get', $statusCode = 200, $message = NULL)
  {
    $request = Request::create($url, $method);
    $response = $this->app->handle($request);
    $this->assertEquals($statusCode, $response->getStatusCode(), "{$url}: Status code {$response->getStatusCode()} != {$statusCode}");
    if (!empty($message)) {
      $this->assertEquals($message, $response->statusText, "{$url}: Status text {$response->statusText} != {$message}");
    }
    return $response;
  }

  public function testHomepage() {
    $this->checkResponse('/');
  }

  public function testExistingCategory() {
    $this->checkResponse('/category1');
  }

  public function testNonExistingCategory() {
    $this->checkResponse('/non-existing-category', 'get', 404);
  }
}
