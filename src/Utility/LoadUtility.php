<?php

declare(strict_types=1);

namespace SourceBroker\DeployerLoader\Utility;

class LoadUtility
{
    public static array $loadedItems = [];

    public static function isLoaded(array $itemToCheck): bool
    {
        if (empty($itemToCheck)) {
            return false;
        }
        $typeToCheck = key($itemToCheck);
        $valueToCheck = current($itemToCheck);

        foreach (self::$loadedItems as $loadedItem) {
            if (isset($loadedItem[$typeToCheck]) && $loadedItem[$typeToCheck] === $valueToCheck) {
                return true;
            }
        }
        return false;
    }
}
