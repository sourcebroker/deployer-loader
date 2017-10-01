deployer-loader
===============
|

.. image:: http://img.shields.io/packagist/v/sourcebroker/deployer-loader.svg?style=flat
   :target: https://packagist.org/packages/sourcebroker/deployer-loader

.. image:: https://img.shields.io/badge/license-MIT-blue.svg?style=flat
   :target: https://packagist.org/packages/sourcebroker/deployer-loader

|

.. contents:: :local:

What does it do?
----------------

This package allows to:

1. Register your project vendor classes to be used in deploy.php. Read "Include class loader" for more info why you
   should not include your project vendor/autoload.php in deploy.php.
2. Load single task/setting file.
3. Load multiple tasks/settings files from folders.

Installation
------------
::

  composer require sourcebroker/deployer-loader ^1.0.0


Usage
-----

Include class loader
++++++++++++++++++++

If Deployer is used as phar directly or by ./vendor/bin/dep form deployer/dist (and this is the preferred way to not
pollute project with dependencies of deployment stuff) then it is already including his own vendor/autoload.php. If in
deploy.php file we will require new vendor/autoload.php from our project then its like asking for troubles because we
are joining two autoload with not synchronized dependencies. The second composer autoload is overwriting libraries from
first autoload leading to situation that deployer will use newer(or older) libraries from your project.

The solution is to include in deploy.php the autoload.php from sourcebroker/deployer-loader.

Using spl_autoload_register() it will register new closure function to find classes and it will register itself after
composer autoload. So first classes from Deployer composer autoload will be initiated and if they will not exists
there will be fallback to classes from the main project vendors.

How to use it ? Just include autoload at the beginning of your deploy.php (and remove vendor/autoload.php if you had one)
::

  require_once(__DIR__ . '/vendor/sourcebroker/deployer-loader/autoload.php');


After this point in code you can use all vendor classes declared in psr4 of your composer.json files.


Loading deployer files with task definitions
++++++++++++++++++++++++++++++++++++++++++++

The package sourcebroker/deployer-loader allows you also to include single files or bunch of files from folder
(recursively).

- Example for loading single file:

  ::

   new \SourceBroker\DeployerLoader\Load(
      [path => 'vendor/sourcebroker/deployer-extended-database/deployer/db/task/db:copy.php'],
      [path => 'vendor/sourcebroker/deployer-extended-database/deployer/db/task/db:move.php'],
   );

- Example for loading all files from folder recursively:

  ::

   new \SourceBroker\DeployerLoader\Load(
      [
        path => 'vendor/sourcebroker/deployer-extended-database/deployer/db/'
        excludePattern => '/move/'
      ],
      [
        path => 'vendor/sourcebroker/deployer-extended-media/deployer/media/'
      ],
   );

  You can use preg_match "excludePattern" to exclude files.


Changelog
---------

See https://github.com/sourcebroker/deployer-loader/blob/master/CHANGELOG.rst
