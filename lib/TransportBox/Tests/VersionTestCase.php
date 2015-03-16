<?php

namespace TransportBox\Test;
use TransportBox\Driver\FileTransportBox;

/**
 * Class VersionTestCase
 *
 * @package TransportBox\Test
 */
class VersionTestCase extends \PHPUnit_Framework_TestCase {

    const TEST_VERSION        = 4;

    public function testVersionHasBeSet()
    {

        $fileTest = 'test.db';

        $transport = new \TransportBox\Driver\FileTransportBox($fileTest, 0);

        $transport->setVersion($this::TEST_VERSION);
        // Act

        // Assert
        $this->assertEquals($this::TEST_VERSION, $transport->getVersion());

    }
}
