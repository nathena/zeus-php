<?php 


require_once '../zeus/bootstrap.php';

class a
{
	protected $a = array('a'=>"aaa");
	protected $b = array('a'=>"bbb");
	protected $c = array('a'=>"ccc");
	
	public function __construct($a=NULL,$b=NULL,$c=NULL)
	{
		if(!is_null($a)) 
		{
			$this->a = $a;
		}
		
		if(!is_null($b))
		{
			$this->b = $b;
		}
		
		if(!is_null($c))
		{
			$this->c = $c;
		}
	}
	
	public function test()
	{
		print_r(get_object_vars($this));
	}
	
	public function m($b)
	{
		$vars = get_object_vars($this);
		foreach( $vars as $key => $var )
		{
			if( method_exists($b, $key) )
			{
				$this->$key = array_merge($this->$key, $b->$key());
			}
		}
	}
	
	public function a()
	{
		return $this->a;
	}
	
	protected function b()
	{
		return $this->b;
	}
}

$b1 = new a();
$b2 = new a(["aa"=>1],["bbbbb"=>2],["aa"=>3]);

$b1->m($b2);
$b1->test();
$b2->test();