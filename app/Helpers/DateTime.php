<?php

namespace App\Helpers;

use DateTimeZone;
use Exception;

class DateTime
{
    const FORMAT = 'Y-m-d H:i:s';

    /**
     * @param string|null $dateTime
     * @return string
     */
    public static function formatShopifyDateTimeToUTCString(?string $dateTime): ?string
    {
        if (!$dateTime) {
            return null;
        }

        $dateTimeObject = static::formatShopifyDateTimeToUTCSDateTimeObject($dateTime);

        return $dateTimeObject->format('Y-m-d H:i:s');
    }

    /**
     * @param string|null $dateTime
     * @return \DateTime
     * @throws Exception
     */
    public static function formatShopifyDateTimeToUTCSDateTimeObject(?string $dateTime): ?\DateTime
    {
        if (!$dateTime) {
            return null;
        }

        $dateTimeObject = new \DateTime($dateTime);
        $dateTimeObject->setTimezone(new DateTimeZone('UTC'));
        return $dateTimeObject;
    }
}
