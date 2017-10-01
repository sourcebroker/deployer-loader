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
 1) Register your project vendor classes to be used in deploy.php. Read "Include class loader" for more info why you
    should not include your project vendor/autoload.php in deploy.php.
 2) Load single tasks.
 3) Load multiple tasks from folders.

Installation
------------

 ::

      composer require sourcebroker/deployer-loader ^1.0.0


Usage
-----

Include class loader
++++++++++++++++++++

If Deployer is used from phar file (and this is the preferred way to not pollute project with dependencies of
deployment stuff) then it is already including his own vendor/autoload.php. If we will require again vendor/autoload.php
from our project then it can overwrite libraries of Deployer package leading to unexpected errors because code of
Deployer can expect to use some other version of library that the one from your project vendor/autoload.php.

The loader from /vendor/sourcebroker/deployer-loader/autoload.php will register with spl_autoload_register and
will be executed after deployer composer spl_autoload_register. So first classes from Deployer composer autoload will be
initiated and if they will not exists they will fallback to classes supported by
vendor/sourcebroker/deployer-loader/autoload.php

Include class loader at the beginning of your deploy.php.

 ::

    require_once(__DIR__ . '/vendor/sourcebroker/deployer-loader/autoload.php');


After this point in code you can use all vendor classes declared in psr4 of your composer.json files.


Include deployer recipes and settings
+++++++++++++++++++++++++++++++++++++

- Single file:

 ::

   new \SourceBroker\DeployerLoader\Load(
      [path => 'vendor/sourcebroker/deployer-extended-database/deployer/db/task/db:copy.php'],
      [path => 'vendor/sourcebroker/deployer-extended-database/deployer/db/task/db:move.php'],
   );

- All files from folder recursively:

  ::

   new \SourceBroker\DeployerLoader\Load(
      [
        path => 'vendor/sourcebroker/deployer-extended-database/deployer/db/task/'
        excludePattern => '/move/'
      ],
      [
        path => 'vendor/sourcebroker/deployer-extended-media/deployer/media/task/'
      ],
   );

  You can use preg_match "excludePattern" to exclude files.