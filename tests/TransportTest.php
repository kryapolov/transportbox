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

        $box = new \TransportBox\Driver\FileTransportBox($this::TEST_FILENAME, 0);

        $box->setVersion($this::TEST_VERSION);
        // Act

        // Assert
        $this->assertEquals($this::TEST_VERSION, $box->getVersion());

    }

    public function testGetNameBox() {

        $box = new \TransportBox\Driver\FileTransportBox($this::TEST_FILENAME, 0);

        $nameBox = $box->getNameBox();
        // Act

        // Assert
        $this->assertEquals($this::TEST_FILENAME, $nameBox);

    }

    public function testInsertRecords() {

        $box = new \TransportBox\Driver\FileTransportBox($this::TEST_FILENAME, 0);

        $box->insertRecords($this::TEST_CHUNK);
        $lenChunk = strlen($this::TEST_CHUNK);
        //length box + length meta+length first record
        $totalLength = $lenChunk + $box::LENGTH_SIZE*3+$box::LENGTH_VERSION;

        unset($box);

        $this->assertEquals(@filesize($this::TEST_FILENAME), $totalLength);

    }

}
