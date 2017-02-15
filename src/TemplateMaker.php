<?php

/**
 * Copyright (c) Jan Pospisil (http://www.jan-pospisil.cz)
 */

namespace JP\TemplateMaker;
use JP\Composition\UI\ITemplateControl;
use JP\Composition\UI\Presenter;
use Nette\Application\UI\Control;
use Nette\Http\Request;
use Nette\Utils\Strings;

/**
 * TemplateMaker
 * @author Jan Pospisil
 */

class TemplateMaker extends \Nette\Object {

	private $httpRequest;
	private $fileCreator;
	private $templateControl;
	const KEY_NAME = '__create_template';
	const KEY_FILE = '__create_template_file';
	private $templateDirectory;

	public function __construct($templateDirectory, Request $request, FileCreator $fileCreator, ITemplateControl $templateControl){
		$this->templateDirectory = $templateDirectory;
		$this->httpRequest = $request;
		$this->fileCreator = $fileCreator;
		$this->templateControl = $templateControl;
	}

	public function createFiles(Presenter $presenter){
		$name = $this->httpRequest->getUrl()->getQueryParameter(self::KEY_NAME);
		$filename = $this->templateDirectory.'/'.strtr($name, array('-' => '/'));
		if($name){
			$name = strtr($name, array('.' => '_'));
			$this->fileCreator->createTemplate($filename, $name, $this->httpRequest->getUrl()->getQueryParameter(self::KEY_FILE));
			$presenter->redirect('this');
		}
	}
}
