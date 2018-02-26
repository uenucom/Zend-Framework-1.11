<?php
class Custom_Controller_PreDispatchPlugin extends Zend_Controller_Action {
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
        $controller = $dispatcher->getController($request);
        if (!$controller) {
            $controller = $dispatcher->getDefaultControllerName($request);
        }
        $action = $dispatcher->getAction($request);
        if (!method_exists($controller, $action)) {
            $defaultAction = $dispatcher->getDefaultAction();
            $controllerName = $request->getControllerName();
            $response = Zend_Controller_Front::getInstance()->getResponse();
            $response->setRedirect('/' . $controllerName .'/' . $defaultAction);
            $response->sendHeaders();
            exit;
        }
    }

}
