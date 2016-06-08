<?php

namespace Application\Controller;

use Zend\View\Model\JsonModel;

// This class provides a search-by-color service using the TinEye API. 
// 
// Note that it's not possible for browser-side JavaScript to call the TinEye API directly,
// due to TinEye's security model.
class TinEyeSearchController extends AbstractSearchController
{
    // This JSON web service performs a search by color.
    // For documentation see AbstractSearchController.searchAction.
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
