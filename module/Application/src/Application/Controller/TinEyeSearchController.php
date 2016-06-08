<?php

namespace Application\Controller;

use Zend\View\Model\JsonModel;

// This class provides a search-by-color service using the TinEye API.
// This class is included in the project in case you would like to try the TinEye API for this.
// Normally this application uses SearchController.php instead.
// 
// Note that it's not possible for browser-side JavaScript to call the TinEye API directly,
// due to TinEye's security model.
class TinEyeSearchController extends AbstractSearchController
{
    private $imageService;
    private $tinEyeService;
    private $tinEyeConfig;

    public function __construct($imageService, $tinEyeService, $tinEyeConfig)
    {
        $this->imageService = $imageService;
        $this->tinEyeService = $tinEyeService;
        $this->tinEyeConfig = $tinEyeConfig;
    }

    // This JSON web service performs a search by color.
    // Please see the comments for AbstractSearchController.searchAction.
    //
    // For this controller, the route is /tin-eye-search instead of /search.
    public function searchAction()
    {
        if (!isset($_GET['color'])) {
            return $this->noColorError();
        }
        $color = $_GET['color'];
        if (!($this->imageService->isValidHexRgbColor($color))) {
            return $this->badColorFormatError();
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
                $name = $this->imageService->readableName($result->filepath);

                // Guard against broken links due to Linux being case sensitive while Mac and Windows are not.
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
