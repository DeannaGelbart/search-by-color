<?php

namespace Application\Service;

// Wrap the TinEye PHP client library to allow controller testability through injection.
interface TinEyeServiceInterface
{
    // Create a ready-to-use MulticolorEngineRequest.
    public function createMulticolorEngineRequest($tinEyeConfig);
}