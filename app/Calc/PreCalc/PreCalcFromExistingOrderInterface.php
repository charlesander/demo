<?php

namespace App\Calc\PreCalc;

interface PreCalcFromExistingOrderInterface
{
    public function __construct(
        string $orderId,
        int $orderStatus
    );
}
