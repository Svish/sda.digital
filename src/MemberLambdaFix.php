<?php

trait MemberLambdaFix
{
	public function __call($method, $args)
	{
		if(is_callable($this->$method))
			return call_user_func_array($this->$method, $args);
	}
}
