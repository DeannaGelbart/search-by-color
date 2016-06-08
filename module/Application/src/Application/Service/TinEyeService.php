<?php

namespace Application\Service;

use TinEye\MulticolorEngineRequest;

class TinEyeService implements TinEyeServiceInterface
{
    // Create a ready-to-use TinEye MulticolorEngineRequest.
    public function createMulticolorEngineRequest($tinEyeConfig)
    {
        $tinEyeUrl = 'http://multicolorengine.tineye.com/' . $tinEyeConfig['username'] . '/rest/';
        return new MulticolorEngineRequest($tinEyeUrl, $tinEyeConfig['username'], $tinEyeConfig['password']);
    }
}