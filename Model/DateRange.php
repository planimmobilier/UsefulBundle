<?php

namespace Resomedia\UsefulBundle\Model;

/**
 * Class DateRange
 * @package Resomedia\UsefulBundle\Model
 */
class DateRange
{
    public $date_format;

    private $daterange_separator;

    /** @var
     * \DateTime
     */
    public $dateStart;

    /**
     * @var \DateTime
     */
    public $dateEnd;

    /**
     * DateRange constructor.
     * @param string $date_format
     * @param string $daterange_separator
     */
    public function __construct ($date_format='d/m/Y', $daterange_separator = ' - ')
    {
        $this->date_format = $date_format;
        $this->daterange_separator = $daterange_separator;

        $this->setData (new \DateTime(), new \DateTime());
    }

    /**
     * @param \DateTime $dateStart
     * @param \DateTime $dateEnd
     */
    public function setData(\DateTime $dateStart, \DateTime $dateEnd)
    {
        $this->dateStart = $dateStart->setTime(0,0,0);
        $this->dateEnd   = $dateEnd->setTime(23, 59, 59);
    }

    /**
     * @param $string
     * @throws \Exception
     */
    public function parseData($string)
    {
        list($start, $end) = explode($this->daterange_separator, $string);

        try {
            $dateStart = \DateTime::createFromFormat($this->date_format, $start);
            $dateEnd = \DateTime::createFromFormat($this->date_format, $end);
        } catch (\Exception $e) {
            throw new \Exception ('Unknown daterange format: "' . $string . '". Must be: "' . $this->date_format . '"');
        }
        if (!$dateStart || !$dateEnd)
            throw new \Exception ('Unknown daterange format: "' . $string . '". Must be: "' . $this->date_format . '"');

        $this->setData ($dateStart, $dateEnd);
    }

    /**
     * @return string
     */
    public function __toString ()
    {
        return $this->dateStart->format($this->date_format) . $this->daterange_separator . $this->dateEnd->format($this->date_format);
    }

    /**
     * @param $dateEnd
     * @param $interval
     */
    public function createToDate($dateEnd, $interval)
    {
        $dateEnd = clone($dateEnd);
        $dateStart = clone($dateEnd);
        $dateStart->sub(new \DateInterval($interval));

        $this->setData($dateStart, $dateEnd);
    }

    /**
     * @return bool|\DateInterval
     */
    public function getInterval()
    {
        return $this->dateEnd->diff($this->dateStart);
    }

}
