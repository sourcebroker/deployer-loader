<?php

/* If Deployer is used from phar file (and this is the preferred way to not pollute project with dependencies of
 * deployment stuff) then it is already including his vendor/autoload.php. If we will require again vendor/autoload.php
 * from our project then it can overwrite libraries leading to unexpected errors.
 *
 * This spl_autoload_register will register after deployer composer so there should not be problems like above. First
 * classes from Deployer composer autoload will be initiated and if they will not exists they will fallback to classes
 * defined here - so all defined in our project vendors psr4 areas.
*/

spl_autoload_register(function ($className) {
    if (file_exists(__DIR__ . '/../../../vendor/composer/autoload_psr4.php')) {
        /** @noinspection PhpIncludeInspection */
        $autoloadPsr4 = include(__DIR__ . '/../../../vendor/composer/autoload_psr4.php');
    } else {
        throw new \Exception('Can not load: "' . realpath(__DIR__ . '/../../../vendor/composer/autoload_psr4.php') . '"');
    }
    foreach ($autoloadPsr4 as $namespace => $namespacePath) {
        if (strpos($className, $namespace) === 0) {
            $requireClassPath = $namespacePath[0] . '/' .
                str_replace('\\', '/', substr($className, strlen($namespace))) . '.php';
            if (file_exists($requireClassPath)) {
                /** @noinspection PhpIncludeInspection */
                include($requireClassPath);
            } else {
                throw new \Exception('Can not find: ' . $requireClassPath);
            }
        }
    }
});