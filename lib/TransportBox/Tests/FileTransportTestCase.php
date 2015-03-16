<?php

namespace TransportBox\Test;
use TransportBox\Driver\FileTransportBox;

/**
 * Class VersionTestCase
 *
 * @package TransportBox\Test
 */
class FileTransportTestCase extends \PHPUnit_Framework_TestCase {

    const TEST_VERSION        = 4;
    const TEST_FILENAME       = 'test.db';


    protected function setUp()
    {
        if(file_exists($this::TEST_FILENAME)){
            unlink($this::TEST_FILENAME);
        }
    }


    public function testVersionHasBeSet()
    {

        $transport = new \TransportBox\Driver\FileTransportBox($this::TEST_FILENAME, 0);

        $transport->setVersion($this::TEST_VERSION);
        // Act

        // Assert
        $this->assertEquals($this::TEST_VERSION, $transport->getVersion());

    }

    public function testGetNameBox() {

        $transport = new \TransportBox\Driver\FileTransportBox($this::TEST_FILENAME, 0);

        $nameBox = $transport->getNameBox();
        // Act

        // Assert
        $this->assertEquals($this::TEST_FILENAME, $nameBox);

    }
}
