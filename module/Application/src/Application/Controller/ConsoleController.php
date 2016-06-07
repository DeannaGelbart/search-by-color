<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;
use Application\Controller\TinEyeProxyController;
use Application\ControllerFactory\TinEyeProxyControllerFactory;


class ConsoleController extends AbstractActionController
{

    // This command line console action extracts the dominant colors from an image using the TinEye API.
    //
    // Usage:
    // $ php public/index.php console extract-colors <imageFilename> <imageName>
    // Example:
    // $ php public/index.php console extract-colors Ready_Set_Go.jpg "Ready, Set, Go"
    //
    // The output will be in CSV format with the following columns:
    //
    // Image Filename,Image Name,Color1,Percent1,...,Color10,Percent10
    
    // where Color<n> and Percent<n> are the hex value and percentage of image for dominant color n.
    // The colors will be provided in descending order of percentage.
    // The number of colors may be less than 10.
    //
    // Example output:
    // "Ready, Set, Go",Ready_Set_Go.jpg,
    public function extractColorsAction()
    {
        $request = $this->getRequest();

        if (!$request instanceof ConsoleRequest){
            throw new \RuntimeException('This action can only be used from the command line console.');
        }

        $filename = $request->getParam('imageFilename');
        $name = $request->getParam('imageName');

        return "Done! Got filename $filename for name $name";
    }
}