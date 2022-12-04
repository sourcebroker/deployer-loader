<?php

namespace SourceBroker\DeployerLoader;

use SourceBroker\DeployerLoader\Utility\FileUtility;

/**
 * Class Load
 *
 * @package SourceBroker\DeployerLoader\Load
 */
class Load
{
    public function __construct($locationsToLoad)
    {
        $fileUtility = new FileUtility();
        foreach ($locationsToLoad as $locationToLoad) {
            if (!empty($locationToLoad['path'])) {
                $absolutePath = $this->projectRootAbsolutePath() . '/' . ltrim($locationToLoad['path'], '/\\');
                if (is_dir($absolutePath)) {
                    $fileUtility->requireFilesFromDirectoryRecursively(
                        $absolutePath,
                        !empty($locationToLoad['excludePattern']) ? $locationToLoad['excludePattern'] : null);
                } else {
                    require($absolutePath);
                }
            }
        }
    }

    /**
     * Return absolute path to project root so we can add it to relative pathes.
     *
     * @return bool|string
     */
    public function projectRootAbsolutePath()
    {
        $dir = __DIR__;
        while (!is_file($dir . '/composer.json') || basename($dir) === 'deployer-loader') {
            if ($dir === \dirname($dir)) {
                break;
            }
            $dir = \dirname($dir);
        }

        return $dir;
    }
}
