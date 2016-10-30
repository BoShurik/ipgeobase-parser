<?php
/**
 * User: boshurik
 * Date: 28.10.16
 * Time: 15:51
 */

namespace BoShurik\IPGeoBase\Model;

final class City
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $area;

    /**
     * @var string
     */
    private $region;

    /**
     * @var float
     */
    private $latitude;

    /**
     * @var float
     */
    private $longitude;

    /**
     * @param integer $id
     * @param string $title
     * @param string $area
     * @param string $region
     * @param float $latitude
     * @param float $longitude
     */
    public function __construct($id, $title, $area, $region, $latitude, $longitude)
    {
        $this->id = $id;
        $this->title = $title;
        $this->area = $area;
        $this->region = $region;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
}