<?php

/**
 * This file is part of the doctrine-renew-connection package.
 *
 * (c) Jordi Domènech Bonilla
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jdomenechb\Doctrine\DBAL;


class TimedRenewConnection extends \Doctrine\DBAL\Connection
{
    /** @var bool */
    protected $skipConnection = false;

    /** @var int */
    protected $lastUsed;

    /** @var int */
    protected $secondsToRenew = 0;

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

        $this->lastUsed = time();

        return parent::connect();
    }
}