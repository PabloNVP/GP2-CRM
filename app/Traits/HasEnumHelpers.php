<?php

namespace App\Traits;

use UnitEnum;
use BackedEnum;

trait HasEnumHelpers
{
    /**
     * Obtiene los valores para usar en migraciones de base de datos.
     * Funciona para BackedEnums (con valor) y UnitEnums (solo nombre).
     */
    public static function getDatabaseValues(): array
    {
        return array_map(function (UnitEnum $case) {
            return $case instanceof BackedEnum ? $case->value : $case->name;
        }, self::cases());
    }

    /**
     * Obtiene el valor por defecto del primer caso.
     */
    public static function getDefaultValue(): mixed
    {
        $firstCase = self::cases()[0] ?? null;
        
        if (!$firstCase) {
            return null;
        }

        return $firstCase instanceof BackedEnum ? $firstCase->value : $firstCase->name;
    }
    
    /**
     * Obtiene todos los nombres de los casos.
     */
    public static function getNames(): array
    {
        return array_map(fn($case) => $case->name, self::cases());
    }
}
