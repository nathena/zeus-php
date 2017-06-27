<?php
namespace zeus\sandbox;

class Autoloader
{
	/**
	 * Array of available namespaces prefixes.
	 * @var array
	 */
	private $prefixes = array();
	/**
	 * Class map array.
	 * @var array
	 */
	private $classmap = array();
	
	/**
	 * Class file path array
	 * @var array
	 */
	private $_includeDir = array();
	
	public function __construct()
	{
		spl_autoload_register($this, true, true);
	}
	
	public function registerNamespaces($namespace, $directory)
	{
        $directory = realpath($directory);
		$this->prefixes[$namespace] = $directory;
		$this->registerDirs([$directory]);
		
		return $this;
	}
	
	public function registerDirs(array $dirs)
	{
		$this->_includeDir = array_unique(array_merge($this->_includeDir,$dirs));
		
		return $this;
	}
	
	public function registerClassMap($class,$map = '')
	{
		if( is_array($class))
		{
			$this->classmap = array_merge($this->classmap, $class);
		}
		else 
		{
			$this->classmap[$class] = $map;
		}
	
		return $this;
	}
	
	public function __invoke($class)
	{
		if( class_exists($class,false) || interface_exists($class, false))
		{
			return true;
		}

		$classFile = $this->findClassFileByClassMap($class);
		//echo '1=>'.$classFile.':'.$class.'<br>';
		if( !is_null($classFile) && !empty($classFile) )
		{echo 2;
			include_once $classFile;
			return true;
		}
		
		$classFile = $this->findClassByNamespace($class);
		//echo '2=>'.$classFile.':'.$class.'<br>';
		if( !is_null($classFile) && !empty($classFile) )
		{
			include_once $classFile;
			return true;
		}
		
		$classFile = $this->findClassByIncludeDir($class);
		//echo '3=>'.$classFile.':'.$class.'<br>';
		if( !is_null($classFile) && !empty($classFile) )
		{
			include_once $classFile;
			return true;
		}
		
		//echo '4=><br>';
		return false;
	}
	
	protected function findClassFileByClassMap($class)
	{
		if (array_key_exists($class, $this->classmap))
		{
			$_classFile =  $this->classmap[$class];
			if( file_exists($_classFile) )
			{
				return $_classFile;
			}
		}
		
		return '';
	}
		
	protected function findClassByNamespace($class)
	{
        $class = trim($class,'\\');
		foreach( $this->prefixes as $ns => $dir )
		{
			if( ($index = strpos($class,$ns))!==false )
			{
                $class = str_replace($ns,'',$class);
                $class = trim($class,'\\');
                $class = str_replace('\\',DIRECTORY_SEPARATOR,$class);

				$_classFile = $dir.DIRECTORY_SEPARATOR.$class.'.php';
				//register namespace
				if( file_exists($_classFile) )
				{
					return $_classFile;
				}
			}
		}
		return '';
	}
	
	protected function findClassByIncludeDir($class)
	{
        $class = trim($class,'\\');
        $class = str_replace('\\',DIRECTORY_SEPARATOR,$class);
		foreach( $this->_includeDir as $dir)
		{
			$_classFile = $dir.DIRECTORY_SEPARATOR.$class.'.php';
			if( file_exists($_classFile) )
			{
				return $_classFile;
			}
		}
		
		return '';
	}
}