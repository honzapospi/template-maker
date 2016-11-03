<?php

/**
 * Copyright (c) Jan Pospisil (http://www.jan-pospisil.cz)
 */

namespace JP\TemplateMaker;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Nette\Utils\Html;

/**
 * TemplateMaker
 * @author Jan Pospisil
 */

class FileCreator extends \Nette\Object {

	private $createFiles = array('latte', 'less', 'js');
	public $onCreateTemplate;

	public function createTemplate($filename, Control $control, $ext = 'all'){
		$baseName = Helpers::getBaseFilename($filename);
		$key = self::createKey($control);
		if($ext == 'all'){
			$files = $this->createFiles;
		} else {
			$files = array($ext);
		}
		if(in_array('latte', $files)){
			$latte = $baseName.'latte';
			if($control instanceof Presenter){
				$div = Html::el('div')->addAttributes(array('id' => $key));
				self::createFile($latte, '{block #content}'."\n".$div->startTag().'{*** NEVER CHANGE GENERATED ID ***}'."\n\n".$div->endTag());
			} else {
				$div = Html::el('div')->addAttributes(array('class' => $key));
				self::createFile($latte, $div->startTag().'{*** NEVER CHANGE GENERATED CLASSNAME ***}'."\n\n".$div->endTag());
			}
		}
		if(in_array('less', $files)){
			$less = $baseName.'less';
			if($control instanceof Presenter){
				self::createFile($less, '#'.$key.'{ // NEVER CHANGE GENERATED ID'."\n\n}");
			} else {
				self::createFile($less, '.'.$key.'{ // NEVER CHANGE GENERATED CLASSNAME'."\n\n}");
			}
		}
		if(in_array('js', $files)){
			$js = $baseName.'js';
			if($control instanceof Presenter){
				self::createFile($js, "//$('<selector>', $('#".$key."'));");
			} else {
				self::createFile($js, "//$('<selector>',$('.".$key."'));");
			}
		}
		$this->onCreateTemplate($this, $filename);
	}

	private static function createKey(Control $control){
		$parts = explode('\\', get_class($control));
		array_shift($parts);
		$key = array();
		foreach($parts as $part){
			if(substr($part, -6) == 'Module'){
				$key[] = $part;
			} elseif(substr($part, -9) == 'Presenter'){
				$key[] = $part;
				break;
			}
		}
		$key = implode('-',$key);
		if($control instanceof Presenter)
			$key .= '-' . $control->view;
		return $key;
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
