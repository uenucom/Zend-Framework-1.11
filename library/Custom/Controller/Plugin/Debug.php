<?php

class Custom_Controller_Plugin_Debug extends Zend_Controller_Plugin_Abstract {

    private $startTime = 0;
    private $endTime = 0;

    private function getsection() {
        list($msec, $sec) = explode(" ", microtime());
        return $sec + $msec;
    }

    public function routeStartup(Zend_Controller_Request_Abstract $request) {
        $this->startTime = $this->getsection();
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request) {
        $response = $this->getResponse();
        $this->endTime = $this->getsection();
        $startTime = $this->startTime;
        $endTime = $this->endTime;
        $allTime = ($this->endTime - $this->startTime);
        $moduleName = $request->getModuleName();
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();
        $params = Zend_Debug::dump($request->getParams(), null, false);
        
        $view_path = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewFile = $view_path->getViewScript();
        $view = $view_path->view;
        $vars = $view->getVars();
        //$view = $smarty;
        //$vars = $smarty->dispaly();
        $viewVars =Zend_Debug::dump($vars,null,false);
        //$viewVars = "&nbsp;";
        $str = '
         <br />
        <table width="309" border="1" cellspacing="0" cellpadding="0">
            <tr>
                <td colspan="2" align="center">调试信息</td>
            </tr>
            <tr>
                <td width="97">开始时间</td><td width="206">&nbsp;' . $startTime . '</td>
            </tr>
            <tr>
                <td>结束时间</td><td>&nbsp;' . $endTime . '</td>
            </tr>
            <tr>
                <td>花费时间</td><td>&nbsp;' . $allTime . '秒</td>
            </tr>
            <tr>
                <td>使用模块</td><td>&nbsp;' . $moduleName . '</td>
            </tr>
            <tr>
                <td>控 制 器</td><td>&nbsp;' . $controllerName . '</td>
            </tr>
            <tr>
                <td>方　　法</td><td>&nbsp;' . $actionName . '</td>
            </tr>
            <tr>
                <td>视图文件</td><td>&nbsp;' . $viewFile . '</td>
            </tr>
            <tr>
                <td>请求参数</td><td>&nbsp;' . $params . '</td>
            </tr>
            <tr>
                <td>视图变量</td><td>&nbsp;' . $viewVars . '</td>
            </tr>
        </table>
        ';
        $response->appendBody($str);
    }

}
