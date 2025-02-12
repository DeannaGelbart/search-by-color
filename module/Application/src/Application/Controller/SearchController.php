<?php

namespace Application\Controller;

use Zend\View\Model\JsonModel;
use Application\Service\ImageService;

// This class provides a search-by-color web service.
//
// It requires a colorcoordinator-zf2/data/extracted-colors.csv file containing the list of dominant colors for each image.
// For instructions on how to create the file, see https://github.com/dgelbart/colorcoordinator-zf2/blob/master/README.md
class SearchController extends AbstractSearchController
{
    const MAX_IMAGES_TO_RETURN = 20;
    const MINIMUM_COLOR_WEIGHT = 10;
    const MINIMUM_IMAGE_SCORE = 1.7;
    const MAXIMUM_COLOR_DISTANCE = 40;

    private $imageService;

    public function __construct($imageService)
    {
        $this->imageService = $imageService;
    }

    // Comparison function for descending order sort.
    public function compareMatchesByScore($a, $b)
    {
        $a = $a['score'];
        $b = $b['score'];
        if ($a == $b) {
            return 0;
        }
        return ($a > $b) ? -1 : 1;
    }


    // Please see the comments for AbstractSearchController.searchAction.
    public function searchAction()
    {
        if (!isset($_GET['color'])) {
            return $this->noColorError();
        }
        $searchColor = $_GET['color'];
        if (!($this->imageService->isValidHexRgbColor($searchColor))) {
            return $this->badColorFormatError();
        }

        // Read the entire list of images' dominant colors. In future we may want to cache this list.
        $imagesDominantColors = $this->imageService->readColorCsv('data/extracted-colors.csv', self::MINIMUM_COLOR_WEIGHT);
        if (isset($imagesDominantColors['error'])) {
            return $this->errorResponse($imagesDominantColors['error']);
        }

        // Return matching images, in descending order of the match quality score.
        $matches = $this->imageService->scoreImageSet($searchColor, $imagesDominantColors, self::MINIMUM_IMAGE_SCORE, self::MAXIMUM_COLOR_DISTANCE);
        
        // Handle sorting
        $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'score';
        $sortOrder = isset($_GET['order']) ? $_GET['order'] === 'asc' : false;
        
        if ($sortBy === 'size') {
            $matches = $this->imageService->sortMatchesBySize($matches, $sortOrder);
        } else {
            usort($matches, array($this, "compareMatchesByScore"));
        }

        $matchesToReturn = array();
        $returnCount = 0;
        foreach ($matches as $match) {
            $returnCount++;
            if ($returnCount <= self::MAX_IMAGES_TO_RETURN) {
                $matchesToReturn[] = $match;
            }
        }

        return new JsonModel(array(
            'match_count' => count($matchesToReturn),
            'matches' => $matchesToReturn
        ));
    }
}
