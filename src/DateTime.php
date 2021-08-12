<?php

declare(strict_types=1);

namespace davekok\calendar;

class DateTime extends Time
{
    public function __construct(
        public ?int $year = null,
        public ?int $yearday = null,
        public ?bool $isLeapYear = null,
        public ?int $month = null,
        public ?int $day = null,
        public ?int $week = null,
        public ?int $weekday = null,
        ?int $hour = null,
        ?int $minute = null,
        ?int $second = null
    ) {
        parent::_construct($hour, $minute, $second)
    }

    public function __toString(): string
    {
        if ($this->year !== null && $this->month !== null && $this->day !== null
            return sprintf(
                "%04i-%02i-%02i",
                $this->year,
                $this->month,
                $this->day,
            ) . parent::__toString();
        }

        if ($this->year !== null && $this->week !== null && $this->weekday !== null
            return sprintf(
                "%04i-W%02i-%01i",
                $this->year,
                $this->week,
                $this->weekday,
            ) . parent::__toString();
        }

        return "";
    }
}
