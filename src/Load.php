<?php

namespace SourceBroker\DeployerLoader;

use SourceBroker\DeployerLoader\Utility\FileUtility;

class Load
{
    protected FileUtility $fileUtility;

    protected string $projectRoot;

    protected array $loaders = [];

    public function __construct(array $loaderConfigurations = [])
    {
        $this->fileUtility = new FileUtility();
        $this->projectRoot = $this->fileUtility->projectRootAbsolutePath(__DIR__);

        $this->collectLoaderConfigurations($loaderConfigurations);
        $this->processCollectedLoaders();
    }

    protected function collectLoaderConfigurations(array $loaderConfigurations): void
    {
        foreach ($loaderConfigurations as $loaderConfig) {
            foreach ($loaderConfig as $type => $target) {
                if ($type === 'get') {
                    $this->collectLoaderFromPackage($target);
                } else {
                    $this->loaders[] = [
                        'type' => $type,
                        'target' => $target,
                        'excludePattern' => $loaderConfig['excludePattern'] ?? null
                    ];
                }
            }
        }
    }

    protected function collectLoaderFromPackage(string $packageName): void
    {
        $composerJsonPath = $this->projectRoot . '/vendor/' . $packageName . '/composer.json';
        if (file_exists($composerJsonPath)) {
            $composerJson = json_decode(file_get_contents($composerJsonPath), true);
            if (isset($composerJson['extra']['sourcebroker/deployer']['loader-file'])) {
                $loaderFilePath = $this->projectRoot . '/vendor/' . $packageName . '/' . $composerJson['extra']['sourcebroker/deployer']['loader-file'];
            } else {
                $loaderFilePath = $this->projectRoot . '/vendor/' . $packageName . '/config/loader.php';
            }

            if (file_exists($loaderFilePath)) {
                $loaderConfigs = require $loaderFilePath;
                if (is_array($loaderConfigs)) {
                    $this->collectLoaderConfigurations($loaderConfigs);
                }
            }
        }
    }

    protected function processCollectedLoaders(): void
    {
        foreach ($this->loaders as $loader) {
            switch ($loader['type']) {
                case 'file_phar':
                    $this->loadFilePhar($loader['target']);
                    break;
                case 'path':
                    $this->loadPath($loader['target'], $loader['excludePattern'] ?? null);
                    break;
                case 'package':
                    $this->loadPackage($loader['target']);
                    break;
            }
        }
    }

    protected function loadPackage(string $packageName): void
    {
        $deployerPath = $this->projectRoot . '/vendor/' . $packageName . '/deployer';
        if (is_dir($deployerPath)) {
            $this->fileUtility->requireFilesFromDirectoryRecursively($deployerPath);
        }
    }

    protected function loadFilePhar(string $target): void
    {
        require_once($target);
    }

    protected function loadPath(string $target, string $excludePattern = null): void
    {
        $absolutePath = $this->projectRoot . '/' . ltrim($target, '/\\');
        if (is_dir($absolutePath)) {
            $this->fileUtility->requireFilesFromDirectoryRecursively($absolutePath, $excludePattern);
        } else {
            require_once($absolutePath);
        }
    }
}
