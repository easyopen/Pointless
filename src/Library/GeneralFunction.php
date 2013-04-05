<?php
/**
 * General Function
 * 
 * @package		Pointless
 * @author		ScarWu
 * @copyright	Copyright (c) 2012-2013, ScarWu (http://scar.simcz.tw/)
 * @link		http://github.com/scarwu/Pointless
 */

/**
 * Create Link
 *
 * @param string
 * @param string
 * @return string
 */
function linkTo($link, $name) {
	return '<a href="' . $link . '">' . $name . '</a>';
}

/**
 * Bind PHP Data to HTML Template
 *
 * @param string
 * @param string
 * @return string
 */
function bindData($data, $path) {
	ob_start();
	include $path;
	$result = ob_get_contents();
	ob_end_clean();
	
	return $result;
}

/**
 * Write Data to File
 *
 * @param string
 * @param string
 */
function writeTo($data, $path) {
	// FIXME and Theme/Script/*/*.php
	if(!preg_match('/\.(html|xml)$/', $path)) {
		if(!file_exists($path))
			mkdir($path, 0755, TRUE);
		$path = $path . '/index.html';
	}

	$handle = fopen($path, 'w+');
	fwrite($handle, $data);
	fclose($handle);
}

/**
 * Recursive Copy
 *
 * @param string
 * @param string
 */
function recursiveCopy($src, $dest) {
	if(file_exists($src)) {
		if(is_dir($src)) {
			if(!file_exists($dest))
				mkdir($dest, 0755, TRUE);
			$handle = @opendir($src);
			while($file = readdir($handle))
				if($file != '.' && $file != '..' && $file != '.git')
					recursiveCopy($src . '/' . $file, $dest . '/' . $file);
			closedir($handle);
		}
		else
			copy($src, $dest);
	}
}

/**
 * Recursive Remove
 *
 * @param string
 * @param string
 * @return boolean
 */
function recursiveRemove($path = NULL) {
	if(file_exists($path)) {
		if(is_dir($path)) {
			$handle = opendir($path);
			while($file = readdir($handle))
				if($file != '.' && $file != '..' && $file != '.git')
					recursiveRemove($path . '/' . $file);
			closedir($handle);
			
			if($path != PUBLIC_FOLDER && $path !=DEPLOY_FOLDER)
				return rmdir($path);
		}
		else
			return unlink($path);
	}
}

/**
 * Sort Using Article's Count
 *
 * @param array
 * @return array
 */
function countSort($list) {
	uasort($list, function($a, $b) {
		if (count($a) == count($b))
			return 0;

		return count($a)  > count($b) ? -1 : 1;
	});
	
	return $list;
}

/**
 * Create Date List Using Article
 *
 * @param array
 * @return array
 */
function createDateList($list) {
	$result = array();

	foreach((array)$list as $article) {
		if(!isset($result[$article['year']]))
			$result[$article['year']] = array();
		
		if(!isset($result[$article['year']][$article['month']]))
			$result[$article['year']][$article['month']] = array();
		
		$result[$article['year']][$article['month']][] = $article;
	}

	return $result;
}