<?php

class ErrorController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $this->_helper->layout()->disableLayout();
        $this->_logger = Zend_Registry::get("ELog");
        //$this->_helper->viewRenderer->setNoRender(true);
    }

    public function errorAction() {

        header("Content-type:text/html;charset=utf-8");
        $errors = $this->_getParam('error_handler');
        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = '很抱歉，您要访问的页面不存在!'; //Page not found
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = '应用程序错误'; //Application error
                break;
        }

        // Log exception, if logger available
        if ($log = $this->getELog()) {
            $log->log("[IP: " . Custom_Controller_Plugin_Ipaddress::getIP() . "]" . $this->view->message . $errors->exception, $priority, $errors->exception);
            $log->log('Request Parameters:' . json_encode($errors->request->getParams()), $priority, "\r\n");
        }
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        $this->view->request = $errors->request;
        $this->view->showtype = 1;
        $this->view->URL = "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }

    public function getLog() {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('log')) {
            return false;
        } else {
            $log = $bootstrap->getResource('log');
            return $log;
        }
    }

    public function getELog() {
        return Zend_Registry::get("ELog");
    }
   
}
