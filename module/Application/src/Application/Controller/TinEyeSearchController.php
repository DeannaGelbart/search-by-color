<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Application\Service\TinEyeServiceInterface;
use Application\Utilities;

// Note that it's not possible for browser-side JavaScript to call the TinEye API directly,
// due to TinEye's security model.
class TinEyeSearchController extends AbstractActionController
{
    private $utilitiesService;
    private $tinEyeService;
    private $tinEyeConfig;

    public function __construct($utilitiesService, $tinEyeService, $tinEyeConfig)
    {
        $this->utilitiesService = $utilitiesService;
        $this->tinEyeService = $tinEyeService;
        $this->tinEyeConfig = $tinEyeConfig;
    }
    
    private function errorResponse($message)
    {
        $this->response->setStatusCode(500);
        $error = new JsonModel(array(
            'message' => $message
        ));
        return $error;
    }
    
    // This JSON web service performs a TinEye API search by color.
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

        // Call TinEye API search by color:
        //   https://services.tineye.com/developers/multicolorengine/methods/color_search.html
        //   https://services.tineye.com/library/php/docs/classMulticolorEngineRequest.html
        $tinEyeApi = $this->tinEyeService->createMulticolorEngineRequest($this->tinEyeConfig);
        $tinEyeJson = $tinEyeApi->search_color(array($color), array(100), false, false);

        if ($tinEyeJson->status == 'fail' || $tinEyeJson->status == 'warn') {
            return $this->errorResponse(join('; ', $tinEyeJson->error));
        }

        $matches = array();
        if ($tinEyeJson->count > 0) {
            foreach ($tinEyeJson->result as $result) {
                $name = $this->utilitiesService->readableName($result->filepath);

                // Guard against broken links due to uppercase/lowercase differences.
                $filepath = $result->filepath;
                $filepath = str_replace(".JPG", ".jpg", $filepath);

                $matches[] = array('name' => $name,
                    'filename' => $filepath,
                    'score' => $result->score
                );
            }
        }

        // $apiResult is now the output of json_decode on the TinEye API response.
        return new JsonModel(array(
            'match_count' => $tinEyeJson->count,
            'matches' => $matches
        ));
    }
}
