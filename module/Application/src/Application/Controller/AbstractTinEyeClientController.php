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
}