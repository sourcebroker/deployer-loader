<?php

/*
 * DEPRECATED. Deployer use now low amount of dependencies and this loader should not be needed anymore.
 *
 *
 *
 *
 * If Deployer is used from phar file (and this is the preferred way to not pollute project with dependencies of
 * deployment stuff) then it is already including his vendor/autoload.php. If we will require again vendor/autoload.php
 * from our project then it can overwrite libraries leading to unexpected errors.
 *
 * This spl_autoload_register will register after deployer phar so there should not be problems like above. First
 * classes from Deployer phar autoload will be initiated and if they will not exists they will fallback to classes
 * defined here - so all defined in our project vendors psr4 areas.
*/

spl_autoload_register(static function ($className) {

    $dir = __DIR__;
    while ((!is_file($dir . '/composer.json') && !is_file($dir . '/root_dir') && !is_file($dir . '/deploy.php')) || basename($dir) === 'deployer-loader') {
        if ($dir === \dirname($dir)) {
            break;
        }
        $dir = \dirname($dir);
    }

    $autoloadPsr4 = require($dir . '/vendor/composer/autoload_psr4.php');
    foreach ($autoloadPsr4 as $namespace => $namespacePath) {
        if (!empty($namespace) && strpos($className, $namespace) === 0) {
            $includeClassPath = $namespacePath[0] . '/' .
                str_replace('\\', '/', substr($className, strlen($namespace))) . '.php';
            if (file_exists($includeClassPath)) {
                /** @noinspection PhpIncludeInspection */
                include($includeClassPath);
                return;
            }
        }
    }
});
