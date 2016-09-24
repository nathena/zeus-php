<?php
namespace zeus\loader;

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
	
	private static $_instance = null;
	
	private function __construct()
	{
		spl_autoload_register($this, true, true);
	}
	
	public static function getInstance()
	{
		if(is_null(self::$_instance))
		{
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	public function register($namespace, $directory)
	{
		$this->prefixes[$namespace] = realpath($directory);
		
		return $this;
	}
	
	public function loadClassMap($classmap)
	{
		if (file_exists($classmap))
		{
			$newClassMap = include $classmap;
		
			if (count($this->classmap) > 0)
			{
				$ary = array_merge($this->classmap, $newClassMap);
			}
			else
			{
				$this->classmap = $newClassMap;
			}
		}
	
		return $this;
	}
	
	public function addClassMap($class,$map)
	{
		$this->classmap[$class] = $map;
		
		return $this;
	}
	
	public function __invoke($class)
	{
		if( class_exists($class,false) || interface_exists($class, false))
		{
			return true;
		}
		
		$classFile = $this->findClassFile($class);
		if( !is_null($classFile) && !empty($classFile) )
		{
			include_once $classFile;
			return true;
		}
		return false;
	}
	
	protected function findClassFile($class)
	{
		return $this->findClassFileByClassMap($class);
	}
	
	private function findClassFileByClassMap($class)
	{
		if (array_key_exists($class, $this->classmap))
		{
			$_classFile =  $this->classmap[$class];
			if( file_exists($_classFile) )
			{
				return $_classFile;
			}
		}
		
		$sep = (strpos($class, '\\') !== false) ? '\\' : '_';
		$classNameFragment = explode($sep, $class);
		
		return $this->findClassByNamespace($classNameFragment);
	}
		
	private function findClassByNamespace($classNameFragment)
	{
		$namespace_prefix = $classNameFragment[0];
		foreach( $this->prefixes as $ns => $dir )
		{
			if( $ns == $namespace_prefix )
			{
				$_classNameFragment = array_slice($classNameFragment,1);
				$_classFile = $dir.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $_classNameFragment).'.php';
				//register namespace
				if( file_exists($_classFile) )
				{
					return $_classFile;
				}
			}
		}
		return $this->findClassByLibrary($classNameFragment);
	}
	
	private function findClassByLibrary($classNameFragment)
	{
		//library
		$_classFile = ZEUS_PATH.DS.'library'.DS.implode(DIRECTORY_SEPARATOR, $classNameFragment).'.php';
		if( file_exists($_classFile) )
		{
			return $_classFile;
		}
		return $this->findClassByZeusPath($classNameFragment);
	}
	
	private function findClassByZeusPath($classNameFragment)
	{
		$_classFile = ZEUS_PATH.DS.implode(DIRECTORY_SEPARATOR, $classNameFragment).'.php';
		if( file_exists($_classFile) )
		{
			return $_classFile;
		}
		return "";
	}
}