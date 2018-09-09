<?php

declare(strict_types=1);

/**
 * This file is part of the doctrine-renew-connection package.
 *
 * (c) Jordi Domènech Bonilla
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jdomenechb\Doctrine\DBAL;


class Connection extends \Doctrine\DBAL\Connection
{
    /** @var bool */
    protected $skipConnection = false;

    /**
     * @inheritdoc
     */
    public function connect()
    {
        // Avoid call to connect() method when doing the internal ping()
        if ($this->skipConnection) {
            return false;
        }

        if ($this->_conn) {
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
}