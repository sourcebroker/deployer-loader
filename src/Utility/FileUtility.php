<?php

namespace SourceBroker\DeployerLoader\Utility;

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
    public function requireFilesFromDirectoryReqursively($absolutePath, $excludePattern = null)
    {
        if (is_dir($absolutePath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($absolutePath),
                \RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($iterator as $file) {
                /** @var $file \SplFileInfo */
                if ($file->isFile()) {
                    $excludeMatch = null;
                    if ($excludePattern !== null) {
                        $excludeMatch = preg_match($excludePattern, $file->getFilename());
                    }
                    if ($file->getExtension() == 'php' && $excludeMatch !== 1) {
                        /** @noinspection PhpIncludeInspection */
                        require_once $file->getRealPath();
                    }
                }
            }
        }
    }

    /**
     * RequireFile
     *
     * @param $absolutePath
     */
    public function requireFile($absolutePath)
    {
        if (file_exists($absolutePath)) {
            /** @noinspection PhpIncludeInspection */
            require_once $absolutePath;
        }
    }
}
