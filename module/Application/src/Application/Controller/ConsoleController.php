<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;
use Zend\View\Model\ConsoleModel;
use TinEye\Image;
use Application\Service\ImageService;


class ConsoleController extends AbstractActionController
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

    // This command line console action extracts the dominant colors from an image using the TinEye API.
    //
    // Usage:
    // $ php public/index.php console extract-colors <imageFilename> <imageName>
    // Example:
    // $ php public/index.php console extract-colors Ready_Set_Go.jpg "Ready, Set, Go"
    //
    // The output will be in CSV format with the following columns:
    //
    // Image Filename,Image Name,Color1,Weight1,...,Color10,Weight10
    //
    // where Color<n> and Percent<n> are the hex value and weight (importance) for dominant color n.
    // The sort order of the colors keeps similar colors together, rather than being in order of weight.
    // The number of colors may be less than 10.
    //
    // Example output:
    // Blue_smoke.jpg,"Blue smoke",503a4c,32.89,baaaa9,1.7,836869,9.71,9f8374,16.06,cbb194,9.64,eeeeee,30
    // Negev_Israel.jpg,"Negev, Israel",755548,2.68,8a5f40,4.99,8b4733,1.6,ad9084,1.52,a9905d,10.34,ab8642,35.68,b48c1f,4.84,a6b0a6,18.35,eeeeee,20
    public function extractColorsAction()
    {
        $request = $this->getRequest();
        $model = new ConsoleModel();

        if (!$request instanceof ConsoleRequest){
            throw new \RuntimeException('This action can only be used from the command line console.');
        }

        $filename = $request->getParam('imageFilename');
        $name = $request->getParam('imageName');
        if (!isset($name)) {
            $name = $this->imageService->readableName($filename);
        }

        $image = new Image($filename, '', basename($filename));

        // Call TinEye API to extract colors:
        //   https://services.tineye.com/developers/multicolorengine/methods/extract_image_colors.html
        //   https://services.tineye.com/library/php/docs/classMulticolorEngineRequest.html
        $tinEyeApi = $this->tinEyeService->createMulticolorEngineRequest($this->tinEyeConfig);
        $tinEyeJson = $tinEyeApi->extract_image_colors_image(array($image), false, false, 10, 'rgb');

        if ($tinEyeJson->status == 'fail' || $tinEyeJson->status == 'warn') {
             $model->setErrorLevel(1);
             $model->setResult('TinEye API error: ' + join('; ', $tinEyeJson->error));
             return $model;
        }

        $colors = array();
        foreach ($tinEyeJson->result as $result) {
            $colors[] = $this->imageService->rgbArrayToHex($result->color) . ',' . $result->weight;
        }

        $model->setErrorLevel(0);
        $model->setResult(basename($filename) . ',"' . $name . '",' . join(',', $colors) . "\n");
        return $model;
    }
}