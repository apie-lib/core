<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\Fields\DatePeriodOptions;
use Apie\Core\Metadata\Fields\GetterMethod;
use Apie\Core\Metadata\Fields\PublicProperty;
use Apie\Core\Metadata\MetadataInterface;
use Apie\Core\Metadata\StrategyInterface;
use DatePeriod;
use DateTimeInterface;
use ReflectionClass;

class DatePeriodStrategy implements StrategyInterface
{
    public static function supports(ReflectionClass $class): bool
    {
        return $class->name === DatePeriod::class;
    }

    /**
     * @param ReflectionClass<covariant DatePeriod<DateTimeInterface, DateTimeInterface|null, int|null>> $class
     */
    public function __construct(private ReflectionClass $class)
    {
    }

    public function getCreationMetadata(ApieContext $context): MetadataInterface
    {
        return new CompositeMetadata(
            new MetadataFieldHashmap([
                'startDate' => new PublicProperty($this->class->getProperty('start')),
                'dateInterval' => new PublicProperty($this->class->getProperty('interval')),
                'recurrences' => new PublicProperty($this->class->getProperty('recurrences'), optional :true),
                'endDate' => new PublicProperty($this->class->getProperty('end'), optional :true),
                'options' => new DatePeriodOptions(),
            ]),
            $this->class
        );
    }

    public function getModificationMetadata(ApieContext $context): MetadataInterface
    {
        return new CompositeMetadata(
            new MetadataFieldHashmap([
            ]),
            $this->class
        );
    }

    public function getResultMetadata(ApieContext $context): MetadataInterface
    {
        return new CompositeMetadata(
            new MetadataFieldHashmap([
                'dateInterval' => new GetterMethod($this->class->getMethod('getDateInterval')),
                'endDate' => new GetterMethod($this->class->getMethod('getEndDate')),
                'recurrences' => new GetterMethod($this->class->getMethod('getRecurrences')),
                'startDate' => new GetterMethod($this->class->getMethod('getStartDate')),
                'includeStartDate' => new PublicProperty($this->class->getProperty('include_start_date')),
                'includeEndDate' => new PublicProperty($this->class->getProperty('include_end_date')),
            ]),
            $this->class
        );
    }
}
