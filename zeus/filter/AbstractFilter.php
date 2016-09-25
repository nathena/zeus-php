<?php
namespace zeus\filter;

abstract class AbstractFilter implements FilterInterface
{
	protected $nextFilter = null;
	
	public function __construct(FilterInterface $nextFilter = null)
	{
		$this->nextFilter = $nextFilter;
	}
	
	public final function doFilter($data)
	{
		$data = $this->doChain($data);
		
		if( !is_null($this->nextFilter) )
		{
			return $this->nextFilter->doFilter($data);
		}
		return $data;
	}
	
	public function getNext()
	{
		return $this->nextFilter;
	}
	
	public function setNext(FilterInterface $filter)
	{
		$this->nextFilter = $filter;
	}
	
	protected abstract function doChain($data);
}