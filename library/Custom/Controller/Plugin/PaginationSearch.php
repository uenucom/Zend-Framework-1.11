<?php

class Custom_Controller_Plugin_PaginationSearch {

    private $_navigationItemCount = 10;                //导航栏显示导航总页数
    private $_pageSize = null;                //每页项目数
    private $_align = "right";             //导航栏显示位置
    private $_itemCount = null;                //总项目数
    private $_pageCount = null;                //总页数
    private $_currentPage = null;                //当前页
    private $_front = null;                            //前端控制器
    private $_PageParaName = "pn";              //页面参数名称
    /**
      private $_firstPageString = "|<<";                 //导航栏中第一页显示的字符
      private $_nextPageString = ">>";                   //导航栏中前一页显示的字符
      private $_previousPageString = "<<";               //导航栏中后一页显示的字符
      private $_lastPageString = ">>|";                  //导航栏中最后一页显示的字符
      /* */
    private $_firstPageString = "首页";              //导航栏中第一页显示的字符
    private $_nextPageString = "下一页";            //导航栏中前一页显示的字符
    private $_previousPageString = "前一页";            //导航栏中后一页显示的字符
    private $_lastPageString = "尾页";              //导航栏中最后一页显示的字符
    private $_splitString = "  ";

    //private $_splitString        = " | ";
    //页数字间的间隔符 /

    public function __construct($itemCount, $pageSize) {
        if (!is_numeric($itemCount) || (!is_numeric($pageSize)))
            throw new Exception("Pagination Error:not Number");
        $this->_itemCount = $itemCount;
        $this->_pageSize = $pageSize;
        $this->_front = Zend_Controller_Front::getInstance();

        $this->_pageCount = ceil($itemCount / $pageSize);            //总页数
        $page = $this->_front->getRequest()->getParam($this->_PageParaName);
        if (empty($page) || (!is_numeric($page))) {    //为空或不是数字，设置当前页为1
            $this->_currentPage = 1;
        } else {
            if ($page < 1)
                $page = 1;
            if ($page > $this->_pageCount)
                $page = $this->_pageCount;
            $this->_currentPage = $page;
        }
    }

    /**
     * 返回当前页
     * @param int 当前页
     */
    public function getCurrentPage() {
        return $this->_currentPage;
    }

    /**
     * 返回导航栏目
     * @return string 导航html                class="PageNavigation" 
     */
    public function getNavigation() {
        $navigation = '<div style="float:right;text-align:' . $this->_align . '">';

        $pageCote = ceil($this->_currentPage / ($this->_navigationItemCount - 1)) - 1;    //当前页处于第几栏分页
        $pageCoteCount = ceil($this->_pageCount / ($this->_navigationItemCount - 1));    //总分页栏
        $pageStart = $pageCote * ($this->_navigationItemCount - 1) + 1;                    //分页栏中起始页
        $pageEnd = $pageStart + $this->_navigationItemCount - 1;                        //分页栏中终止页
        if ($this->_pageCount < $pageEnd) {
            $pageEnd = $this->_pageCount;
        }
        //$navigation .= "<div style='float:left; font:14px blod;padding: 5px 0;' >总共：{$this->_itemCount}　{$this->_pageCount}页 </div>";
        if ($this->_currentPage > 0 && $this->_currentPage != 1) {  //if($pageCote > 0)//首页导航
            $navigation .= ' <div style="float:left;margin:0 3px;"><a href="' . $this->createHref(1) . "\" class=\"page_button\">$this->_firstPageString</a></div> ";
        } else {
            $navigation .= ' <div style="float:left;margin:0 3px;"><div class="page_button_unlink">' . $this->_firstPageString . "</div> ";
        }
        if ($this->_currentPage > 0 && $this->_currentPage != 1) {                    //上一页导航
            $navigation .= ' <div style="float:left;margin:0 3px;"><a href="' . $this->createHref($this->_currentPage - 1);
            $navigation .= "\" class=\"page_button\">$this->_previousPageString</a></div> ";
        } else {
            $navigation .= ' <div style="float:left;margin:0 3px;"><div class="page_button_unlink">' . $this->_previousPageString . "</a></div> ";
        }
        while ($pageStart <= $pageEnd && $pageStart >= 0) {          //构造数字导航区
            if ($pageStart == $this->_currentPage) {
                $navigation .= " <div class='page_now'><strong>$pageStart</strong></div> " . $this->_splitString;
            } else {
                $navigation .= ' <a href="' . $this->createHref($pageStart) . "\" class=\"page_link\" >$pageStart</a> " . $this->_splitString;
            }
            $pageStart++;
        }
        if ($this->_currentPage != $this->_pageCount) {    //下一页导航
            $navigation .= ' <div style="float:left;margin:0 3px;"> <a href="' . $this->createHref($this->_currentPage + 1) . "\" class=\"page_button\">$this->_nextPageString</a></div> ";
        } else {
            $navigation .= ' <div style="float:left;margin:0 3px;"> <div  class="page_button_unlink">' . $this->_nextPageString . "</div></div> ";
        }
        if ($this->_currentPage != $this->_pageCount) { //if($pageCote < $pageCoteCount-1)  //未页导航
            $navigation .= ' <div style="float:left;margin:0 3px;"><a href="' . $this->createHref($this->_pageCount) . "\" class=\"page_button\">$this->_lastPageString</a></div> ";
        } else {
            $navigation .= ' <div style="float:left;margin:0 3px;"><div class="page_button_unlink">' . $this->_lastPageString . "</div></div> ";
        }
        //添加直接导航框
        //$navigation .= '<input type="text" size="3" onkeydown="if(event.keyCode==13){window.location=\' ';
        //$navigation .= $this->createHref().'\'+this.value;return false;}" />';
        //2008年8月27号补充输入非正确页码后出现的错误——begin
        if (URL_TYPE == 1) {
            $navigation .= ' <div style="float:left;margin:0 10px;">  <select onchange="window.location=\' ' . $this->createHref() . '\'+this.options[this.selectedIndex].value;"></div> ';
        } elseif (URL_TYPE == 2) {
            $navigation .= ' <div  style="float:left;margin:0 10px;">  <select onchange="window.location=\' ' . $this->createHrefHtml() . '_' . '\'+this.options[this.selectedIndex].value' . '+\'.html\';"></div> ';
        }

        for ($i = 1; $i <= $this->_pageCount; $i++) {
            if ($this->getCurrentPage() == $i) {
                $selected = "selected";
            } else {
                $selected = "";
            }
            $navigation .= '<option value=' . $i . ' ' . $selected . '>' . $i . '</option>';
        }
        $navigation .= '</select>';
        //2008年8月27号补充输入非正确页码后出现的错误——end
        $navigation .= "</div>";
        return $navigation;
    }

    /**
     * 取得导航栏显示导航总页数
     *
     * @return int 导航栏显示导航总页数
     */
    public function getNavigationItemCount() {
        return $this->_navigationItemCount;
    }

    /**
     * 设置导航栏显示导航总页数
     *
     * @param  int $navigationCount:导航栏显示导航总页数
     */
    public function setNavigationItemCoun($navigationCount) {
        if (is_numeric($navigationCount)) {
            $this->_navigationItemCount = $navigationCount;
        }
    }

    /**
     * 设置首页显示字符
     * @param string $firstPageString 首页显示字符
     */
    public function setFirstPageString($firstPageString) {
        $this->_firstPageString = $firstPageString;
    }

    /**
     * 设置上一页导航显示字符
     * @param string $previousPageString:上一页显示字符
     */
    public function setPreviousPageString($previousPageString) {
        $this->_previousPageString = $previousPageString;
    }

    /**
     * 设置下一页导航显示字符
     * @param string $nextPageString:下一页显示字符
     */
    public function setNextPageString($nextPageString) {
        $this->_nextPageString = $nextPageString;
    }

    /**
     * 设置未页导航显示字符
     * @param string $nextPageString:未页显示字符
     */
    public function setLastPageString($lastPageString) {
        $this->_lastPageString = $lastPageString;
    }

    /**
     * 设置导航字符显示位置
     * @param string $align:导航位置
     */
    public function setAlign($align) {
        $align = strtolower($align);
        if ($align == "center") {
            $this->_align = "center";
        } elseif ($align == "right") {
            $this->_align = "right";
        } else {
            $this->_align = "left";
        }
    }

    /**
     * 设置页面参数名称
     * @param string $pageParamName:页面参数名称
     */
    public function setPageParamName($pageParamName) {
        $this->_PageParaName = $pageParamName;
    }

    /**
     * 获取页面参数名称
     * @return string 页面参数名称
     */
    public function getPageParamName() {
        return $this->_PageParaName;
    }

    /**
     * 生成导航链接地址
     * @param int $targetPage:导航页
     * @return string 链接目标地址
     */
    private function createHref($targetPage = null) {
        $params = $this->_front->getRequest()->getParams();
        $module = $params["module"];
        $controller = $params["controller"];
        $action = $params["action"];
        $wd = urlencode($params["wd"]);
        $page = $params["pn"];
        if (URL_TYPE == 1) {
            $targetUrl = $this->_front->getBaseUrl() . "/$controller?wd=" . $wd . "&$this->_PageParaName=" . $targetPage;
            return $targetUrl;
        } elseif (URL_TYPE == 0) {
            $targetUrl = $this->_front->getBaseUrl() . "/$module/$controller/$action";
            foreach ($params as $key => $value) {
                if ($key != "controller" && $key != "module" && $key != "action" && $key != $this->_PageParaName) {
                    $targetUrl .= "/$key/$value";
                }
            }
            if (isset($targetPage))                //指定目标页
                $targetUrl .= "/$this->_PageParaName/$targetPage";
            else
                $targetUrl .= "/$this->_PageParaName/";
            return $targetUrl;
        }
        elseif (URL_TYPE == 2) {
            $targetUrl = $this->_front->getBaseUrl();
            if ($targetUrl != "") {
                $targetUrl .= "_" . $module . "_" . $controller . "_" . $action;
            } else {
                $targetUrl .= $module . "_" . $controller . "_" . $action;
            }

            foreach ($params as $key => $value) {
                if ($key != "controller" && $key != "module" && $key != "action" && $key != $this->_PageParaName) {
                    $targetUrl .= "_" . $key . "_" . $value;
                }
            }
            if (isset($targetPage))                //指定目标页
                $targetUrl .= "_" . $this->_PageParaName . "_" . $targetPage . ".html";
            else
                $targetUrl .= "_" . $this->_PageParaName . ".html";
            return $targetUrl;
        }
    }

    //伪静态=====================================================
    private function createHrefHtml($targetPage = null) {
        $params = $this->_front->getRequest()->getParams();
        $module = $params["module"];
        $controller = $params["controller"];
        $action = $params["action"];
        if (URL_TYPE == 1) {
            $targetUrl = $this->_front->getBaseUrl() . "/$module/$controller/$action";
            foreach ($params as $key => $value) {
                if ($key != "controller" && $key != "module" && $key != "action" && $key != $this->_PageParaName) {
                    $targetUrl .= "/$key/$value";
                }
            }
            if (isset($targetPage))                //指定目标页
                $targetUrl .= "/$this->_PageParaName/$targetPage";
            else
                $targetUrl .= "/$this->_PageParaName/";
            return $targetUrl;
        }
        elseif (URL_TYPE == 2) {
            $targetUrl = $this->_front->getBaseUrl();
            if ($targetUrl != "") {
                $targetUrl .= "_" . $module . "_" . $controller . "_" . $action;
            } else {
                $targetUrl .= $module . "_" . $controller . "_" . $action;
            }

            foreach ($params as $key => $value) {
                if ($key != "controller" && $key != "module" && $key != "action" && $key != $this->_PageParaName) {
                    $targetUrl .= "_" . $key . "_" . $value;
                }
            }
            if (isset($targetPage))                //指定目标页
                $targetUrl .= "_" . $this->_PageParaName . "_" . $targetPage;
            else
                $targetUrl .= "_" . $this->_PageParaName;
            return $targetUrl;
        }
    }

    //伪静态=====================================================
}

?>
