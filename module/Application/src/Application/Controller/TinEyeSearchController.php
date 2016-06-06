<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use TinEye\MulticolorEngineRequest;


class TinEyeSearchController extends AbstractActionController
{
    // Perform a TinEye API search by color.
    //
    // Pass this a single GET parameter named color
    // which is an RGB color expressed as 6 hex digits,
    // e.g. 'color=836cc7'
    //
    // This action returns the exact JSON provided by the TinEye
    // API color_search method:
    //
    //   https://services.tineye.com/developers/multicolorengine/methods/color_search.html
    //
    // The reason this action exists: TinEye's security model doesn't allow browser
    // JavaScript to call the API.
    public function searchAction()
    {
        $api = new MulticolorEngineRequest ('http://multicolorengine.tineye.com/focalfilter/rest/');
            //TinEyeCredentials::username, TinEyeCredentials::password);

        $result = new JsonModel(array(
            'some_parameter' => 'some value',
            'jsonArray' => array(1,2,3,4,5,6),
            'success'=>true,
        ));

        error_log(print_r($this->getServiceLocator()->get('config')['tineye']));

        return $result;
    }
}
