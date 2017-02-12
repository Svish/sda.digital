<?php

namespace Data;

class Hash extends Computed
{
	const ALGO = PASSWORD_DEFAULT;
	const ALGO_OPT = [];


	protected function _set(string $key, $value)
	{
		$hash = password_hash($value, self::ALGO, self::ALGO_OPT);
		yield "{$key}_hash" => $hash;
	}

	protected function _unset(string $key)
	{
		yield "{$key}_hash";
	}
}
