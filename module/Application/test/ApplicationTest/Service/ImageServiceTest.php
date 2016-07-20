<?php

namespace ApplicationTest\Model;

use Application\Service\ImageService;
use PHPUnit_Framework_TestCase;

class ImageServiceTest extends PHPUnit_Framework_TestCase
{
    private $service;

    public function __construct()
    {
        $this->service = new ImageService();
    }

    public function testFindClosestColorWithExactMatch()
    {
        $red = 'ff0000';
        $green = '00ff00';
        $blue = '0000ff';
        $colors = array($red, $green, $blue);
        $searchColor = $blue;

        $result = $this->service->findClosestColor($searchColor, $colors);

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

        $result = $this->service->findClosestColor($searchColor, $colors);

        $this->assertEquals(1, $result['index'], 'Closest should be green');
    }

    public function testReadColorCsvBadFilename()
    {
        $badFilename = 'no-such-file.csv';
        $result = $this->service->readColorCsv($badFilename, 10);
        $this->assertEquals("Cannot find data file $badFilename", $result['error']); 
    }

    public function testReadColorCsv()
    {
        $images = $this->service->readColorCsv('data/test/extracted-colors-test.csv', 0);

        $this->assertEquals(2, count($images));

        $image = $images[0];
        $this->assertEquals('Abisarika_Nayika.jpg', $image['filename']);
        $this->assertEquals('Abisarika Nayika', $image['name']);
        $colors = $image['colors'];
        $this->assertEquals(2, count($colors));
        $this->assertEquals('252029', $colors[0]['rgb']);
        $this->assertEquals('eeeeee', $colors[1]['rgb']);
        $this->assertEquals('71', $colors[0]['weight']);
        $this->assertEquals('29', $colors[1]['weight']);
    }

    public function testReadColorCsvWithMinimumWeight()
    {
        $images = $this->service->readColorCsv('data/test/extracted-colors-test.csv', 50);

        $this->assertEquals(2, count($images));

        $image = $images[0];
        $colors = $image['colors'];
        $this->assertEquals(1, count($colors));
        $this->assertEquals('252029', $colors[0]['rgb']);
        $this->assertEquals('71', $colors[0]['weight']);
    }

    public function testScoreImageSetWithCloselyMatchingColor()
    {
        $images = $this->service->readColorCsv('data/test/extracted-colors-test.csv', 10);

        // Almost this exact color is in the CSV with a large weight, therefore the image it's in should match and
        // should get a large score.
        $searchColor = '242128';

        $scores = $this->service->scoreImageSet($searchColor, $images, 4, 10);
        $this->assertEquals(1, count($scores));
        $this->assertGreaterThanOrEqual(40, $scores[0]['score']);
    }

    public function testScoreImageSetWithPoorlyMatchingColor()
    {
        $images = $this->service->readColorCsv('data/test/extracted-colors-test.csv', 10);

        // Nothing close to this is in the CSV, therefore neither image should match.
        $searchColor = '000000';

        $scores = $this->service->scoreImageSet($searchColor, $images, 4, 10);
        $this->assertEquals(0, count($scores));
    }

    public function testScoreImageSetWithMinimumScore()
    {
        $images = $this->service->readColorCsv('data/test/extracted-colors-test.csv', 10);

        $searchColor = '252029';

        $scores = $this->service->scoreImageSet($searchColor, $images, 100);
        $this->assertEquals(1, count($scores));
    }

    public function testReadableNameSuffixRemoval()
    {
        $this->assertEquals('foo', $this->service->readableName('foo.jpg'));
        $this->assertEquals('foo', $this->service->readableName('foo.JPG'));
    }

    public function testReadableNameUnderscoreToSpace()
    {
        $this->assertEquals('A Walk In The Park', $this->service->readableName('A_Walk_In_The_Park.jpg'));
    }
}
