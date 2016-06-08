<?php

namespace ApplicationTest\Model;

use Application\Service\UtilitiesService;
use PHPUnit_Framework_TestCase;

class UtilitiesServiceTest extends PHPUnit_Framework_TestCase
{
    private $us;

    public function __construct()
    {
        $this->us = new UtilitiesService();
    }

    public function testFindClosestColorWithExactMatch()
    {
        $red = 'ff0000';
        $green = '00ff00';
        $blue = '0000ff';
        $colors = array($red, $green, $blue);
        $searchColor = $blue;

        $result = $this->us->findClosestColor($searchColor, $colors);

        $this->assertEquals(2, $result['index'], 'Closest should be blue');
        $this->assertEquals(0.0, $result['dist'], 'Distance should be zero');
    }


    // Based on a test in https://github.com/matthewbaggett/php-color.
    public function testFindClosestColorWithoutExactMatch()
    {
        $red = 'ff0000';
        $green = '00ff00';
        $blue = '0000ff';
        $colors = array($red, $green, $blue);
        $searchColor = 'ccfedd';

        $result = $this->us->findClosestColor($searchColor, $colors);

        $this->assertEquals(1, $result['index'], 'Closest should be green');
    }

    public function testReadColorCsvBadFilename()
    {
        $badFilename = 'no-such-file.csv';
        $result = $this->us->readColorCsv($badFilename);
        $this->assertEquals("Cannot find data file $badFilename", $result['error']); 
    }

    public function testReadColorCsv() 
    {
        $images = $this->us->readColorCsv('data/test/extracted-colors-test.csv');
        
        $this->assertEquals(2, count($images));

        $image = $images[0];
        $this->assertEquals('Abisarika_Nayika.jpg', $image['filename']);
        $this->assertEquals('Abisarika Nayika', $image['name']);
        $colors = $image['colors'];
        $this->assertEquals('252029', $colors[0]['rgb']);
        $this->assertEquals('eeeeee', $colors[1]['rgb']);
        $this->assertEquals('71', $colors[0]['weight']);
        $this->assertEquals('29', $colors[1]['weight']);
    }
    
    public function testReadableNameSuffixRemoval()
    {
        $this->assertEquals('foo', $this->us->readableName('foo.jpg'));
        $this->assertEquals('foo', $this->us->readableName('foo.JPG'));
    }

    public function testReadableNameUnderscoreToSpace()
    {
        $this->assertEquals('A Walk In The Park', $this->us->readableName('A_Walk_In_The_Park.jpg'));
    }
}
