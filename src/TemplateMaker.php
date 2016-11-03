<?php

/**
 * Copyright (c) Jan Pospisil (http://www.jan-pospisil.cz)
 */

namespace JP\TemplateMaker;
use JP\Composition\UI\ITemplateControl;
use Nette\Application\UI\Control;
use Nette\Http\Request;

/**
 * TemplateMaker
 * @author Jan Pospisil
 */

class TemplateMaker extends \Nette\Object {

	private $httpRequest;
	private $fileCreator;
	private $templateControl;
	const KEY_CLASS = '__create_template';
	const KEY_FILE = '__create_template_file';

	public function __construct(Request $request, FileCreator $fileCreator, ITemplateControl $templateControl){
		$this->httpRequest = $request;
		$this->fileCreator = $fileCreator;
		$this->templateControl = $templateControl;
	}

	public function check(Control $control, $templateFilename){
		$class = get_class($control);
		$getClass = $this->httpRequest->getUrl()->getQueryParameter(self::KEY_CLASS);
		if($getClass == $class){
			$this->fileCreator->createTemplate($templateFilename, $control, $this->httpRequest->getUrl()->getQueryParameter(self::KEY_FILE));
			$control->redirect('this');
		}
	}

}
