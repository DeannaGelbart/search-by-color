<?php

namespace Application\Service;

interface TinEyeServiceInterface
{
    // Create a ready-to-use MulticolorEngineRequest.
    public function createMulticolorEngineRequest($tinEyeConfig);
}