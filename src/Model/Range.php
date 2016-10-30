<?php
/**
 * User: boshurik
 * Date: 28.10.16
 * Time: 15:51
 */

namespace BoShurik\IPGeoBase\Model;

final class Range
{
    /**
     * @var integer
     */
    private $cityId;

    /**
     * @var integer
     */
    private $ipFrom;

    /**
     * @var integer
     */
    private $ipTo;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @param int $cityId
     * @param int $ipFrom
     * @param int $ipTo
     * @param string $countryCode
     */
    public function __construct($cityId, $ipFrom, $ipTo, $countryCode)
    {
        $this->cityId = $cityId;
        $this->ipFrom = $ipFrom;
        $this->ipTo = $ipTo;
        $this->countryCode = $countryCode;
    }

    /**
     * @return int
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * @return int
     */
    public function getIpFrom()
    {
        return $this->ipFrom;
    }

    /**
     * @return int
     */
    public function getIpTo()
    {
        return $this->ipTo;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }
}