<?php

class Tag {
	private $_list;
	
	public function __construct() {
		$this->_list = array();
		$source = Resource::get('source');

		foreach($source['article'] as $index => $value) {
			foreach($value['tag'] as $tag) {
				if(!isset($this->_list[$tag]))
					$this->_list[$tag] = array();

				$this->_list[$tag][] = $value;
			}
		}
	}
	
	public function getList() {
		return $this->_list;
	}
	
	public function sortList() {
		$this->_list = countSort($this->_list);
	}
	
	public function gen($slider) {
		$max = array(0, NULL);
		$count = 0;
		$total = count($this->_list);
		$key = array_keys($this->_list);
		
		foreach((array)$this->_list as $index => $article_list) {
			NanoIO::writeln(sprintf("Building tag/%s", $index));
			$max = count($article_list) > $max[0] ? array(count($article_list), $index) : $max;
			
			$output_data['bar'] = array();
			$output_data['bar']['index'] = $count+1;
			$output_data['bar']['total'] = $total;
			if(isset($key[$count-1]))
				$output_data['bar']['prev'] = array(
					'title' => $key[$count-1],
					'url' => $key[$count-1]
				);
			if(isset($key[$count+1]))
				$output_data['bar']['next'] = array(
					'title' => $key[$count+1],
					'url' => $key[$count+1]
				);
			
			$count++;
			
			$output_data['title'] = 'Tag: ' . $index;
			$output_data['article_list'] = $article_list;
			$output_data['container'] = bindData($output_data, THEME_CONTAINER . 'Tag.php');
			$output_data['slider'] = $slider;
			
			$result = bindData($output_data, THEME . 'index.php');
			writeTo($result, PUBLIC_FOLDER . 'tag/' . $index);
		}
		
		if(file_exists(PUBLIC_FOLDER . 'tag/' . $max[1] . '/index.html'))
			copy(PUBLIC_FOLDER . 'tag/' . $max[1] . '/index.html', PUBLIC_FOLDER . 'tag/index.html');
	}
}
