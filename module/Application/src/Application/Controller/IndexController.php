<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        // This returns the HTML which drives the single-page UI of the application.
        // There is no work for the controller here since it's a client-side UI.
        return new ViewModel();
    }
}
