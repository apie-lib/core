<?php
namespace Apie\Core\ValueObjects\Interfaces;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Value objects that can be mapped to a time-specific format.
 */
interface TimeRelatedValueObjectInterface extends StringValueObjectInterface
{
    public static function createFromDateTimeObject(DateTimeInterface $dateTime): self;
    public static function createFromCurrentTime(): self;
    public function toDate(): DateTimeImmutable;
    /**
     * The string should be in the format of this date string.
     *
     * @see https://www.php.net/manual/en/datetime.format.php#refsect1-datetime.format-parameters
     */
    public static function getDateFormat(): string;
}
