<?php

namespace App\Calc;

use App\Calc\OrderCalc\OrderCalc;
use App\Models\LineItem;
use App\Models\LineItemDiscount;

class DiscountCalc
{

    /**
     * @param float $percentageOfGrossThatIsVATable
     * @param float $grossTotalReduction
     * @return float
     */
    public function calculateVatableGrossTotalReduction(
        float $percentageOfGrossThatIsVATable,
        float $grossTotalReduction
    ): float {
        $vatableGrossTotalReduction = ($grossTotalReduction / 100) * $percentageOfGrossThatIsVATable;

        return $vatableGrossTotalReduction;
    }

    /**
     * @param $vatableGrossTotalReduction
     * @return float
     */
    public function calculateVatableNetTotalReduction($vatableGrossTotalReduction): float
    {
        $vatableNetTotalReduction = ($vatableGrossTotalReduction / (100 + OrderCalc::VAT_RATE)) * 100;

        return $vatableNetTotalReduction;
    }

    /**
     * @param $vatableGrossTotalReduction
     * @param $vatableNetTotalReduction
     * @return float
     */
    public function calculateVatableVatTotalReduction($vatableGrossTotalReduction, $vatableNetTotalReduction): float
    {
        $vatableVatTotalReduction = $vatableGrossTotalReduction - $vatableNetTotalReduction;

        return $vatableVatTotalReduction;
    }
}
