<?php
/**
 * User: Konstantin Ryapolov
 * Date: 03.02.14
 */

namespace TransportBox;

abstract class TransportBoxProvider implements TransportBox
{

    /** version of box
     * @var    string
     */
    protected $version = '';

    /** record count
     * @var    int
     */
    protected $numberOfRecords = 0;

    /** name box
     * @var    string
     */
    protected $nameBox;

    /**
     * call close opened resource from box
     */
    public final function __destruct()
    {
        $this->_closeBox();
    }

    /**
     * Update record counter
     */
    protected final function increaseRecordCounter()
    {
        $this->numberOfRecords = $this->numberOfRecords + 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getLengthBox()
    {
        return $this->numberOfRecords;
    }

    /**
     * {@inheritdoc}
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function insertRecords($data);

    /**
     * {@inheritdoc}
     */
    abstract public function getNextRecords();

    /**
     * {@inheritdoc}
     */
    abstract public function getVersion();

    /**
     * {@inheritdoc}
     */
    abstract public function getMetaData();

    /**
     * {@inheritdoc}
     */
    abstract public function setMetaData($data);

    /**
     * {@inheritdoc}
     */
    abstract public function getNameBox();

    /**
     * Correct close opened box
     * @return mixed
     */
    abstract protected function _closeBox();

}