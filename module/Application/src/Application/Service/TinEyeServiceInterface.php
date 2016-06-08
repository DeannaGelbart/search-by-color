<?php

namespace Application\Service;

// Interface provided for testability.
interface TinEyeServiceInterface
{
    // Create a ready-to-use MulticolorEngineRequest.
    public function createMulticolorEngineRequest($tinEyeConfig);
}