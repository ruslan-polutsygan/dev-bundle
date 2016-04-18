<?php

namespace RP\DevBundle\Doctrine\DBAL;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Connection as DriverConnection;

/**
 * @see https://gist.github.com/makasim/f1e28c9bc9458f20f38f
 *
 * Connection wrapper sharing the same db handle across multiple requests
 *
 * Allows multiple Connection instances to run in the same transaction
 */
class PersistedConnection extends Connection
{
    /**
     * @var DriverConnection[]
     */
    protected static $persistedConnections;

    /**
     * @var int[]
     */
    protected static $persistedTransactionNestingLevels;

    /**
     * {@inheritDoc}
     */
    public function connect()
    {
        if ($this->isConnected()) {
            return false;
        }

        if ($this->hasPersistedConnection()) {
            $this->_conn = $this->getPersistedConnection();
            $this->setConnected(true);
        } else {
            parent::connect();
            $this->setPersistedConnection($this->_conn);

            $this->_conn->exec('SET SESSION wait_timeout=2147483');
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function close($force = false)
    {
        if ($force) {
            parent::close();

            $this->unsetPersistedConnection();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function beginTransaction()
    {
        $this->wrapTransactionNestingLevel('beginTransaction');
    }

    /**
     * {@inheritDoc}
     */
    public function commit()
    {
        $this->wrapTransactionNestingLevel('commit');
    }

    /**
     * {@inheritDoc}
     */
    public function rollBack()
    {
        $this->wrapTransactionNestingLevel('rollBack');
    }

    /**
     * {@inheritDoc}
     */
    public function isTransactionActive()
    {
        $this->setTransactionNestingLevel($this->getPersistedTransactionNestingLevel());

        return parent::isTransactionActive();
    }

    /**
     * @param int $level
     */
    private function setTransactionNestingLevel($level)
    {
        $rp = new \ReflectionProperty('Doctrine\DBAL\Connection', '_transactionNestingLevel');
        $rp->setAccessible(true);
        $rp->setValue($this, $level);
        $rp->setAccessible(false);
    }

    /**
     * @param string $method
     *
     * @throws \Exception
     */
    private function wrapTransactionNestingLevel($method)
    {
        $exception = null;

        $this->setTransactionNestingLevel($this->getPersistedTransactionNestingLevel());

        try {
            call_user_func(array('parent', $method));

            $this->setPersistedTransactionNestingLevel($this->getTransactionNestingLevel());
        } catch (\Exception $e) {
            $this->setPersistedTransactionNestingLevel($this->getTransactionNestingLevel());

            throw $e;
        }
    }

    /**
     * @param bool $connected
     */
    protected function setConnected($connected)
    {
        $rp = new \ReflectionProperty('Doctrine\DBAL\Connection', '_isConnected');
        $rp->setAccessible(true);
        $rp->setValue($this, $connected);
        $rp->setAccessible(false);
    }

    /**
     * @return int
     */
    protected function getPersistedTransactionNestingLevel()
    {
        if (isset(static::$persistedTransactionNestingLevels[$this->getConnectionId()])) {
            return static::$persistedTransactionNestingLevels[$this->getConnectionId()];
        }

        return 0;
    }

    /**
     * @param int $level
     */
    protected function setPersistedTransactionNestingLevel($level)
    {
        static::$persistedTransactionNestingLevels[$this->getConnectionId()] = $level;
    }

    /**
     * @param DriverConnection $connection
     */
    protected function setPersistedConnection(DriverConnection $connection)
    {
        static::$persistedConnections[$this->getConnectionId()] = $connection;
    }

    /**
     * @return bool
     */
    protected function hasPersistedConnection()
    {
        return isset(static::$persistedConnections[$this->getConnectionId()]);
    }

    /**
     * @return DriverConnection
     */
    protected function getPersistedConnection()
    {
        return static::$persistedConnections[$this->getConnectionId()];
    }

    /**
     * @return DriverConnection
     */
    protected function unsetPersistedConnection()
    {
        unset(static::$persistedConnections[$this->getConnectionId()]);
        unset(static::$persistedTransactionNestingLevels[$this->getConnectionId()]);
    }

    /**
     * @return string
     */
    protected function getConnectionId()
    {
        return md5(serialize($this->getParams()));
    }
}
