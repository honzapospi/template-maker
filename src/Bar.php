<?php

/**
 * Copyright (c) Jan Pospisil (http://www.jan-pospisil.cz)
 */

namespace JP\TemplateMaker;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Nette\Utils\ArrayHash;
use Nette\Utils\Html;
use Tracy\Debugger;
use Tracy\IBarPanel;

/**
 * Bar
 * @author Jan Pospisil
 */

class Bar extends \Nette\Object implements IBarPanel {

	private $controls = array();
	private $presenter;
	private $templateMaker;

	public function __construct(){
		Debugger::getBar()->addPanel($this);
	}

	public function setTemplateMaker(TemplateMaker $templateMaker){
		$this->templateMaker = $templateMaker;
	}

	public function addControl(Control $control, $templateFilename){
		if(Debugger::$productionMode)
			return;
		$this->controls[$templateFilename] = ArrayHash::from(array(
			'control' => $control,
			'templateFile' => $templateFilename
		));
		if($control instanceof Presenter)
			$this->presenter = $control;
	}

	function getTab() {
		return 'Controls';
	}

	function getPanel() {
		$table = Html::el('table');
		$tr = Html::el('tr');
		$tr->addHtml(Html::el('th')->setHtml('name'));
		$tr->addHtml(Html::el('th')->setHtml('class'));
		$tr->addHtml(Html::el('th')->setHtml('latte'));
		$tr->addHtml(Html::el('th')->setHtml('less'));
		$tr->addHtml(Html::el('th')->setHtml('js'));
		if($this->templateMaker)
			$tr->addHtml(Html::el('th')->setHtml('action'));
		$table->addHtml($tr);
		foreach($this->controls as $control){
			$tr = Html::el('tr');
			$tr->addHtml(Html::el('td')->setHtml($control->control->getName()));
			$tr->addHtml(Html::el('td')->addHtml(Html::el('a')->setAttribute('href', \Tracy\Helpers::editorUri($this->findFile($control->control)))->setHtml(get_class($control->control))));
			$this->getTemplateFiles($control->control, $control->templateFile, $tr);
			$table->addHtml($tr);
		}
		return $table;
	}

	private function findFile(Control $control){
		$rc = new \ReflectionClass($control);
		return $rc->getFileName();
	}

	private function getTemplateFiles(Control $control, $templateFile, Html $tr){
		$files = array('latte', 'less', 'js');
		$baseFileName = Helpers::getBaseFilename($templateFile);
		$exist = true;
		$key =self::createKey($control);
		foreach($files as $ext){
			if(file_exists($baseFileName.$ext)){
				$file = Html::el('a')->setHtml($ext)->setAttribute('href', \Tracy\Helpers::editorUri($baseFileName.$ext));
			} else {
				$exist = false;
				$file = Html::el('span');
				$text = Html::el('span')->setHtml($ext.' ');
				$file->addHtml($text);
				if($this->templateMaker){
					$link = Html::el('a')->setHtml('(create)')->setAttribute('href', $this->presenter->link('this', array(
						'__create_template' => $key,
						'__create_template_file' => $ext
					)));
					$file->addHtml($link);
				}
			}
			$tr->addHtml(Html::el('td')->addHtml($file));
		}
		if($this->templateMaker){
			$action = Html::el('td');
			$tr->addHtml($action);
			if(!$exist){
				$action->addHtml(Html::el('a')->setHtml('create all')->setAttribute('href', $this->presenter->link('this', array(
					'__create_template' => $key,
					'__create_template_file' => 'all'
				))));
			}
		}
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
			} elseif(!$control instanceof Presenter){
				$key[] = $part;
			}
		}
		$key = implode('-',$key);
		if($control instanceof Presenter)
			$key .= '.' . $control->view;
		return $key;
	}
}
