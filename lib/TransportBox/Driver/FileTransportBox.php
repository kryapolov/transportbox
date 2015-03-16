<?php
/**
 * This file is part of the mtools/transportbox package.
 */

namespace TransportBox\Driver;
use TransportBox\TransportBoxProvider;


/**
 * Class FileTransportBox
 *
 * @author kryapolov <kryapolov@yandex.ru>
 * @package TransportBox\Driver
 */
class FileTransportBox extends TransportBoxProvider
{

    /**
     * length size of record in byte
     */
    const LENGTH_SIZE = 4;

    /**
     * length of head record version in byte
     */
    const LENGTH_VERSION = 12;

    /** Descriptor of opened file
     * @var    resource link
     */
    private $descriptor;

    /** State of Box container
     * 0 - not started
     * 1 - in progress
     * @var int
     */
    private $state = 0;

    /** instance flag controller for persist data
     * @var    int
     */
    private $instances = 0;

    /** file access mode
     * @var string
     */
    private $fileMode;

    /**
     * error message
     * @var string
     */
    private $errorSubject;

    /**
     * Create a new FileTransportBox
     *
     * @param string $nameBox       filename
     * @param int    $accessMode    mode
     *
     * @internal param string $filename name of file(included and path)
     */
    public function __construct($nameBox = "default.db", $accessMode = 0)
    {
        $this->_boxInit($nameBox, $accessMode);
    }

    /**
     * {@inheritdoc}
     */
    public function getNameBox()
    {
        return basename($this->nameBox);
    }

    /**
     * {@inheritdoc}
     */
    public function insertRecords($data)
    {
        fseek($this->descriptor, 0, SEEK_END);
        $this->_saveRecordToFile($data);
        $this->increaseRecordCounter();
    }

    /**
     * {@inheritdoc}
     */
    public function getNextRecords()
    {
        $data = null;
        if ($this->state <= 0) {
            $this->_passHeader();
        }

        $lengthData = fread($this->descriptor, self::LENGTH_SIZE);
        $lengthRecord = unpack("l", $lengthData);
        $data = fread($this->descriptor, $lengthRecord[1]);
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        fseek($this->descriptor, self::LENGTH_SIZE);
        $version = fread($this->descriptor, self::LENGTH_VERSION);
        return trim($version);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaData($data)
    {
        fseek($this->descriptor, self::LENGTH_SIZE + self::LENGTH_VERSION);
        $this->_saveRecordToFile($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaData()
    {
        $data = 'null';
        fseek($this->descriptor, self::LENGTH_SIZE + self::LENGTH_VERSION);
        $lengthData = fread($this->descriptor, self::LENGTH_SIZE);
        $lengthRecord = unpack("l", $lengthData);
        if ($lengthRecord[1] != 0) {
            $data = fread($this->descriptor, $lengthRecord[1]);
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function _closeBox()
    {
        if ($this->descriptor != null) {
            $this->instances = 0;
            if ($this->fileMode != "r") {
                $this->_updateBox();
            }
            fclose($this->descriptor);
            $this->numberOfRecords = 0;
            $this->state = 0;
            $this->descriptor = null;
        }
    }

    //**********************************************P R I V A T E*******************************************************

    /**
     * release save to file
     *
     * @param string $data data to write
     *
     * @throws \RuntimeException
     */
    private function _saveRecordToFile($data)
    {
        $lengthData = strlen($data);

        if (!fwrite($this->descriptor, pack("l", $lengthData), self::LENGTH_SIZE) ||
            !fwrite($this->descriptor, (string)$data, $lengthData) ||
            !fflush($this->descriptor)
        ) {
            throw new \RuntimeException('Error insert record to boxFile, ' . var_export($this->nameBox, true) . ' .');
        }
    }

    /**
     * Init Header opened Box
     *
     * @throws \RuntimeException
     */
    private function _initHeader()
    {
        if (!fwrite($this->descriptor, pack("l", 0x00000000), self::LENGTH_SIZE) ||
            !fwrite($this->descriptor, "            ", self::LENGTH_VERSION) ||
            !fwrite($this->descriptor, pack("l", 0x00000000), self::LENGTH_SIZE)
        ) {
            throw new \RuntimeException('Error writing to boxFile, ' . var_export($this->nameBox, true) . ' .');
        }

    }

    /**
     * Init internal counter from opened Box
     *
     * @return int count of record in current Box
     */
    private function _initLengthBox()
    {
        fseek($this->descriptor, 0);
        $lengthData = fread($this->descriptor, self::LENGTH_SIZE);
        $lengthRecord = unpack("l", $lengthData);
        return $this->numberOfRecords = $lengthRecord[1];
    }

    /**
     * Init metadata info of opened Box
     */
    private function _initMetaInfo()
    {
        $this->numberOfRecords = $this->_initLengthBox();
        $this->version = $this->getVersion();
    }

    /**
     * Update Box version
     *
     * @throws \RuntimeException
     */
    private function _updateBox()
    {
        fseek($this->descriptor, 0);

        $counter = fwrite($this->descriptor, pack("l", $this->numberOfRecords), self::LENGTH_SIZE);
        $version = fwrite($this->descriptor, (string)$this->version, self::LENGTH_VERSION);

        if ($counter === false || $version === false || !fflush($this->descriptor)) {
            throw new \RuntimeException('Error writing from update boxFile, ' . var_export($this->nameBox, true) . ' .');
        }
    }

    /**
     * pass self::LENGTH_SIZE+self::LENGTH_VERSION+ length first record of metadata
     */
    private function _passHeader()
    {
        fseek($this->descriptor, self::LENGTH_SIZE + self::LENGTH_VERSION);
        $lengthMarker = fread($this->descriptor, self::LENGTH_SIZE);
        $lengthRecord = unpack("l", $lengthMarker);
        fseek($this->descriptor, 20 + $lengthRecord[1]);
        $this->state = 1;
    }

    /**
     * Formatter 'access mode' to 'file-access-modes'
     *
     * @param $accessMode
     *
     * @throws \InvalidArgumentException
     */
    private function _setFileMode($accessMode)
    {
        $accessMode = (int)$accessMode;
        if ($accessMode != 0 && $accessMode != 1) {
            throw new \InvalidArgumentException("Error not set Undefined access mode for boxFile, " . var_export($accessMode, true) . " .");
        }

        switch ($accessMode) {
            case 0:
                $this->fileMode = "w+";
                break;
            case 1:
                $this->fileMode = "r";
                break;
        }
    }

    /**
     * Init file contained box
     *
     * @param string $nameBox     filename
     * @param int    $accessMode  mode
     *
     * @throws \RuntimeException if permission problem has detected
     * @throws \UnexpectedValueException stream open has error
     */
    private function _boxInit($nameBox, $accessMode)
    {

        $this->nameBox = $nameBox;
        $this->_setFileMode($accessMode);

        if (file_exists($this->nameBox) && $this->fileMode === "w+" && !is_writable($this->nameBox)) {
            throw new \RuntimeException('Permission denied for boxFile, ' . var_export($this->nameBox, true) . ' .');
        }

        // requires direct access to file for persist data
        if ($this->instances == 0) {
            $this->_closeBox();
        }

        // set custom handler for detect in stream and check
        $this->errorSubject = null;
        set_error_handler(array($this, 'mainErrorHandler'));
        $this->descriptor = fopen($this->nameBox, $this->fileMode);
        restore_error_handler();

        if (!is_resource($this->descriptor)) {
            $this->descriptor = null;
            throw new \UnexpectedValueException(sprintf('The stream or file "%s" could not be opened: ' . $this->errorSubject, $this->nameBox));
        }

        if ($this->fileMode === "w+") {
            $this->_initHeader();
        } else {
            $this->_initMetaInfo();
        }

        $this->instances = 1;
    }

    /**
     * interpretation php handler interface
     *`
     * @param int $errorCode
     * @param string $text
     */
    private function mainErrorHandler($errorCode, $text)
    {
        $this->errorSubject = preg_replace('{^fopen\(.*?\): }', '', $text);
    }
}