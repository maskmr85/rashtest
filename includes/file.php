<?php
/***************************************************************************
 *                                  Rash CMS
 *                          -------------------
 *   copyright            : (C) 2009 The RashCMS  $Team = "www.rashcms.com";
 *   email                : info@rashcms.com
 *   email                : rashcms@gmail.com
 *   programmer           : Amir Hossein Abiri (www.BeepDesign.net), Reza Shahrokhian
 ***************************************************************************/
//         Security
if ( !defined('news_security'))
{
 die("You are not allowed to access this page directly!");
}
function unzip_file($file, $to) {

	// Unzip can use a lot of memory, but not this much hopefully
	@ini_set( 'memory_limit', '256M' );

	$needed_dirs = array();
	$to = trailingslashit($to);


	if ( class_exists('ZipArchive') ) {
		$result = _unzip_file_ziparchive($file, $to, $needed_dirs);
		if ( true === $result ) {
			return $result;
		} else {
			
		}
	}
	// Fall through to PclZip if ZipArchive is not available, or encountered an error opening the file.
	return _unzip_file_pclzip($file, $to, $needed_dirs);
}


function _unzip_file_ziparchive($file, $to, $needed_dirs = array() ) {

	$z = new ZipArchive();

	// PHP4-compat - php4 classes can't contain constants
	$zopen = $z->open($file, /* ZIPARCHIVE::CHECKCONS */ 4);
	if ( true !== $zopen )
		return false;
	
	$z->extractTo($to);

	$z->close();

	return true;
}


function _unzip_file_pclzip($file, $to, $needed_dirs = array()) {
	global $wp_filesystem;

	// See #15789 - PclZip uses string functions on binary data, If it's overloaded with Multibyte safe functions the results are incorrect.
	if ( ini_get('mbstring.func_overload') && function_exists('mb_internal_encoding') ) {
		$previous_encoding = mb_internal_encoding();
		mb_internal_encoding('ISO-8859-1');
	}

	require_once('class-pclzip.php');

	$archive = new PclZip($file);

	//$archive_files = $archive->extract(PCLZIP_OPT_EXTRACT_AS_STRING);
	
	if( $archive->extract($to) )
		return true;
	else
		return false;
}


function copy_dir( $source, $destination ) {
	if ( is_dir( $source ) ) {
		@mkdir( $destination );
		$directory = dir( $source );
		while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
			if ( $readdirectory == '.' || $readdirectory == '..' ) {
				continue;
			}
			$PathDir = $source . '/' . $readdirectory; 
			if ( is_dir( $PathDir ) ) {
				copy_dir( $PathDir, $destination . '/' . $readdirectory );
				continue;
			}
			copy( $PathDir, $destination . '/' . $readdirectory );
		}
 
		$directory->close();
	}else {
		copy( $source, $destination );
	}
}

function delete_directory($dirname) {
   if (is_dir($dirname))
      $dir_handle = opendir($dirname);
   if (!$dir_handle)
      return false;
   while($file = readdir($dir_handle)) {
      if ($file != "." && $file != "..") {
         if (!is_dir($dirname."/".$file))
            unlink($dirname."/".$file);
         else
            delete_directory($dirname.'/'.$file);    
      }
   }
   closedir($dir_handle);
   rmdir($dirname);
   return true;
}

function copyDir($source, $dest){ 
    $sourceHandle = opendir($source);
    
	if( !is_dir( $dest ) )
		mkdir($dest); 
    
    while($res = readdir($sourceHandle)){ 
        if($res == '.' || $res == '..') 
            continue; 
        
        if(is_dir($source . '/' . $res)){ 
            copyDir($source . '/' . $res, $dest . '/' . $res); 
        } else { 
            copy($source . '/' . $res, $dest . '/' . $res); 
        } 
    } 
}

function get_data($url)
{
  $ch = curl_init();
  $timeout = 5;
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}