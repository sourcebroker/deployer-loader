<?php

namespace SourceBroker\DeployerLoader\Utility;

use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Class FileUtility
 *
 * @package SourceBroker\DeployerLoader\Utility
 */
class FileUtility
{
    /**
     * @param $absolutePath
     * @param null $excludePattern
     */
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
}
