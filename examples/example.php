#!/usr/bin/env php
<?php
/**
 * This file is part of the mtools/transportbox package.
 *
 * Test is full check public api TransportBox based on file;
 * insertRecords
 * getNextRecords
 * getLengthBox
 * getVersion
 * setVersion
 * getMetaData
 * setMetaData
 * getNameBox
 */

require_once('lib/TransportBox/autoload.php');

$fileTest = 'test.db';
$transport = new \TransportBox\Driver\FileTransportBox($fileTest, 0);

$transport->setVersion('1');
$transport->setMetaData('tested data');
$transport->insertRecords("one record");
$transport->insertRecords("two record");
$transport->insertRecords("three record");

unset($transport);

$checkBox = new \TransportBox\Driver\FileTransportBox($fileTest, 1);
echo 'Box name: ' . $checkBox->getNameBox() . "\n\r";
echo 'Box version: ' . $checkBox->getVersion() . "\n\r";
echo 'Box metadata: ' . $checkBox->getLengthBox() . "\n\r";
echo 'Box metadata: ' . $checkBox->getMetaData() . "\n\r";

for ($i = 0; $i < $checkBox->getLengthBox(); $i++) {
    echo 'Box record № ' . $i . ' contained: ' . $checkBox->getNextRecords() . "\n\r";
}