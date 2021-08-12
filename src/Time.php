<?php

declare(strict_types=1);

namespace davekok\calendar;

use Stringable;

class Time implements Stringable
{
    public function __construct(
        public ?int $hour = null,
        public ?int $minute = null,
        public ?int $second = null
    ) {}

    public function __toString(): string
    {
        if ($this->hour !== null && $this->minute !== null && $this->second !== null)
            return sprintf(
                "T%02i:%02i:%02i",
                $this->hour,
                $this->minute,
                $this->second
            );
        }

        if ($this->hour !== null && $this->minute !== null && $this->second === null)
            return sprintf(
                "T%02i:%02i",
                $this->hour,
                $this->minute
            );
        }

        if ($this->hour !== null && $this->minute === null && $this->second === null)
            return sprintf("T%02i:00", $this->hour);
        }

        return "";
    }
}
