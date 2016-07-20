<?php

namespace Application\Service;

use \Color; // Color routines from https://github.com/matthewbaggett/php-color

// Library for dealing with images and colors.
class ImageService
{
    // Return Euclidean distance in Lab colorspace
    //
    // Like the similarly-named function in https://github.com/hasbridge/php-color/blob/master/Color.php but
    // with a bugfix in order to compute Euclidean distance.
    //
    // Results should match http://colormine.org/delta-e-calculator/
    public function getEuclideanDistanceLab(Color $color1, Color $color2)
    {
        $lab1 = $color1->toLabCie();
        $lab2 = $color2->toLabCie();

        $lDiff = ($lab2['l'] - $lab1['l'])*($lab2['l'] - $lab1['l']);
        $aDiff = ($lab2['a'] - $lab1['a'])*($lab2['a'] - $lab1['a']);
        $bDiff = ($lab2['b'] - $lab1['b'])*($lab2['b'] - $lab1['b']);

        $delta = sqrt($lDiff + $aDiff + $bDiff);

        return $delta;
    }



    // This searches for $hexSearchColor in the array of colors $hexColors and returns
    // an associative array: 
    //   'index': index of the color in $hexColors which is nearest to $hexSearchColor
    //   'dist' : Euclidean distance in Lab colorspace between $hexSearchColor and $hexColors[$index]
    //
    // Each color passed to this method must be a 6-digit RGB hex string e.g. '3F8A34'.
    //
    // We use Lab colorspace for distance because, compared to RGB, Lab has a tighter relationship
    // between perceptual distance and Euclidean distance.
    public function findClosestColor($hexSearchColor, $hexColors)
    {
        $searchColor = new Color();
        $searchColor->fromRgbString($hexSearchColor);

        $colors = array();
        foreach ($hexColors as $hexColor) {
            $color = new Color();
            $color->fromRgbString($hexColor);
            $colors[] = $color;
        }

        $indexOfClosest = 0;
        $distanceToClosest = $this->getEuclideanDistanceLab($searchColor, $colors[$indexOfClosest]);


        for ($i = 1; $i < count($colors); $i++) {
            $dist = $this->getEuclideanDistanceLab($searchColor, $colors[$i]);
            if ($dist < $distanceToClosest) {
                $distanceToClosest = $dist;
                $indexOfClosest = $i;
            }
        }
        return array('index' => $indexOfClosest, 'dist' => $distanceToClosest);
    }

    // Given a color $searchColor being searched for, and a list $imagesDominantColors of dominant colors for each image,
    // this function returns a score for each image indicating how well it matches $searchColor.
    //
    // $searchColor must be a 6-digit RGB hex string e.g. '3F8A34'.
    // $imagesDominantColors must be the array returned by readColorCsv().
    // Images scoring lower than $minimumScore will not be returned by this function.
    // Image colors further away than $maximumDistance (Euclidean distance in Lab colorspace) will be ignored.
    //
    // This function returns an an array each element of which is an associative array representing one image:
    //   'filename': e.g., 'Ready_Set_Go.jpg'
    //   'name': e.g., 'Ready, Set, Go'
    //   'score': A numerical score for how well the image matched the search color. Higher score is better.
    //   and for debug/diagnostic purposes:
    //   'distance': Euclidean distance in Lab color space between search color and closest matching color.
    //   'weight': Importance of closest matching color in the image.
    //
    // Note that the number of dominant colors that we extract when building the index of dominant
    // colors is partly a choice of the color extraction code, not just an inherent property of an image.
    // You may want to tune that number to find the best results for your image set.
    //
    // The scoring formula is ad hoc and could benefit from more tuning and expertise.
    //
    // YOU ARE EXPECTED TO TUNE $minimumScore AND $maximumDistance FOR YOUR DATA SET.    
    public function scoreImageSet($searchColor, $imagesDominantColors, $minimumScore = 0, $maximumDistance = 100)
    {
        $results = array();

        // Loop through all the images and score them for how well they match $searchColor
        foreach ($imagesDominantColors as $imageDominantColors)
        {
            // Make the list of colors in this image.
            $hexColors = array();
            foreach ($imageDominantColors['colors'] as $colors)
            {
                $hexColors[] = $colors['rgb'];
            }

            // Find the closest matching color.
            $match = $this->findClosestColor($searchColor, $hexColors);
            $closestMatchDistance = $match['dist'];
            $closestMatchIndex = $match['index'];
            $weight = $imageDominantColors['colors'][$closestMatchIndex]['weight'];

            // If the closest matching color is close enough to the search color...
            // (You may want to play with the threshold here to find the best results for your image set.)
            //
            // (Perhaps we could improve performance in the future if, rather than only looking at the closest
            // matching color, we also look at further-but-not-too-far colors.)
            if ($closestMatchDistance < $maximumDistance) {

                // Prevent distance-based part of score from getting so high that it swamps the weight.
                $d = $closestMatchDistance;
                if ($d < 0.1) {
                    $d = 0.1;
                }

                // Distance-based part of score.
                $score = 1/$d;

                // Weight score by the importance of the closest matching color in the image.
                $score = $score * $weight;

                if ($score >= $minimumScore) {
                    $result = array('name' => $imageDominantColors['name'],
                        'filename' => $imageDominantColors['filename'],
                        'distance' => $closestMatchDistance,
                        'weight' => $weight,
                        'score' => $score);
                    $results[] = $result;
                }
            }
        }

        return $results;
    }


    // This reads a CSV file each line of which contains dominant color data for one image.
    // (See ConsoleController.extractColorsAction for the CSV format.)
    //
    // This function returns an array each element of which contains:
    //   'filename' e.g., 'Ready_Set_Go.jpg'
    //   'name' e.g., 'Ready, Set, Go'
    //   'colors': array of dominant colors each element of which contains
    //      'rgb': this color as a 6-digit RGB hex string e.g. '3F8A34'
    //      'weight': score for the importance of this color in the image
    // If the file cannot be read successfully this function returns an associative array
    // containing the key 'error', the value of which is the error message.
    //
    // Any colors with less weight than $minimumWeight will not be returned.
    public function readColorCsv($filename, $minimumWeight)
    {
        if (!is_file($filename)) {
            return array('error' => 'Cannot find data file ' . $filename);
        }
        if (!($handle = fopen($filename, 'r'))) {
            return array('error' => 'Unable to open file ' . $filename . ' for reading');
        }

        $results = array();
        while ($fields = fgetcsv($handle, 1000, ",")) {
            $fieldCount = count($fields);

            // If the row has a plausible number of fields
            if (($fieldCount % 2 == 0) && ($fieldCount >= 4)) {
                $result = array('filename' => $fields[0], 'name' => $fields[1]);
                
                $colors = array();
                for ($i = 2; $i < $fieldCount; $i += 2) {
                    $color = array('rgb' => $fields[$i], 'weight' => $fields[$i + 1]);

                    if ($color['weight'] >= $minimumWeight) {
                        $colors[] = $color;
                    }
                }

                $result['colors'] = $colors;
                $results[] = $result;
            }
        }
        fclose($handle);

        return $results;
    }

    // Reconstruct a more human readable name from an image filename.
    public function readableName($filename)
    {
        $name = basename($filename);
        $name = str_replace(".jpg", "", $name);
        $name = str_replace(".JPG", "", $name);

        // Whether this mapping of _ is useful will depend on your data set.
        $name = str_replace("_", " ", $name);

        return $name;
    }

    public function isValidHexRgbColor($color)
    {
        // Check for 6 hex digits. 
        return preg_match('/^[0-9A-Fa-f]{6}$/', $color);
    }

    // Convert an RGB color represented as 3 integers to a 6-digit hex string.
    public function rgbArrayToHex($rgb)
    {
        return sprintf('%02x', $rgb[0]) . sprintf('%02x', $rgb[1]) . sprintf('%02x', $rgb[2]);
    }
}