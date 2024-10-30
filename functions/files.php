<?php

// include_files_in_dir("/functions/");
function includeFilesInDir($dir, $no_more = FALSE)
{
	$dir_init = $dir;
	// $dir = dirname(__FILE__) . $dir;
	$dir .= "/";

	//if (!file_exists($dir)) throw new Exception("Folder $dir does not exist");

	$files = array();
	if ($handle = opendir($dir)) {
		while (false !== ($file = @readdir($handle))) {
			if (is_dir($dir . $file) && !preg_match('/^\./', $file) && !$no_more) {
				includeFilesInDir($dir_init . $file . "/", TRUE);
			} else {
				if (preg_match('/^[^~]{1}.*\.php$/', $file)) {
					$files[] = $dir . $file;
				}
			}
		}
		@closedir($handle);
	}
	sort($files);

	// foreach ($files as $file) include_once $file;
	foreach ($files as $file) {
		if (file_exists($file)) {
			$realpath = realpath($file);
			include_once esc_url($realpath);
		}
	}
}

function getWebserviceList($dir = "", $no_more = FALSE)
{

	if (empty($dir)) {
		$dir = plugin_dir_path(dirname(__FILE__)) . "webservice";
	}

	$dir_init = $dir;
	// $dir = dirname(__FILE__) . $dir;

	if (!file_exists($dir)) throw new Exception("Folder $dir does not exist");

	$files = array();
	if ($handle = opendir($dir)) {
		while (false !== ($file = @readdir($handle))) {
			if (is_dir($dir . $file) && !preg_match('/^\./', $file) && !$no_more) {
				getWebserviceList($dir_init . $file . "/", TRUE);
			} else {
				if (preg_match('/^[^~]{1}.*\.php$/', $file)) {
					// $files[] = $file;
					$files[] = pathinfo($file, PATHINFO_FILENAME);
				}
			}
		}
		@closedir($handle);
	}
	sort($files);

	return $files;
}
