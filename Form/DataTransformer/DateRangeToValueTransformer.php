<?php

namespace Resomedia\UsefulBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Resomedia\UsefulBundle\Model\DateRange;

/**
 * Class DateRangeToValueTransformer
 * @package Resomedia\UsefulBundle\Form\DataTransformer
 */
class DateRangeToValueTransformer implements DataTransformerInterface
{
    private $date_format;
    private $daterange_separator;

    /**
     * DateRangeToValueTransformer constructor.
     * @param null $date_format
     * @param string $daterange_separator
     */
    public function __construct($date_format=null, $daterange_separator=' - ')
    {
        $this->date_format = $date_format;
        $this->daterange_separator = $daterange_separator;
    }

    /**
     * @param mixed $dateRange
     * @return string
     */
    public function transform($dateRange)
    {

        if (null === $dateRange) {
            return '';
        }

        if (!is_object($dateRange)) {
            return '';
            //throw new UnexpectedTypeException($dateRange, 'DateRange');
        }

        $value = (string)$dateRange;

        return $value;
    }

    /**
     * @param mixed $value
     * @return null|DateRange
     */
    public function reverseTransform($value)
    {
        if ('' === $value || null === $value) {
            return null;
        }

        if (!is_string($value)) {
            return null;
        }

        $dateRange = new DateRange($this->date_format, $this->daterange_separator);
        $dateRange->parseData($value);

        return $dateRange;
    }
}
