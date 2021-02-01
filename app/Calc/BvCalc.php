<?php

namespace App\Calc;

class BvCalc
{
    /**
     * @param LineItem[] $lines
     * @return LineItem[]
     */
    public function extractBVLines(array $lines)
    {
        return array_filter($lines, function ($line) {
            return $line->isBv();
        });
    }
}
