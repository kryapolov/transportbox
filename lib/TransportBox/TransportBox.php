<?php
/**
 * User: Konstantin Ryapolov
 * Date: 03.02.14
 */
namespace TransportBox;

interface TransportBox
{

    /**
     * Insert Record to current box
     *
     * @param string $data user-data
     */
    function insertRecords($data);

    /**
     * Get Next Record
     * @return string record data
     */
    function getNextRecords();

    /**
     * Get length current box in record
     * @return    int
     */
    function getLengthBox();

    /**
     * Get version of current box
     * @return string version of box
     */
    function getVersion();

    /**
     * Set version from current box
     *
     * @param string $version
     */
    function setVersion($version);

    /**
     * Get metadata from current box
     * @return string metadata
     */
    function getMetaData();

    /**
     * Set metadata from current box
     *
     * @param string $data data
     *
     * @return mixed inserted data or null
     */
    function setMetaData($data);

    /**
     * Calculate and return name from current box
     * @return string name of Box
     */
    function getNameBox();
}