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
            if(!empty($locationToLoad['path'])) {
                if (is_dir($locationToLoad['path'])) {
                    $fileUtility->requireFilesFromDirectoryReqursively(
                        $locationToLoad['path'],
                        isset($locationToLoad['excludePattern']) ? $locationToLoad['excludePattern'] : null);
                } else {
                    $fileUtility->requireFile($locationToLoad['path']);
                }
            }
        }
    }
}