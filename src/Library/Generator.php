<?php

class Generator {
	private $_script;
	private $_slider;
	
	public function __construct() {
		$this->_script = array();
	}
	
	public function run() {

		$handle = opendir(TEMPLATE_SCRIPT);
		while($filename = readdir($handle))
			if('.' != $filename && '..' != $filename) {
				require TEMPLATE_SCRIPT . $filename;

				$class_name = preg_replace('/.php$/', '', $filename);
				$this->_script[$class_name] = new $class_name;
			}
		closedir($handle);

		foreach((array)$this->_script as $class)
			$class->sortList();
		
		$this->genSlider();
		$this->genContainer();
	}

	/**
	 * Gen Container
	 */
	private function genContainer() {
		foreach((array)$this->_script as $class)
			$class->gen($this->_slider);
	}

	/**
	 * Gen Slider
	 */
	private function genSlider() {
		$result = '';
		$list = array();
		$handle = opendir(THEME_SLIDER);
		while($file = readdir($handle))
			if('.' != $file && '..' != $file)
				$list[] = $file;
		closedir($handle);
		
		sort($list);

		foreach((array)$list as $filename)
			$result .= bindData(
				$this->_script[preg_replace(array('/^\d+_/', '/.php$/'), '', $filename)]->getList(),
				THEME_SLIDER . $filename
			);
		
		$this->_slider = $result;
	}
}
