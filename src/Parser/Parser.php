<?php
/**
 * User: boshurik
 * Date: 28.10.16
 * Time: 15:47
 */

namespace BoShurik\IPGeoBase\Parser;

use BoShurik\IPGeoBase\Event\CityEvent;
use BoShurik\IPGeoBase\Event\Events;
use BoShurik\IPGeoBase\Event\RangeEvent;
use BoShurik\IPGeoBase\Model\City;
use BoShurik\IPGeoBase\Model\Range;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Parser
{
    const KEY_CITIES = 'cities';
    const KEY_RANGE = 'cidr_optim';

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $path
     */
    public function parse($path)
    {
        $this->eventDispatcher->dispatch(Events::BEFORE_PARSE);

        $files = $this->extract($path);
        $this->parseCities($files[self::KEY_CITIES]);
        $this->parseRanges($files[self::KEY_RANGE]);

        $this->eventDispatcher->dispatch(Events::AFTER_PARSE);
    }

    /**
     * @param string $path
     */
    private function parseCities($path)
    {
        $this->eventDispatcher->dispatch(Events::BEFORE_CITIES);

        $file = fopen($path, 'r');
        while ($string = iconv('windows-1251', 'utf-8', fgets($file))) {
            $row = str_getcsv($string, "\t");
            $city = $this->parseCity($row);

            $this->eventDispatcher->dispatch(Events::CITY, new CityEvent($city));
        }

        fclose($file);

        $this->eventDispatcher->dispatch(Events::AFTER_CITIES);
    }

    /**
     * @param string $path
     */
    private function parseRanges($path)
    {
        $this->eventDispatcher->dispatch(Events::BEFORE_RANGE);

        $file = fopen($path, 'r');
        while ($string = iconv('windows-1251', 'utf-8', fgets($file))) {
            $row = str_getcsv($string, "\t");
            $range = $this->parseRange($row);

            $this->eventDispatcher->dispatch(Events::RANGE, new RangeEvent($range));
        }

        fclose($file);

        $this->eventDispatcher->dispatch(Events::AFTER_RANGE);
    }

    /**
     * @param $row
     *
     * @return City
     */
    private function parseCity($row)
    {
        return new City((integer)$row[0], $row[1], $row[2], $row[3], $row[4], $row[5]);
    }

    /**
     * @param $row
     *
     * @return Range
     */
    private function parseRange($row)
    {
        $ips = array_map('ip2long', array_map('trim', explode('-', $row[2])));
        $cityId = '-' !== $row[4] ? (integer)$row[4] : null;

        return new Range($cityId, (integer)$ips[0], (integer)$ips[1], $row[3]);
    }

    /**
     * @param string $path
     * @return array
     */
    private function extract($path)
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \RuntimeException(sprintf('Can\'t open file "%s"', $path));
        }

        $tmpDirName = sprintf('%s/%s', sys_get_temp_dir(), uniqid());
        if (!$zip->extractTo($tmpDirName)) {
            throw new \RuntimeException(sprintf('Can\'t extract file "%s" to "%s"', $path, $tmpDirName));
        }
        $zip->close();

        $dir = dir($tmpDirName);
        $files = array();
        while (false !== ($entry = $dir->read())) {
            if (in_array($entry, array('.', '..'))) {
                continue;
            }
            $files[] = sprintf('%s/%s', $tmpDirName, $entry);
        }
        $dir->close();

        if (!$files) {
            throw new \RuntimeException(sprintf('Archive "%s" is empty', $path));
        }

        if (count($files) != 2) {
            throw new \RuntimeException(sprintf('Wrong archive. Files count miss match'));
        }

        $keys = array_map(function($value){
            return pathinfo($value, PATHINFO_FILENAME);
        }, $files);

        $files = array_combine($keys, $files);

        if (!isset($files[self::KEY_RANGE])) {
            throw new \RuntimeException(sprintf('Wrong archive. Missing range file'));
        }
        if (!isset($files[self::KEY_CITIES])) {
            throw new \RuntimeException(sprintf('Wrong archive. Missing cities file'));
        }

        return $files;
    }
}