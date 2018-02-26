<?php
/**
 * Bingo2.0视图
 * @author xuliqiang <xuliqiang@baidu.com>
 * @since 2010-04-25
 * @package bingo
 */
require_once 'Bingo/View/Script.php';
class Bingo_View
{
    /**
     * 根路径
     * @var string
     */
	protected $_strBaseDir = '.';
	/**
	 * 输出类型，html/json/xml
	 * TODO 根据该类型，自动输出不同的头信息
	 * @var string
	 */
	protected $_strOutputType = 'html';
	/**
	 * view文件夹的路径名称
	 * @var string
	 */
	protected $_strScriptPathName = 'control';
	/**
	 * View文件夹路径，根据输出类型进行一一对应。
	 * @var array
	 */
	protected $_arrScriptPaths = array();
	/**
	 * 默认视图处理器，当找不到视图处理器的时候，就采用默认的。
	 * @var string
	 */
	protected $_strDefaultView = 'index.php';
	/**
	 * 试图初始化对应的文件。
	 * @var string
	 */
	protected $_strInitView = '__init.php';
	/**
	 * 错误号，UI传递给FE
	 * @var int
	 */
	protected $_intErrno = 0;
	/**
	 * 是否开启了debug模式，在开启debug模式的时候，不会进行页面渲染，而是直接进行数据的var_dump
	 * @var boolean
	 */
	protected $_bolDebug = false;	
	
	protected $_objScript = null;
	
	public function __construct($arrConfig=array())
	{
		if (! empty($arrConfig)){
			$this->setOptions($arrConfig);
		}
		$this->_objScript = Bingo_View_Script::getInstance();
		$this->_objScript->setBaseDir($this->_strBaseDir . DIRECTORY_SEPARATOR . $this->_strOutputType);
	}
	/**
	 * 设置相关配置信息
	 * @param array $arrConfig
	 * {
	 * 		baseDir : 根目录
	 * 		defaultView ： 默认View处理器，默认是index.php
	 * 		scriptPathName : script文件夹存放的文件夹名称。默认是script
	 * 		outputType : 输出类型。默认是html
	 * 		initView : 初始化View的文件。默认是__init.php
	 * 		debug : 是否启用debug模式
	 * }
	 */
	public function setOptions($arrConfig=array())
	{
		if (isset($arrConfig['baseDir'])) {
    		$this->setBaseDir($arrConfig['baseDir']);
    	}
    	if (isset($arrConfig['defaultView'])) {
    		$this->_strDefaultView = $arrConfig['defaultView'];
    	}
    	if (isset($arrConfig['scriptPathName'])) {
    		$this->_strScriptPathName = $arrConfig['scriptPathName'];
    	}
    	if (isset($arrConfig['outputType'])) {
    		$this->_strOutputType = $arrConfig['outputType'];
    	}
    	if (isset($arrConfig['initView'])) {
    		$this->_strInitView = $arrConfig['initView'];
    	}
    	if (array_key_exists('debug', $arrConfig)) {
    		$this->_bolDebug = (boolean) $arrConfig['debug'];
    	}
	}
	/*
	 * 设置根目录。注意，在FE中使用到目录的时候，都会自动加上该目录，需要使用绝对路径。
	 */
	public function setBaseDir($strBaseDir)
	{
		if (is_dir($strBaseDir) && file_exists($strBaseDir)) {
			$this->_strBaseDir = rtrim($strBaseDir, DIRECTORY_SEPARATOR);
		} else {
			trigger_error('setBaseDir baseDir invalid!baseDir=' . $strBaseDir, E_USER_WARNING);
		}
		return false;
	}
	/**
	 * 设置输出类型，默认是HTML，传递给view使用
	 * @param string $strOutputType
	 */
	public function setOutputType($strOutputType)
	{
		$this->_strOutputType = $strOutputType;
	}
	/**
	 * 添加输出类型对应的目录结构
	 * @param string $strPath
	 * @param string $strOutputType
	 */
	public function setScriptPath($strPath, $strOutputType = 'html')
	{
		if (! is_dir($strPath)) {
    		trigger_error('setScriptPath path invalid!' . $strPath, E_USER_WARNING);
    	} 
    	$this->_arrScriptPaths[$strType] = rtrim($strPath, DIRECTORY_SEPARATOR);
	}
	/**
	 * 获取当前Script脚本的根目录
	 */
	public function getScriptPath()
    {
    	$strPath = '';
    	if (isset($this->_arrScriptPaths[$this->_strOutputType])) {
    		$strPath = $this->_arrScriptPaths[$this->_strOutputType];
    	} else {
    		$strPath = $this->_strBaseDir  .DIRECTORY_SEPARATOR . $this->_strOutputType . DIRECTORY_SEPARATOR . $this->_strScriptPathName;
    	}
    	if (! is_dir($strPath)) {
    		throw new Exception($strPath . ' invalid!');
    	}
    	return $strPath;
    }
	/**
	 * 转向视图层，进行渲染
	 * @param string $strViewName
	 */
	public function render($strViewName)
	{
		set_error_handler(array($this->_objScript, 'errorHandler'));
    	$bolRet = false;
    	try{
    		$bolRet = $this->_render($strViewName);
    	} catch (Exception $e) {
    		$this->_objScript->errorHandler(E_USER_WARNING, $e->getMessage(), $e->getFile(), $e->getLine());
    	}
    	restore_error_handler();
    	return $bolRet;
	}
	/***
	 * 进行模板的渲染，适合在不采用view层的时候调用
	 */
	public function display($strTemplateName)
	{
		set_error_handler(array($this->_objScript, 'errorHandler'));
    	$bolRet = false;
    	try{
    		require_once 'Bingo/View/Functions.php';
    		$this->_objScript->display($strTemplateName);
    	} catch (Exception $e) {
    		$this->_objScript->errorHandler(E_USER_WARNING, $e->getMessage(), $e->getFile(), $e->getLine());
    	}
    	restore_error_handler();
    	return $bolRet;
	}
	/**
	 * 模板变量赋值
	 * @param mix(string|array) $mixKey
	 * @param mix(array|string) $mixValue
	 */
	public function assign($mixKey, $mixValue = null)
	{
		return $this->_objScript->assign($mixKey, $mixValue);
	}
	public function getScript()
	{
		return $this->_objScript;
	}
	/**
	 * 清空变量
	 */
	public function clean()
	{
		$this->_objScript->clean();
	}
	/**
	 * 开启debug模式
	 * @param boolean $bolDebug
	 */
	public function setDebug($bolDebug = true)
	{
		$this->_bolDebug = (bool)$bolDebug;
	}
	/**
	 * 传递错误信息给view
	 * @param int $intErrno
	 */
	public function error($intErrno)
	{
		$this->_intErrno = intval($intErrno);
		$this->_objScript->setErrno($intErrno);
	}
	
	protected function _debugOutput($strViewName, $strViewPath, $strInitViewPath)
    {
    	echo '<b>基础参数</b><br/>';
    	echo '根目录（baseDir） : ' . $this->_strBaseDir . '<br />';
    	echo '输出类型（outputType） : ' . $this->_strOutputType . '<br />';
    	echo '初始化视图文件：' . $strInitViewPath . '<br />';
    	echo '视图文件名称：' . $strViewName . '<br />';
    	echo '视图文件路径：' . $strViewPath . '<br />';
    	echo '错误号：' . $this->_intErrno . '<br />';
    	echo '<hr><b>数据字典</b></hr><br/>';
    	echo '<pre>';
    	print_r($this->_objScript->g());
    	echo '</pre>';	
    }
    
    protected function _render($strViewName)
    {
    	//包含视图需要的函数库
    	require_once 'Bingo/View/Functions.php';
    	$strViewRootPath = $this->getScriptPath() . DIRECTORY_SEPARATOR;
    	$strViewFilePath = $strViewRootPath . $strViewName;
    	
    	if (! is_file($strViewFilePath)) {
    		$strViewFilePath = $strViewRootPath . $this->_strDefaultView;
    	}  	
    	$strInitViewPath = $strViewRootPath . $this->_strInitView;  
    	if (! is_file($strInitViewPath)) {
    		$strInitViewPath = '';
    	}
    	if ($this->_bolDebug) {
    		$this->_debugOutput($strViewName, $strViewFilePath, $strInitViewPath);
    		return true;
    	}
    	$bolRet = $this->_objScript->render($strViewFilePath, $strInitViewPath);
    	if (! $bolRet) {
    		trigger_error('Bingo_View::render ' . $strViewName . ' ret=' . intval($bolRet), E_USER_WARNING);
    	}
    	return $bolRet;
    }
}