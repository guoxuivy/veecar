<?php
/**
 * CFileLogRoute class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 */

/**
 * CFileLogRoute records log messages in files.
 *
 * The log files are stored under {@link setLogPath logPath} and the file name
 * is specified by {@link setLogFile logFile}. If the size of the log file is
 * greater than {@link setMaxFileSize maxFileSize} (in kilo-bytes), a rotation
 * is performed, which renames the current log file by suffixing the file name
 * with '.1'. All existing log files are moved backwards one place, i.e., '.2'
 * to '.3', '.1' to '.2'. The property {@link setMaxLogFiles maxLogFiles}
 * specifies how many files to be kept.
 *
 * @property string $logPath Directory storing log files. Defaults to application runtime path.
 * @property string $logFile Log file name. Defaults to 'application.log'.
 * @property integer $maxFileSize Maximum log file size in kilo-bytes (KB). Defaults to 1024 (1MB).
 * @property integer $maxLogFiles Number of files used for rotation. Defaults to 5.
 *
 * 移植于YII 1.3 二次开发
 * @author ivy <guoxuivy@gmail.com>
 * @copyright Copyright &copy; 2013-2017 Ivy Software LLC
 * @license http://www.ivyframework.com/license/
 * @package framework
 * @link https://github.com/guoxuivy/ivy 
 * @since 1.0 
 */
namespace Ivy\logging;
use Ivy\core\CComponent;
use Ivy\core\CException;
class CFileLogRoute extends CLogRoute
{
	/**
	 * @var integer maximum log file size
     * 每个日志文件最多1024KB
	 */
	private $_maxFileSize=1024; // in KB
	/**
	 * @var integer number of log files used for rotation
     * 最多保存最近的5个日志文件
	 */
	private $_maxLogFiles=5;
	/**
	 * @var string directory storing log files
	 */
	private $_logPath;
	/**
	 * @var string log file name
	 */
	private $_logFile='application.log';


	/**
	 * Initializes the route.
	 * This method is invoked after the route is created by the route manager.
	 */
	public function init()
	{
		parent::init();
		if($this->getLogPath()===null)
			$this->setLogPath(\Ivy::app()->getRuntimePath());
	}

	/**
	 * @return string directory storing log files. Defaults to application runtime path.
	 */
	public function getLogPath()
	{
		return $this->_logPath;
	}

	/**
	 * @param string $value directory for storing log files.
	 * @throws CException if the path is invalid
	 */
	public function setLogPath($value)
	{
		if (!is_dir($value)){
			@mkdir($value,0777,true);
		}
		$this->_logPath=realpath($value);
		if($this->_logPath===false || !is_dir($this->_logPath) || !is_writable($this->_logPath))
			throw new CException('CFileLogRoute.logPath "'.$value.'" does not point to a valid directory. Make sure the directory exists and is writable by the Web server process.');
	}

	/**
	 * @return string log file name. Defaults to 'application.log'.
	 */
	public function getLogFile()
	{
		return $this->_logFile;
	}

	/**
	 * @param string $value log file name
	 */
	public function setLogFile($value)
	{
		$this->_logFile=$value;
	}

	/**
	 * @return integer maximum log file size in kilo-bytes (KB). Defaults to 1024 (1MB).
	 */
	public function getMaxFileSize()
	{
		return $this->_maxFileSize;
	}

	/**
	 * @param integer $value maximum log file size in kilo-bytes (KB).
	 */
	public function setMaxFileSize($value)
	{
		if(($this->_maxFileSize=(int)$value)<1)
			$this->_maxFileSize=1;
	}

	/**
	 * @return integer number of files used for rotation. Defaults to 5.
	 */
	public function getMaxLogFiles()
	{
		return $this->_maxLogFiles;
	}

	/**
	 * @param integer $value number of files used for rotation.
	 */
	public function setMaxLogFiles($value)
	{
		if(($this->_maxLogFiles=(int)$value)<1)
			$this->_maxLogFiles=1;
	}

	/**
	 * Saves log messages in files.
	 * @param array $logs list of log messages
	 */
	protected function processLogs($logs)
	{
		$logFile=$this->getLogPath().DIRECTORY_SEPARATOR.$this->getLogFile();
		if(@filesize($logFile)>$this->getMaxFileSize()*1024)
			$this->rotateFiles();
		$fp=@fopen($logFile,'a');
		@flock($fp,LOCK_EX);
		foreach($logs as $log)
			@fwrite($fp,$this->formatLogMessage($log[0],$log[1],$log[2],$log[3]));
		@flock($fp,LOCK_UN);
		@fclose($fp);
	}

	/**
	 * Rotates log files.
     * 轮转日志文件
	 */
	protected function rotateFiles()
	{
		$file=$this->getLogPath().DIRECTORY_SEPARATOR.$this->getLogFile();
		$max=$this->getMaxLogFiles();
		for($i=$max;$i>0;--$i)
		{
			$rotateFile=$file.'.'.$i;
			if(is_file($rotateFile))
			{
				// suppress errors because it's possible multiple processes enter into this section
				if($i===$max)
					@unlink($rotateFile);
				else
					@rename($rotateFile,$file.'.'.($i+1));
			}
		}
		if(is_file($file))
			@rename($file,$file.'.1'); // suppress errors because it's possible multiple processes enter into this section
	}
}