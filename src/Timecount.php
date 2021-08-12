<?php

declare(strict_types=1);

namespace davekok\calendar;

class Timecount
{
    public const OF_SECOND     = 1,
    public const OF_MINUTE     = 60 * self::OF_SECOND,
    public const OF_HOUR       = 60 * self::OF_MINUTE,
    public const OF_DAY        = 24 * self::OF_HOUR,
    public const OF_1969YRS    = Daycount::OF_1969YRS * self::OF_DAY,
    public const OF_9999YRS    = Daycount::OF_9999YRS * self::OF_DAY,
}
