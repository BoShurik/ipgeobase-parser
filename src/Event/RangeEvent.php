<?php
/**
 * User: boshurik
 * Date: 28.10.16
 * Time: 16:53
 */

namespace BoShurik\IPGeoBase\Event;

use BoShurik\IPGeoBase\Model\Range;
use Symfony\Component\EventDispatcher\Event;

class RangeEvent extends Event
{
    /**
     * @var Range
     */
    private $range;

    public function __construct(Range $range)
    {
        $this->range = $range;
    }

    /**
     * @return Range
     */
    public function getRange()
    {
        return $this->range;
    }
}