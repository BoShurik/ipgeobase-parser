<?php
/**
 * User: boshurik
 * Date: 30.10.16
 * Time: 16:39
 */

namespace BoShurik\IPGeoBase\Persister;

class PersisterFactory
{
    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     * @param string $cityTable
     * @param string $rangeTable
     * @return Persister
     */
    public function createPersister($host, $user, $password, $database, $cityTable = 'city', $rangeTable = 'range')
    {
        return new Persister(
            $this->createPDO($host, $user, $password, $database),
            $cityTable,
            $rangeTable
        );
    }

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     * @return \PDO
     */
    private function createPDO($host, $user, $password, $database)
    {
        $dsn = sprintf('%s:dbname=%s;host=%s',
            'mysql',
            $database,
            $host
        );

        return new \PDO($dsn, $user, $password);
    }
}