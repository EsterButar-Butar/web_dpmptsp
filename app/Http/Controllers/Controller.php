<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Parse formatted number string into float.
     * 
     * @param mixed $value
     * @return float
     */
    protected function parseNumber($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
            return (float) $value;
        }

        return 0.0;
    }
}
