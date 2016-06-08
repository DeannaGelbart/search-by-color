<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

abstract class AbstractTinEyeClientController extends AbstractActionController
{
    protected $tinEyeService;
    protected $tinEyeConfig;

    public function __construct($tinEyeService, $tinEyeConfig)
    {
        $this->tinEyeService = $tinEyeService;
        $this->tinEyeConfig = $tinEyeConfig;
    }

    // Reconstruct a more human readable name from an image filename.
    public function readableName($filename)
    {
        $name = basename($filename);
        $name = str_replace(".jpg", "", $name);
        $name = str_replace(".JPG", "", $name);
        $name = str_replace(".jpeg", "", $name);

        // Whether this mapping of _ is useful will depend on your data set.
        $name = str_replace("_", " ", $name);

        return $name;
    }
}