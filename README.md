Description
===========
Terminalru: Simple product by category listing app.

Based on Silex, Doctrine, SQLite, phpUnit.

Installation
============
The easiest way to try it out is to use Cloud9 service: all necessary environment
is pre-configured - everything is ready to go.

So if you got an email invitation to C9 Terminalru project - you're lucky,
because you don't need to mess up with all of the installation instructions below.

If you didn't - please don't be upset, just follow these straight-forward steps:

1. Download this repository to your local machine and setup it as a site to your
server.
  Apache or built-in PHP server are welcome.
  Make sure .htaccess file is included - otherwise download it manually.

2. Download Composer.
  For the easiest setup use this:

  <code>curl -sS https://getcomposer.org/installer | php</code>

  Use getcomposer.org as a reference for additional info.

3. Run

  <code>composer install</code>

  (or

  <code>php composer.phar install</code>

  it depends on how Composer is installed) at root of the folder, containing the project.
  It will download all necessary libraries.

4. Generate classes.
  Run

  <code>./bin/doctrine orm:generate-entities --generate-annotations=1 src</code>

  It will generate PHP-classes matching ORM entities.

5. Update Composer autoloader.
  Run

  <code>php composer.phar update</code>

6. (optional as pre-filled database is included)
  Switch to MySQL.
  Replace database settings in 'app.php' and 'cli-config.php' from SQLite to MySQL.
  You could skip this step - then pre-filled database is used.

7. (optional as pre-filled database is included, required for re-testing)
  Run

  <code>./bin/doctrine orm:schema-tool:drop --force</code>

  and then

  <code>./bin/doctrine orm:schema-tool:update --force</code>

  It will re-create database schema.

8. (optional as pre-filled database is included, required for re-testing)
  Make your server to handle 'default_content.php' file.
  Just make sure your server is running - and open the file using the corresponding
  site URL.
  Or simply run

  <code>php default_content.php</code>

  It will fill the database in with default content.
  Expected result: you see "Default content successfully created!" message.

9. Open the home page of the site.
  It should display the list of all categories and all products.

10. Run tests to make sure it works.

  <code>./bin/phpunit</code>

Explaining decisions
====================
- Silex: based on Symfony2 components; easy to start though quite powerful;
  switching to Symfony2 is simple.
- SQLite: no installation (bundled with PHP since PHP 5.0); no migration (due to
  Doctrine); switching to MySQL is simple.

Features & Limitations
======================
- phpUnit testing is done on the same database (no re-creation during testing).
  It is necessary to manually re-create tables and fill them in before each test.
  Run './bin/doctrine' to get help of how to manipulate the database schema.
  Otherwise each test will add new portion of default content.
- Tests cover all of the basic functionality needed - as TDD requires.
- There are plenty of growing points to make the application even better.
  Search for "@todo" to find them: https://github.com/PatchRanger/terminalru/search?q="%40todo"
