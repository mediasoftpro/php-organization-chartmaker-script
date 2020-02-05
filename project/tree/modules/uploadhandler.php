<?php
/**
 * upload.php
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */
 
include_once("../../include/config.php");
include_once(INCLUDE_ROOT . "utility/thumbs.php");
require_once ROOTPATH . "assets/plugins/thumbs/ThumbLib.inc.php";  

// HTTP headers for no cache etc
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

try
{
	// Settings
	
	$targetDir = UPLOAD_DIRECTORY_PATH2 . "/original";
	$thumbDir = UPLOAD_DIRECTORY_PATH2;
	$midDir = UPLOAD_DIRECTORY_PATH2 . "/medium";
	
	$cleanupTargetDir = true; // Remove old files
	$maxFileAge = 5 * 3600; // Temp file age in seconds
	
	// 5 minutes execution time
	@set_time_limit(5 * 60);
	
	// Uncomment this one to fake upload time
	// usleep(5000);
	
	// Get parameters
	$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
	$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
	$fName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
	
	// filename preparation
	
	// avoid duplication
	$oFileName = preg_replace('/[^\w\._]+/', '_', $fName);
	$fileName = mt_rand(0,mt_getrandmax()) . "_" . $oFileName;

	// Make sure the fileName is unique but only if chunking is disabled
	if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
		$ext = strrpos($fileName, '.');
		$fileName_a = substr($fileName, 0, $ext);
		$fileName_b = substr($fileName, $ext);
	
		$count = 1;
		while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
			$count++;
	
		$fileName = $fileName_a . '_' . $count . $fileName_b;
	}
	
	$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
	
	// Create target dir
	if (!file_exists($targetDir))
		@mkdir($targetDir);
	
	// Remove old temp files	
	if ($cleanupTargetDir) {
		if (is_dir($targetDir) && ($dir = opendir($targetDir))) {
			while (($file = readdir($dir)) !== false) {
				$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
	
				// Remove temp file if it is older than the max age and is not the current file
				if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
					@unlink($tmpfilePath);
				}
			}
			closedir($dir);
		} else {
			die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
		}
	}	
	
	// Look for the content type header
	if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
		$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
	
	if (isset($_SERVER["CONTENT_TYPE"]))
		$contentType = $_SERVER["CONTENT_TYPE"];
	
	// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
	if (strpos($contentType, "multipart") !== false) {
		if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
			// Open temp file
			$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = @fopen($_FILES['file']['tmp_name'], "rb");
	
				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
				@fclose($in);
				@fclose($out);
				@unlink($_FILES['file']['tmp_name']);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
	} else {
		// Open temp file
		$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
		if ($out) {
			// Read binary input stream and append it to temp file
			$in = @fopen("php://input", "rb");
	
			if ($in) {
				while ($buff = fread($in, 4096))
					fwrite($out, $buff);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	
			@fclose($in);
			@fclose($out);
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
	}
	
	// Check if file has been uploaded
	if (!$chunks || $chunk == $chunks - 1) {
		// Strip the temp .part suffix off 
		rename("{$filePath}.part", $filePath);
	}
	
	//************************************************
	// Resize Image
	//************************************************
	// Thumb Lib Plugin
	$thumb = PhpThumbFactory::create($targetDir .'/' . $fileName); 
	//if(ThumbType == 1)
	//   $thumb->adaptiveResize(CoverWidth, CoverHeight)->save($thumbDir . '/' . $fileName);
	//else
	$thumb->resize(150,150)->save($thumbDir . '/' . $fileName);
	// mid thumb
	//$thumb->resize(MidWidth,MidHeight)->save($thumbDir . '/' . $fileName);
	// Normal Plugin
	//photothumbs::createThumb($targetDir .'/', $fileName, $midDir . '/', MidWidth);
	
	$url = SITE_DOMAIN . "contents/" . $fileName;
	
	$fileType = "image";
	 
	die('{"jsonrpc" : "2.0", "result" : "OK", "id" : "id", "fname" : "' . $fileName . '", "url" : "' . $url . '", "filetype" : "' . $fileType . '", "filename" : "' . $fileName . '"}');
}
catch (Exception $e) 
{
	die('{"jsonrpc" : "2.0", "error" : {"code": 1000, "message": ' . $e->getMessage() . '"}, "id" : "id"}');
}
