<?php

/**
 * @author        YangHuan
 * @datetime    
 * @version        1.0.0
 */

/**
 * Short descrīption.
 *
 * Detail descrīption
 * @author       
 * @version      1.0
 * @copyright    
 * @access       public
 */
class Custom_Controller_Plugin_MenuTree {

    /**
     * Descrīption
     * @var       
     * @since     1.0
     * @access    private
     */
    private $data = array();

    /**
     * Descrīption
     * @var       
     * @since     1.0
     * @access    private
     */
    private $child = array(-1 => array());

    /**
     * Descrīption
     * @var       
     * @since     1.0
     * @access    private
     */
    private $layer = array(-1 => -1);

    /**
     * Descrīption
     * @var       
     * @since     1.0
     * @access    private
     */
    private $parent = array();

    /**
     * 标签分隔符 数组
     *
     * @var array
     */
    //private $levelmark = array();

    /**
     * Short descrīption. 
     *
     * Detail descrīption
     * @param      none
     * @global     none
     * @since      1.0
     * @access     private
     * @return     void
     * @update     date time
     */
    public function __construct($value) {
        $this->setNode(0, -1, $value);
    }

// end func

    /**
     * Short descrīption. 
     *
     * Detail descrīption
     * @param      none
     * @global     none
     * @since      1.0
     * @access     private
     * @return     void
     * @update     date time
     */
    public function setNode($id, $parent, $value) {
        $parent = $parent ? $parent : 0;

        $this->data[$id] = $value;
        $this->child[$id] = array();
        $this->child[$parent][] = $id;
        $this->parent[$id] = $parent;

        if (!isset($this->layer[$parent])) {
            $this->layer[$id] = 0;
        } else {
            $this->layer[$id] = $this->layer[$parent] + 1;
        }
    }

// end func

    /**
     * Short descrīption. 
     *
     * Detail descrīption
     * @param      none
     * @global     none 
     * @since      1.0
     * @access     private
     * @return     void
     * @update     date time
     */
    public function getList(&$tree, $root = 0) {
        foreach ($this->child[$root] as $key => $id) {
            $tree[] = $id;

            if ($this->child[$id])
                $this->getList($tree, $id);
        }
    }

// end func

    /**
     * Short descrīption. 
     *
     * Detail descrīption
     * @param      none
     * @global     none
     * @since      1.0
     * @access     private
     * @return     void
     * @update     date time
     */
    public function getValue($id) {
        return $this->data[$id];
    }

// end func

    /**
     * 获取分类符号
     *
     * @param int $id
     * @param int $level
     * @return string
     */
    public function getLevelMark($id, $level) {
        $result = "";
        $num = count($level); //分类层次
        if ($id > 0) {
            for ($a = 0; $a < ($num - 1); $a++) {
                if ($level[$a] == 0) {
                    $result .= "│&nbsp;&nbsp;&nbsp;";
                    //$result .=  "┃&nbsp;&nbsp;&nbsp;";
                } else {
                    $result .= "&nbsp;&nbsp;&nbsp;";
                }
            }
            $endID = $num - 1;
            if ($level[$endID] == 1) {
                return $result . "└ ";
                //return $result."┗";
            } else {
                return $result . "├ ";
                //return $result."┣";
            }
        } else {
            return "";
        }
    }

    /**
     * Short descrīption. 
     *
     * Detail descrīption
     * @param      none
     * @global     none
     * @since      1.0
     * @access     private
     * @return     void
     * @update     date time
     */
    public function getLayer($id, $isParentEndMenu, $isEndMenu) {
        if ($this->layer[$id] >= 1) {
            $nr = '';
            if ($isParentEndMenu == 1) {
                for ($a == 1; $a < ($this->layer[$id] - 2); $a++) {
                    $nr .= "│&nbsp;&nbsp;&nbsp;";
                }
                $nr .= "&nbsp;&nbsp;&nbsp;" . $this->layer[$id];
            } else {
                for ($a == 1; $a < ($this->layer[$id] - 1); $a++) {
                    $nr .= "│&nbsp;&nbsp;&nbsp;" . $this->layer[$id];
                }
            }

            if ($isEndMenu) {
                return $nr . "└";
            } else {
                return $nr . "├";
            }

            //return str_repeat("|&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;|-", 1);
        } else {
            return "";
        }
    }

// end func

    /**
     * Short descrīption. 
     *
     * Detail descrīption
     * @param      none
     * @global     none
     * @since      1.0
     * @access     private
     * @return     void
     * @update     date time
     */
    public function getParent($id) {
        return $this->parent[$id];
    }

// end func

    /**
     * Short descrīption. 
     *
     * Detail descrīption
     * @param      none
     * @global     none
     * @since      1.0
     * @access     private
     * @return     void
     * @update     date time
     */
    public function getParents($id) {
        while ($this->parent[$id] != -1) {
            $id = $parent[$this->layer[$id]] = $this->parent[$id];
        }

        ksort($parent);
        reset($parent);

        return $parent;
    }

// end func

    /**
     * Short descrīption. 
     *
     * Detail descrīption
     * @param      none
     * @global     none
     * @since      1.0
     * @access     private
     * @return     void
     * @update     date time
     */
    public function getChild($id) {
        return $this->child[$id];
    }

// end func

    /**
     * Short descrīption. 
     *
     * Detail descrīption
     * @param      none
     * @global     none
     * @since      1.0
     * @access     private
     * @return     void
     * @update     date time
     */
    public function getChilds($id = 0) {
        $child = array($id);
        $this->getList($child, $id);

        return $child;
    }

// end func
}

// end class
