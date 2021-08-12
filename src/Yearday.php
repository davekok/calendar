<?php

declare(strict_types=1);

namespace davekok\calendar;

class Yearday
{
    public function __construct(public int $year, public int $day, public bool $isLeapYear) {}
}
