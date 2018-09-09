<?php

/**
 * This file is part of the renew-doctrine-connection package.
 *
 * (c) Jordi Domènech Bonilla
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jdomenechb\Doctrine\DBAL;


use Doctrine\Common\EventManager;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Driver;

class TimedRenewConnection extends \Doctrine\DBAL\Connection
{
    /** @var bool */
    protected $skipConnection = false;

    /** @var int */
    protected $lastUsed;

    /** @var int */
    protected $secondsToRenew = 0;

    /**
     * TimedRenewConnection constructor.
     * @param array $params
     * @param Driver $driver
     * @param Configuration|null $config
     * @param EventManager|null $eventManager
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __construct(
        array $params,
        Driver $driver,
        Configuration $config = null,
        EventManager $eventManager = null
    ) {
        if (isset($params['driverOptions']['secondsToRenew'])) {
            $this->secondsToRenew = (int) $params['driverOptions']['secondsToRenew'];
            unset($params['driverOptions']['secondsToRenew']);
        }

        parent::__construct($params, $driver, $config, $eventManager);
    }

    /**
     * @inheritdoc
     */
    public function connect()
    {
        // Avoid call to connect() method when doing the internal ping()
        if ($this->skipConnection) {
            return false;
        }

        // Check if it needs to be renewed
        if ($this->_conn && $this->lastUsed + $this->secondsToRenew < time()) {
            // Ping the database
            $this->skipConnection = true;
            $ping = $this->ping();
            $this->skipConnection = false;

            // Renew connection if database is down
            if (!$ping) {
                $this->close();
                $this->connect();
            }
        }

        return parent::connect();
    }

    /**
     * @inheritdoc
     */
    public function query(...$args)
    {
        $toReturn = parent::query(...$args);

        $this->lastUsed = time();

        return $toReturn;
    }

    /**
     * @inheritdoc
     */
    public function executeQuery($query, array $params = [], $types = [], QueryCacheProfile $qcp = null)
    {
        $toReturn = parent::executeQuery($query, $params, $types, $qcp);

        $this->lastUsed = time();

        return $toReturn;
    }

    /**
     * @inheritdoc
     */
    public function executeUpdate($query, array $params = [], array $types = [])
    {
        $toReturn = parent::executeUpdate($query, $params, $types);

        $this->lastUsed = time();

        return $toReturn;
    }
}
