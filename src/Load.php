<?php

namespace SourceBroker\DeployerLoader;

use SourceBroker\DeployerLoader\Utility\FileUtility;

class Load
{
    protected FileUtility $fileUtility;

    protected string $projectRoot;

    protected array $loaders = [];

    protected array $rawLoaderConfigurations = [];

    protected array $validLoaderTypes = ['file_phar', 'path', 'package'];

    public function __construct(array $loaderConfigurations = [])
    {
        $this->fileUtility = new FileUtility();
        $this->projectRoot = $this->fileUtility->projectRootAbsolutePath(__DIR__);

        $this->discoverLoaderConfigurations($loaderConfigurations);
        $this->findConflicts();
        $this->processRawConfigurations();
        $this->processCollectedLoaders();
    }

    protected function discoverLoaderConfigurations(array $loaderConfigurations, ?string $sourcePackageName = null): void
    {
        foreach ($loaderConfigurations as $loaderConfig) {
            if (isset($loaderConfig['get'])) {
                $packageName = $loaderConfig['get'];
                $composerJsonPath = $this->projectRoot . '/vendor/' . $packageName . '/composer.json';
                if (file_exists($composerJsonPath)) {
                    $composerJson = json_decode(file_get_contents($composerJsonPath), true);
                    $loaderFileName = $composerJson['extra']['sourcebroker/deployer']['loader-file'] ?? 'config/loader.php';
                    $loaderFilePath = $this->projectRoot . '/vendor/' . $packageName . '/' . ltrim($loaderFileName, '/');

                    if (file_exists($loaderFilePath)) {
                        $newLoaderConfigs = require $loaderFilePath;
                        if (is_array($newLoaderConfigs)) {
                            $this->discoverLoaderConfigurations($newLoaderConfigs, $packageName);
                        }
                    }
                }
            } else {
                $this->rawLoaderConfigurations[] = [
                    'config' => $loaderConfig,
                    'source' => $sourcePackageName
                ];
            }
        }
    }

    protected function processRawConfigurations(): void
    {
        foreach ($this->rawLoaderConfigurations as $entry) {
            $loaderConfig = $entry['config'];
            $loaderType = null;
            $loaderTarget = null;
            foreach ($loaderConfig as $key => $value) {
                if (in_array($key, $this->validLoaderTypes, true)) {
                    $loaderType = $key;
                    $loaderTarget = $value;
                    break;
                }
            }

            if ($loaderType) {
                $this->loaders[] = [
                    'name' => '',
                    'type' => $loaderType,
                    'target' => $loaderTarget,
                    'excludePattern' => $loaderConfig['excludePattern'] ?? null
                ];
            }
        }
    }

    protected function processCollectedLoaders(): void
    {
        foreach ($this->loaders as $loader) {
            if (!in_array($loader['type'], $this->validLoaderTypes, true)) {
                throw new \RuntimeException('Invalid loader type: ' . $loader['type']);
            }
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

    protected function findConflicts(): void
    {
        $typeToValues = [];
        foreach ($this->rawLoaderConfigurations as $entry) {
            $loaderConfig = $entry['config'];
            foreach ($loaderConfig as $type => $value) {
                if (!in_array($type, $this->validLoaderTypes, true)) {
                    continue;
                }
                if (!isset($typeToValues[$type])) {
                    $typeToValues[$type] = [];
                }
                $typeToValues[$type][] = $value;
            }
        }

        foreach ($this->rawLoaderConfigurations as $entry) {
            $loaderConfig = $entry['config'];
            $sourcePackage = $entry['source'];

            if (isset($loaderConfig['conflict']) && is_array($loaderConfig['conflict'])) {
                foreach ($loaderConfig['conflict'] as $conflictType => $conflictValues) {
                    if (!is_array($conflictValues)) {
                        $conflictValues = [$conflictValues];
                    }
                    foreach ($conflictValues as $conflictValue) {
                        if (isset($typeToValues[$conflictType]) && in_array($conflictValue, $typeToValues[$conflictType], true)) {
                            $sourceDescription = $sourcePackage ? "package '" . $sourcePackage . "'" : 'the main deploy.php configuration';
                            throw new \RuntimeException(
                                "Configuration conflict: The loader from {$sourceDescription} specifies that '{$conflictType}: {$conflictValue}' creates a conflict."
                            );
                        }
                    }
                }
            }
        }
    }
}
