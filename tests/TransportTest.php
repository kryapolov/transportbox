<?php

namespace TransportBox\tests;
use TransportBox\Driver\FileTransportBox;

/**
 * Class TransportTest
 *
 * @package TransportBox\tests
 */
class TransportTest extends \PHPUnit_Framework_TestCase {

    const TEST_VERSION        = 4;
    const TEST_FILENAME       = 'test.db';
    const TEST_CHUNK          = '/\/ABCDEFGHIKLMNOPRSTEF/\/';

    protected function setUp()
    {
        if(file_exists($this::TEST_FILENAME)){
            unlink($this::TEST_FILENAME);
        }
    }


    public function testVersionHasBeSet()
    {

        $box = new FileTransportBox($this::TEST_FILENAME, 0);

        $box->setVersion($this::TEST_VERSION);
        // Act

        // Assert
        $this->assertEquals($this::TEST_VERSION, $box->getVersion());

    }

    public function testGetNameBox() {

        $box = new FileTransportBox($this::TEST_FILENAME, 0);

        $nameBox = $box->getNameBox();
        // Act

        // Assert
        $this->assertEquals($this::TEST_FILENAME, $nameBox);

    }

    public function testInsertRecords() {

        $box = new FileTransportBox($this::TEST_FILENAME, 0);

        $box->insertRecords($this::TEST_CHUNK);
        $lenChunk = strlen($this::TEST_CHUNK);
        //length box + length meta+length first record
        $totalLength = $lenChunk + $box::LENGTH_SIZE*3+$box::LENGTH_VERSION;

        unset($box);

        $this->assertEquals(@filesize($this::TEST_FILENAME), $totalLength);

    }

    public function testGetNextRecords()
    {

        $box = new FileTransportBox($this::TEST_FILENAME, 0);

        $box->insertRecords('123456789');
        $box->insertRecords($this::TEST_CHUNK);
        unset($box);

        $box = new FileTransportBox($this::TEST_FILENAME, 1);

        $box->getNextRecords();
        $record = $box->getNextRecords();

        $this->assertEquals($record, $this::TEST_CHUNK);

    }

    public function testSetMetaData()
    {

        $box = new FileTransportBox($this::TEST_FILENAME, 0);

        $box->setMetaData($this::TEST_CHUNK);
        $box->insertRecords('123456789');
        unset($box);

        $box = new FileTransportBox($this::TEST_FILENAME, 1);


        $record = $box->getMetaData();

        $this->assertEquals($record, $this::TEST_CHUNK);

    }

    public function testGetLengthBox()
    {

        $box = new FileTransportBox($this::TEST_FILENAME, 0);

        $box->setMetaData($this::TEST_CHUNK);
        $box->insertRecords($this::TEST_CHUNK);
        $box->insertRecords($this::TEST_CHUNK);
        $box->insertRecords($this::TEST_CHUNK);
        unset($box);

        $box = new FileTransportBox($this::TEST_FILENAME, 1);

        $length = $box->getLengthBox();

        $this->assertEquals($length, 3);

    }






}
