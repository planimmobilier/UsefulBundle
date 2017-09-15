<?php

namespace Resomedia\UsefulBundle\Service;

use Resomedia\UsefulBundle\Model\DateRange;

/**
 * Class DateRangeManager
 * @package Resomedia\UsefulBundle\Service
 */
class DateRangeManager
{
    private $date_format;
    private $default_interval;

    /**
     * DateRangeManager constructor.
     * @param $parameters
     */
    public function __construct($parameters)
    {
        $this->date_format      = $parameters['date_format'];
        $this->default_interval = $parameters['default_interval'];
    }

    /**
     * @param null $date_format
     * @return DateRange
     */
    public function create($date_format = null)
    {
        $date_format = $date_format ? $date_format : $this->date_format;

        $dateRange = new DateRange($date_format);

        return $dateRange;
    }

    /**
     * @param string $dateEnd
     * @param null $date_format
     * @param null $date_interval
     * @return DateRange
     */
    public function createToDate($dateEnd="now", $date_format = null, $date_interval=null)
    {
        $date_format = $date_format ? $date_format : $this->date_format;
        $date_interval = $date_interval ? $date_interval : $this->default_interval;

        $dateRange = new DateRange($date_format);

        if ($dateEnd == "now")
            $dateEnd = new \DateTime;
        $dateRange->createToDate($dateEnd, $date_interval);

        return $dateRange;
    }
}
