<?php

namespace SourceBroker\DeployerLoader;

use SourceBroker\DeployerLoader\Utility\FileUtility;

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

    public function projectRootAbsolutePath(): string
    {
        $dir = __DIR__;
        while ((!is_file($dir . '/composer.json') && !is_file($dir . '/root_dir') && !is_file($dir . '/deploy.php')) || basename($dir) === 'deployer-loader') {
            if ($dir === \dirname($dir)) {
                break;
            }
            $dir = \dirname($dir);
        }

        return $dir;
    }
}
