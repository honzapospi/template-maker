<?php

/**
 * Copyright (c) Jan Pospisil (http://www.jan-pospisil.cz)
 */

namespace JP\TemplateMaker;
use Nette\StaticClass;

/**
 * Helpers
 * @author Jan Pospisil
 */

class Helpers {
	use StaticClass;

	public static function getBaseFilename($filename){
		$parts = explode('.', $filename);
		return substr($filename, 0, strlen($filename) - strlen(end($parts)));
	}

}
