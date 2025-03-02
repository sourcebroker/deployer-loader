<?php

namespace SourceBroker\DeployerLoader\Utility;

use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class FileUtility
{
    public function requireFilesFromDirectoryRecursively($absolutePath, $excludePattern = null): void
    {
        if (is_dir($absolutePath)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($absolutePath),
                RecursiveIteratorIterator::SELF_FIRST
            );
            $filesToRequire = [];
            foreach ($iterator as $file) {
                /** @var $file SplFileInfo */
                if ($file->isFile()) {
                    $excludeMatch = null;
                    if ($excludePattern !== null) {
                        $excludeMatch = preg_match($excludePattern, $file->getFilename());
                    }
                    if ($excludeMatch !== 1 && $file->getExtension() === 'php') {
                        try {
                            $filesToRequire[] = $file->getRealPath();
                        } catch (Exception $exception) {
                            echo $exception->getMessage();
                        }
                    }
                }
            }
            sort($filesToRequire);
            foreach ($filesToRequire as $file) {
                require_once $file;
            }
        }
    }

    public function projectRootAbsolutePath(string $dir): string
    {
        while ((!is_file($dir . '/composer.json') && !is_file($dir . '/root_dir') && !is_file($dir . '/deploy.php')) || basename($dir) === 'deployer-loader') {
            if ($dir === \dirname($dir)) {
                break;
            }
            $dir = \dirname($dir);
        }

        return $dir;
    }

}
