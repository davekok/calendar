<?php

declare(strict_types=1);

namespace davekok\calendar;

class Monthday
{
    public function __construct(public int $month, public int $day) {}
}
