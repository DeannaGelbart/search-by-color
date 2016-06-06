<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use TinEye\MulticolorEngineRequest;


class TinEyeSearchController extends AbstractActionController
{
    private $tinEyeCredentials;

    public function __construct($tinEyeCredentials) {
        $this->tinEyeCredentials = $tinEyeCredentials;
    }

    // This JSON web service performs a TinEye API search by color.
    //
    // It takes a single GET parameter named color
    // which is an RGB color expressed as 6 hex digits,
    // e.g. "/tin-eye-search?color=836cc7".
    //
    // This service returns essentially the same JSON format as provided by the TinEye API:
    //    status: Either 'ok', 'warn', or 'fail'.
    //    error: If status is not 'ok', this will be a string that describes a problem.
    //    count: If status is 'ok', this is an integer giving the number of matching images.
    //    result: A list containing one entry for each matching image. Each entry has these fields:
    //       score: A numerical score for how well the image matched the search color.
    //       filepath: The image pathname that was provided when the image was indexed.
    //
    // The reason this service exists: TinEye's security model doesn't allow browser
    // JavaScript to call the API.
    //
    // The documentation of the underlying TinEye API color_search method is here:
    //
    //   https://services.tineye.com/developers/multicolorengine/methods/color_search.html
    //
    public function searchAction()
    {
        if (!isset($_GET['color'])) {
            $error = new JsonModel(array(
                'status' => 'error',
                'error' => 'You must pass this service a GET parameter named color (RGB represented as 6 hex digits).'
            ));
            return $error;
        }
        $color = $_GET['color'];
        if (!preg_match ('/^[0-9A-Za-z]{6}$/' , $color)) {
            $error = new JsonModel(array(
                'status' => 'error',
                'error' => 'The color you pass to this service must be RGB represented as 6 hex digits.'
            ));
            return $error;
        }

        // Call TinEye API.
        $apiUrl = 'http://multicolorengine.tineye.com/' . $this->tinEyeCredentials['username'] . '/rest/';
        $api = new MulticolorEngineRequest($apiUrl,
            $this->tinEyeCredentials['username'], $this->tinEyeCredentials['password']);
        $apiResult = $api->search_color(array($color), array(100), false, false);

        // $apiResult is now the output of json_decode on the TinEye API response.
        return new JsonModel((array) $apiResult);


    }
}
