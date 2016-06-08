<?php

namespace Application\Controller;

use Zend\View\Model\JsonModel;

// This class provides a search-by-color service using homegrown search code. 
//
// It requires a colorcoordinator-zf2/data/extracted-colors.csv file containing the list of dominant colors for each image.
// For how to generate the file, see https://github.com/dgelbart/colorcoordinator-zf2/blob/master/README.md
class SearchController extends AbstractSearchController
{
    const DOMINANT_COLORS_CSV_FILE = 'data/extracted-colors.csv';

    // This JSON web service performs a search by color.
    // For documentation see AbstractSearchController.searchAction.
    public function searchAction()
    {
        if (!isset($_GET['color'])) {
            return $this->noColorError();
        }
        $searchColor = $_GET['color'];
        if (!($this->isValidColor($searchColor))) {
            return $this->badColorFormatError();
        }

        $colorData = $this->utilitiesService->readColorCsv(self::DOMINANT_COLORS_CSV_FILE);
        if (isset($colorData['error'])) {
            return $this->errorResponse($colorData['error']);
        }

        // TODO
        // find the closest matching color.
        // if colordistance still > threshold then not this image.
        // then take weight * 1/max(colorDistance, 0.001) as the score for each image
        // take top N images


        $matchCount = 0;
        $matches = array();

        // $apiResult is now the output of json_decode on the TinEye API response.
        return new JsonModel(array(
            'match_count' => $matchCount,
            'matches' => $matches
        ));
    }
}
