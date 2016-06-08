<?php

namespace Application\Controller;

use Zend\View\Model\JsonModel;

// This class provides a search-by-color service using homegrown search code. 
//
// It requires a data/extracted-colors.csv file containing the list of dominant colors for each image.
// For how to generate the file, see https://github.com/dgelbart/colorcoordinator-zf2/blob/master/README.md
class SearchController extends AbstractSearchController
{
    // This JSON web service performs a search by color.
    // For documentation see AbstractSearchController.searchAction.
    public function searchAction()
    {
        if (!isset($_GET['color'])) {
            return $this->noColorError();
        }
        $color = $_GET['color'];
        if (!($this->isValidColor($color))) {
            return $this->badColorFormatError();
        }

        // TODO
        $matchCount = 0;
        $matches = array();

        // $apiResult is now the output of json_decode on the TinEye API response.
        return new JsonModel(array(
            'match_count' => $matchCount,
            'matches' => $matches
        ));
    }
}
