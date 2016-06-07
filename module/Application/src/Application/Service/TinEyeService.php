<?php

namespace Application\Service;

use TinEye\MulticolorEngineRequest;

class TinEyeService implements TinEyeServiceInterface
{
    public function createMulticolorEngineRequest($tinEyeConfig)
    {
        $tinEyeUrl = 'http://multicolorengine.tineye.com/' . $tinEyeConfig['username'] . '/rest/';
        return new MulticolorEngineRequest($tinEyeUrl, $tinEyeConfig['username'], $tinEyeConfig['password']);
    }
}