<?php

/**
 * Copyright (c) Jan Pospisil (http://www.jan-pospisil.cz)
 */

namespace JP\TemplateMaker;

/**
 * Helpers
 * @author Jan Pospisil
 */

class Helpers extends \Nette\Object {

	public static function getBaseFilename($filename){
		$parts = explode('.', $filename);
		return substr($filename, 0, strlen($filename) - strlen(end($parts)));
	}

}
