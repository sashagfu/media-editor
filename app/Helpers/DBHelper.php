<?php

namespace App\Helpers;

final class DBHelper
{
    public static function getMapByModel(string $model)
    {
        $maps = \Illuminate\Database\Eloquent\Relations\Relation::morphMap();

        $found = array_search($model, $maps);

        if (!$found) {
            throw new \RangeException($model . ' is not defined in morphMap array!');
        }

        return $found;
    }

    public static function getModelByMap(string $map)
    {
        $maps = \Illuminate\Database\Eloquent\Relations\Relation::morphMap();

        if (empty($maps[$map])) {
            throw new \RangeException($map . ' is not defined in morphMap array!');
        }

        return $maps[$map];
    }
}
