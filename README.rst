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

This package allows to load:

1) single task or set of tasks from given path -> loader type: ``path``
2) set of tasks from "deployer" folder of composer package -> loader type: ``package``
3) loader config from composer package -> loader type: ``get``


Installation
------------
::

  composer require sourcebroker/deployer-loader


Usage
-----

- TYPE ``path``

  You can load single file or multiple files. You can use ``excludePattern`` to exclude.

  ::

   new \SourceBroker\DeployerLoader\Load(
      ['path' => 'vendor/sourcebroker/deployer-extended-database/deployer/db/task/db:copy.php'],
      ['path' => 'vendor/sourcebroker/deployer-extended-database/deployer/db/task/db:move.php'],
   );

   new \SourceBroker\DeployerLoader\Load(
      ['path' => 'vendor/sourcebroker/deployer-extended-database/deployer/db', 'excludePattern' => '/move/'],
      ['path' => 'vendor/sourcebroker/deployer-extended-media/deployer/media'],
   );


- TYPE ``file_phar``

  A file is loaded from relative to root project. Allows to include Deployer phar file.

  ::

   new \SourceBroker\DeployerLoader\Load(
      ['file_phar' => 'recipe/common.php'],
   );


- TYPE ``package``

  Files are loaded recursively form given package from hardcoded folder ``deployer``.

  ::

   new \SourceBroker\DeployerLoader\Load(
      ['package' => 'sourcebroker/deployer-extended-database'],
      ['package' => 'sourcebroker/deployer-extended-media'],
   );

- TYPE ``get``

  In case of ``get`` first the file with array of loader configurations is read from given package.
  The logic to read the file is like:

  - First it checks if the composer.json file contains the key ``extra.sourcebroker/deployer.loader-file``.
  - If the key exists, it read it and execute inclusions.
  - If the key does not exist, it defaults to the path ``config/loader.php`` within the package directory.

  Then the loader configurations read from that file are executed.

  Example of loader file: https://github.com/sourcebroker/deployer-typo3-database/blob/main/config/loader.php

  ::

   new \SourceBroker\DeployerLoader\Load(
      ['get' => 'sourcebroker/deployer-typo3-database'],
      ['get' => 'sourcebroker/deployer-typo3-media'],
      ['get' => 'sourcebroker/deployer-typo3-deploy-ci'],
   );


Conflict detection
-----------------

If a loader specifies a 'conflict' configuration that matches another loaded configuration, an exception is thrown with
detailed information about the conflict, including which package caused it.

::

    [
        'package' => 'sourcebroker/deployer-typo3-deploy',
        'conflict' => [
            'package' => [
                'sourcebroker/deployer-typo3-deploy-ci'
            ]
        ]
    ],


Check if configuration is loaded
-------------------------------

You can check if a specific configuration item has already been loaded using the ``isLoaded()`` method:

::

    if (\SourceBroker\DeployerLoader\Utility\LoadUtility::isLoaded(['package' => 'sourcebroker/deployer-typo3-deploy'])) {

    }

Changelog
---------

See https://github.com/sourcebroker/deployer-loader/blob/master/CHANGELOG.rst
