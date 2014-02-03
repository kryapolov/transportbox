#!/usr/bin/env php
<?php
/**
 * User: Konstantin Ryapolov
 * Date: 03.02.14
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

require_once('../TransportBox.php');
require_once('../TransportBoxProvider.php');
require_once('../Driver/FileTransportBox.php');

$fileTest = 'test.db';
$transport = new \TransportBox\Driver\FileTransportBox('test.db', 0);

$transport->setVersion('1');
$transport->setMetaData('tested data');
$transport->insertRecords("one record");
$transport->insertRecords("two record");
$transport->insertRecords("three record");

unset($transport);

$checkBox = new \TransportBox\Driver\FileTransportBox($fileTest, 1);
echo 'Box name: '. $checkBox->getNameBox()."\n\r";
echo 'Box version: '. $checkBox->getVersion()."\n\r";
echo 'Box metadata: '. $checkBox->getLengthBox()."\n\r";
echo 'Box metadata: '. $checkBox->getMetaData()."\n\r";
for($i=0;$i<$checkBox->getLengthBox(); $i++){
	echo 'Box record № '.$i.' contained: '. $checkBox->getNextRecords()."\n\r";
}