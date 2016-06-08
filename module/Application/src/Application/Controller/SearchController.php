<?php

namespace Application\Controller;

use Zend\View\Model\JsonModel;

// This class provides a search-by-color web service.
//
// It requires a colorcoordinator-zf2/data/extracted-colors.csv file containing the list of dominant colors for each image.
// For instructions on how to create the file, see https://github.com/dgelbart/colorcoordinator-zf2/blob/master/README.md
class SearchController extends AbstractSearchController
{
    const MAX_IMAGES_TO_RETURN = 20;
    const MINIMUM_COLOR_WEIGHT = 10;
    const MINIMUM_IMAGE_SCORE = 5;

    private $imageService;

    public function __construct($imageService)
    {
        $this->imageService = $imageService;
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
        
        $matches = $this->imageService->scoreImageSet($searchColor, $imagesDominantColors, self::MINIMUM_IMAGE_SCORE);
        
        // Now we need to sort by score, and only take the top N scoring images. 
        // self::MAX_IMAGES_TO_RETURN

        return new JsonModel(array(
            'match_count' => count($matches),
            'matches' => $matches
        ));
    }
}
