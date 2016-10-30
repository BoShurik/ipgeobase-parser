<?php
/**
 * User: boshurik
 * Date: 28.10.16
 * Time: 15:48
 */

namespace BoShurik\IPGeoBase\Persister;

use BoShurik\IPGeoBase\Event\CityEvent;
use BoShurik\IPGeoBase\Event\Events;
use BoShurik\IPGeoBase\Event\RangeEvent;
use Symfony\Component\EventDispatcher\Event;

class Persister implements PersisterInterface
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var string
     */
    private $cityTable;

    /**
     * @var string
     */
    private $rangeTable;

    /**
     * @var \PDOStatement
     */
    private $cityStatement;

    /**
     * @var \PDOStatement
     */
    private $rangeStatement;

    public function __construct(\PDO $pdo, $cityTable = 'city', $rangeTable = 'range')
    {
        $this->pdo = $pdo;
        $this->cityTable = $cityTable;
        $this->rangeTable = $rangeTable;
        $this->cityStatement = null;
        $this->rangeStatement = null;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::BEFORE_PARSE => 'onBeforeParse',
            Events::CITY => 'onCity',
            Events::RANGE => 'onRange',
        );
    }

    public function onBeforeParse(Event $event)
    {
        $this->getCityStatement();
        $this->getRangeStatement();
        $this->truncateTables();
    }

    /**
     * @param CityEvent $event
     */
    public function onCity(CityEvent $event)
    {
        $city = $event->getCity();
        $statement = $this->getCityStatement();
        $statement->bindValue('id', $city->getId(), \PDO::PARAM_INT);
        $statement->bindValue('title', $city->getId(), \PDO::PARAM_STR);
        $statement->bindValue('area', $city->getId(), \PDO::PARAM_STR);
        $statement->bindValue('region', $city->getId(), \PDO::PARAM_STR);
        $statement->bindValue('latitude', $city->getId(), \PDO::PARAM_STR);
        $statement->bindValue('longitude', $city->getId(), \PDO::PARAM_STR);

        $this->executeStatement($statement);
    }

    /**
     * @param RangeEvent $event
     */
    public function onRange(RangeEvent $event)
    {
        $range = $event->getRange();
        $statement = $this->getRangeStatement();
        $statement->bindValue('city_id', $range->getCityId(), \PDO::PARAM_INT);
        $statement->bindValue('ip_from', $range->getIpFrom(), \PDO::PARAM_INT);
        $statement->bindValue('ip_to', $range->getIpTo(), \PDO::PARAM_INT);
        $statement->bindValue('country_code', $range->getCountryCode(), \PDO::PARAM_STR);

        $this->executeStatement($statement);
    }

    /**
     * Truncate tables
     */
    public function truncateTables()
    {
        if (!$this->pdo->query('SET FOREIGN_KEY_CHECKS = 0')) {
            throw new \RuntimeException($this->pdo->errorInfo()[2]);
        }
        if (!$this->pdo->query(sprintf('TRUNCATE TABLE `%s`', $this->rangeTable))) {
            throw new \RuntimeException($this->pdo->errorInfo()[2]);
        }
        if (!$this->pdo->query(sprintf('TRUNCATE TABLE `%s`', $this->cityTable))) {
            throw new \RuntimeException($this->pdo->errorInfo()[2]);
        }
        if (!$this->pdo->query('SET FOREIGN_KEY_CHECKS = 1')) {
            throw new \RuntimeException($this->pdo->errorInfo()[2]);
        }
    }

    /**
     * @param \PDOStatement $statement
     */
    private function executeStatement(\PDOStatement $statement)
    {
        if ($statement->execute()) {
            return;
        }

        throw new \RuntimeException($statement->errorInfo()[2]);
    }

    /**
     * @return \PDOStatement
     */
    private function getCityStatement()
    {
        if (!$this->cityStatement) {
            $this->createCityTable();

            $this->cityStatement = $this->pdo->prepare(
                sprintf('INSERT INTO `%s` VALUES (:id, :title, :area, :region, :latitude, :longitude)',
                    $this->cityTable
                )
            );
        }

        return $this->cityStatement;
    }

    /**
     * @return \PDOStatement
     */
    private function getRangeStatement()
    {
        if (!$this->rangeStatement) {
            $this->createRangeTable();

            $this->rangeStatement = $this->pdo->prepare(
                sprintf('INSERT INTO `%s` (city_id, ip_from, ip_to, country_code) VALUES (:city_id, :ip_from, :ip_to, :country_code)',
                    $this->rangeTable
                )
            );
        }

        return $this->rangeStatement;
    }

    /**
     * Creates city table
     */
    private function createCityTable()
    {
        if ($this->pdo->query(sprintf('SELECT 1 FROM `%s`', $this->cityTable))) {
            return;
        }

        $query  = sprintf('CREATE TABLE IF NOT EXISTS `%s` (', $this->cityTable);
        $query .= 'id INT NOT NULL, ';
        $query .= 'title VARCHAR(255) NOT NULL, ';
        $query .= 'area VARCHAR(255) NOT NULL, ';
        $query .= 'region VARCHAR(255) NOT NULL, ';
        $query .= 'latitude NUMERIC(10, 6) NOT NULL, ';
        $query .= 'longitude NUMERIC(10, 6) NOT NULL, ';
        $query .= 'PRIMARY KEY(id)';
        $query .= ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB';

        if (!$this->pdo->query($query)) {
            throw new \RuntimeException($this->pdo->errorInfo()[2]);
        }
    }

    /**
     * Creates range table
     */
    private function createRangeTable()
    {
        if ($this->pdo->query(sprintf('SELECT 1 FROM `%s`', $this->rangeTable))) {
            return;
        }

        $query  = sprintf('CREATE TABLE IF NOT EXISTS `%s` (', $this->rangeTable);
        $query .= 'id INT AUTO_INCREMENT NOT NULL, ';
        $query .= 'city_id INT DEFAULT NULL, ';
        $query .= 'ip_from BIGINT NOT NULL, ';
        $query .= 'ip_to BIGINT NOT NULL, ';
        $query .= 'country_code VARCHAR(255) NOT NULL, ';
        $query .= 'INDEX IDX_CITY_ID (city_id), ';
        $query .= 'PRIMARY KEY(id)';
        $query .= ') DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB';

        if (!$this->pdo->query($query)) {
            throw new \RuntimeException($this->pdo->errorInfo()[2]);
        }

        if (!$this->pdo->query(
            sprintf('ALTER TABLE `%s` ADD CONSTRAINT FK_RANGE_CITY FOREIGN KEY (city_id) REFERENCES `%s` (id)',
                $this->rangeTable,
                $this->cityTable
            )
        )) {
            throw new \RuntimeException($this->pdo->errorInfo()[2]);
        }
    }
}