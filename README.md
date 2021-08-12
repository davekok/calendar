Calendar
--------------------------------------------------------------------------------

An implementation of a proleptic gregorian calendar starting at Mon, 01 Jan 0001 00:00:00 and ending at Fri, 31 Dec 9999 23:59:59.

Years outside this range are not supported.

In accordance with ISO 8601, Monday is considered the first day of the week and
ISO 8601 week numbering is used.

Timestamps are valid in the range of 000000000001 to 315537897600 inclusive.

- 000000000001 = Mon, 01 Jan 0001 00:00:00
- 315537897600 = Fri, 31 Dec 9999 23:59:59

Any timestamp outside this range is considered invalid, 000000000000 is
left intentionally invalid to easily test for errors.

Converting a timestamp to a unix timestamp is as easy as substracting the
difference between year 1 and the year 1970 (in seconds).

Leap seconds are ignored, leap seconds are not regulary and are forecasted
only a few weeks in advance. Supporting leap seconds is thus not possible.
However, since most computer system clocks experience some drift anyways. It
is not considered a problem as synchronising the clock regularly will always
be necessary.
