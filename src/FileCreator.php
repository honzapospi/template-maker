<?php

/**
 * Copyright (c) Jan Pospisil (http://www.jan-pospisil.cz)
 */

namespace JP\TemplateMaker;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Nette\Utils\Html;
use Nette\SmartObject;

/**
 * TemplateMaker
 * @author Jan Pospisil
 */

class FileCreator {
	use SmartObject;

	private $createFiles = array('latte', 'less', 'js');
	public $onCreateTemplate;

	public function createTemplate($baseName, $key, $ext = 'all'){
		$baseName .= '.';
		$isControl = substr($key, -7) == 'Control';
		if($ext == 'all'){
			$files = $this->createFiles;
		} else {
			$files = array($ext);
		}
		if(in_array('latte', $files)){
			$latte = $baseName.'latte';
			if(!$isControl){
				$div = Html::el('div')->addAttributes(array('id' => $key));
				self::createFile($latte, '{block #content}'."\n".$div->startTag().'{*** NEVER CHANGE GENERATED ID ***}'."\n\n".$div->endTag());
			} else {
				$div = Html::el('div')->addAttributes(array('class' => $key));
				self::createFile($latte, $div->startTag().'{*** NEVER CHANGE GENERATED CLASSNAME ***}'."\n\n".$div->endTag());
			}
		}
		if(in_array('less', $files)){
			$less = $baseName.'less';
			if(!$isControl){
				self::createFile($less, '#'.$key.'{ // NEVER CHANGE GENERATED ID'."\n\n}");
			} else {
				self::createFile($less, '.'.$key.'{ // NEVER CHANGE GENERATED CLASSNAME'."\n\n}");
			}
		}
		if(in_array('js', $files)){
			$js = $baseName.'js';
			if(!$isControl){
				self::createFile($js, "//$('<selector>', $('#".$key."'));");
			} else {
				self::createFile($js, "//$('<selector>',$('.".$key."'));");
			}
		}
		$this->onCreateTemplate($this, $baseName);
	}



	private static function createFile($filename, $contents){
		$dir = dirname($filename);
		if(!is_dir($dir)){
			@mkdir($dir, 0777, true);
		}
		if(!is_dir($dir)){
			throw new FileCreationException('Unable to create directory "'.$dir.'"');
		}
		@file_put_contents($filename, $contents);
		if(!file_exists($filename))
			throw new FileCreationException('Unable to create file "'.$filename.'"');
	}


}
