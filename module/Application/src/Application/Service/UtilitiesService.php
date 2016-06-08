<?php

namespace Application\Service;

use \Color; // Color routines from https://github.com/matthewbaggett/php-color

// A class for self-contained utility methods.
class UtilitiesService
{
    // This searches for $hexSearchColor in the array of colors $hexColors and returns
    // an associative array: 
    //   'index': index of the color in $hexColors which is nearest to $hexSearchColor
    //   'dist' : Euclidean distance in Lab colorspace between $hexSearchColor and $hexColors[$index]
    //
    // Each color passed to this method should be a 6-digit RGB hex string e.g. '3F8A34'.
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
        $distanceToClosest = $searchColor->getDistanceLabFrom($colors[$indexOfClosest]);

        for ($i = 1; $i < count($colors); $i++) {
            $dist = $searchColor->getDistanceLabFrom($colors[$i]);
            if ($dist < $distanceToClosest) {
                $distanceToClosest = $dist;
                $indexOfClosest = $i;
            }
        }
        return array('index' => $indexOfClosest, 'dist' => $distanceToClosest);
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
    public function readColorCsv($filename)
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
            // Skip rows that don't seem kosher.
            if (($fieldCount % 2 == 0) && ($fieldCount >= 4)) {
                $result = array('filename' => $fields[0], 'name' => $fields[1]);

                $colors = array();
                for ($i = 2; $i < $fieldCount; $i += 2) {
                    $color = array('rgb' => $fields[$i], 'weight' => $fields[$i + 1]);
                    $colors[] = $color;
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
}