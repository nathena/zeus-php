<?php
namespace zeus\http\filter;

class DefaultFilter extends AbstractFilter
{
	public function __construct(FilterInterface $nextFilter = null)
	{
		parent::__construct($nextFilter);
	}
	
	protected function doChain($data)
	{
		return $data;
	}
}