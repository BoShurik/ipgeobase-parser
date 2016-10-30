<?php
/**
 * User: boshurik
 * Date: 28.10.16
 * Time: 16:53
 */

namespace BoShurik\IPGeoBase\Event;

use BoShurik\IPGeoBase\Model\City;
use Symfony\Component\EventDispatcher\Event;

class CityEvent extends Event
{
    /**
     * @var City
     */
    private $city;

    public function __construct(City $city)
    {
        $this->city = $city;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }
}