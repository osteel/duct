<?php

namespace Osteel\Duct\Sieves;

use Illuminate\Support\Str;

abstract class Sieve
{
	public static function make(string $key, array $options = []): Sieve
	{
		// @TODO check if class exists first
		return new (Str::studly($key))($options);
	}

	// @TODO add interface?
	abstract public function process(): void;
}
