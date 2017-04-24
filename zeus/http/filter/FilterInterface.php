<?php
namespace zeus\http\filter;

interface FilterInterface
{
	public function doFilter($data);
}