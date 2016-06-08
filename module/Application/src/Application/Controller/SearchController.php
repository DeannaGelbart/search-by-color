<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

// This controller allows searching images by color.
//
// It requires a data/extracted-colors.csv file containing the list of dominant colors for each image.
// For how to generate the file, see https://github.com/dgelbart/colorcoordinator-zf2/blob/master/README.md
class SearchController extends AbstractActionController
{
    private function errorResponse($message)
    {
        $this->response->setStatusCode(500);
        $error = new JsonModel(array(
            'message' => $message
        ));
        return $error;
    }
    
    // This JSON web service performs a search by color.
    //
    // This service takes a single GET parameter named color which is an RGB color expressed as 6 hex digits.
    //
    // Example: a GET request to "/tin-eye-search?color=836cc7" will search for color 836cc7.
    //
    // If there is an error, this service returns HTTP status code 500 and the JSON body contains
    // one field 'message' containing the error message.
    //
    // Otherwise, this service returns HTTP status code 200 and this JSON body:
    //    match_count: An integer giving the number of matching images (may be 0).
    //    matches: A list containing one entry for each matching image. Each entry has these fields:
    //      filename: the filename of the matching image.
    //      name: the display name of the matching image.
    //      score: A numerical score for how well the image matched the search color.
    public function searchAction()
    {
        if (!isset($_GET['color'])) {
            return $this->errorResponse('You must pass this service a GET parameter named color (RGB represented as 6 hex digits).');
        }

        $color = $_GET['color'];
        if (!preg_match('/^[0-9A-Za-z]{6}$/', $color)) {
            return $this->errorResponse('The color you pass to this service must be RGB represented as 6 hex digits.');
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
