#!/usr/bin/php
<?php

/**
 * Web Honeypot Client - update script
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  
 * 02110-1301, USA.
 * 
 * update-client.php
 *
 *   this script updates the honeypot templates and the honeypot
 * software itself.
 *
 *   THIS FILE MAY BE OVERWRITTEN BY THE UPDATE PROCESS. 
 *   USE config.local TO CONFIGURE
 */


$sBaseDir=realpath(dirname(__FILE__) . '/../');
require_once "$sBaseDir/lib/common_functions.php";

initialize_default_config(array('logfile' => 'honeypot-update-client-%Y-%m.log'));
read_configuration();
check_config_parameters();

if('cli' != php_sapi_name())
	die_on_error('Update client should only be run from the command line!');

//we use the logs directory because it should be writeable
$sLogsDirectory = "$sBaseDir/logs";
print "Basedir: $sBaseDir\n";
if (!aquire_lock($sLogsDirectory . '/update.lock'))
	die_on_error('Failed to aquire lockfile for update!');

// increase the memory limit to make sure that we have enough memory to 
// construct the query and process the response
ini_set('memory_limit', '128M');
	
// send the list to the update server and get back the diff
echo "Sending request to update server...\n";
logdata("Sending request to update server", 1);		
$sUpdateResponse = send_post_request($aConfig['updateurl'],
	'type=summary&localfiles=' . urlencode(calculate_file_hashes($sTemplateDir)));

if (false === $sUpdateResponse)			
	die_on_error('Failed to get response from update server!');
if ('' == $sUpdateResponse) {
	echo "Server returned empty response, nothing to do\n";
	logdata("Server returned empty response, nothing to do", 1);
	exit;
}

//verify the response received 		
if (!preg_match('/[a-f0-9]{40}$/D', $sUpdateResponse, $aMatches))
	die_on_error('Server response did not contain final hash!');
// sSHA1 is the hash appended to the update by the server
$sSHA1 = $aMatches[0];		
// sUpdateResponse is the compressed file
$sUpdateResponse = substr($sUpdateResponse, 0, strlen($sUpdateResponse) - 40);
// hash of the response
$sUpdateHash=sha1($sUpdateResponse);
$sLastNonce = get_nonce(false);
$sExpectedSHA1 = sha1($sLastNonce . $aConfig['hashpassword'] . $sUpdateHash);
if ($sSHA1 != $sExpectedSHA1)
	die_on_error('Server response contained wrong final hash! '.$sExpectedSHA1);
if ( $sUpdateResponse=='NOUPDATE') {
  logdata("no update available",4);
  exit();
}
$sUpdateResponse = gzuncompress($sUpdateResponse);

if (!$sUpdateResponse)
	die_on_error('Failed to uncompress data from server!');
logdata("Processing received data", 1);

foreach (split("\n", $sUpdateResponse) as $sFileLine) {
	// end of file marker
	if ('DONE!' == $sFileLine) break;
		
	if (!preg_match('/([-+][DF]?) (.*)/', $sFileLine, $aMatches)) continue;

	// file/dir operation
	$sOp       = $aMatches[1];
	$sFilename = validate_filename_for_update($aMatches[2]); // do validations on the filename
	if (!$sFilename)
		die_on_error("FATAL: Invalid filename received for update: \"{$aMatches[2]}\"");		
	if ('-' == $sOp[0]) {
		echo "- $sFilename\n";
		logdata("Removing $sFilename", 1);
		if (!rmdir_recursive($sFilename))
			die_on_error("FATAL: failed to delete template file/directory \"$sFilename\" during update!");										
	}
	elseif ('+' == $sOp[0]) {
		// create file / directory
		echo "+ $sFilename\n";
		logdata("Adding $sFilename", 1);
		if ('F' == $sOp[1]) {
			$sDirname = dirname($sFilename);
			$sFilename = basename($sFilename);					
		}
		else {
			$sDirname = $sFilename;
			$sFilename = '';
		}
		
		if ( !is_dir($sDirname) && !mkdir($sDirname, 0755,true)) {
				die_on_error("FATAL: Failed to create directory \"$sDirname\" during update!");
		}
		$sRelativeDir=str_replace($sTemplateDir,'',$sDirname);
		$sRelativeDir.='/';
		if ('' != $sFilename) {
			$sUpdateResponse = send_post_request($aConfig['updateurl'],'type=file&filename='.$sRelativeDir.$sFilename);
			print "sending request for file $sRelativeDir$sFilename\n";
			if ( $sUpdateResponse!='' ) {
			  $sUpdateResponse = gzuncompress($sUpdateResponse);
			  $fp = fopen($sDirname . '/' . $sFilename, 'wb');
			  fputs($fp,$sUpdateResponse);
			  if (!$fp )
			    die_on_error("FATAL: Failed to write to file \"$sFilename\" in directory \"$sDirname\" during update!");
			  fclose($fp);
			} else {
			  print "empty response\n";
			}

		}
	}					
}
echo "Done!\n";
logdata("Done", 1);		

/**
 * Receives a partial filename and checks wheather we should be able to "update" it
 * Returns a string with the fully qualified file name if yes, false otherwise 
 * @param $sFilename
 * @return string or false
 */
function validate_filename_for_update($sFilename) {
	global $sBaseDir;
	global $sTemplateDir;
	print "validating $sFilename\n";
	// config.txt is the only file we are allowed to update from the template dir
	if ('config.txt' == $sFilename)
		return $sTemplateDir . '/config.txt';
	// all the others must be in the templates subdirectory
	// and furthermore, in a templates/\d+ directory
	// unfortuantely we can't use realpath here because it doesn't work for non-existent files...
	if (!preg_match("/^\d+(?:[\\/\\\\]|$)/D", $sFilename)) return false;
	if (preg_match("/\\.\\./", $sFilename)) return false;
	$sFilename = $sTemplateDir . '/' . $sFilename;
	return $sFilename;
}

/**
 * Deletes files and directories. Tries extra-hard to delete them :-)
 * @param $sFilename
 * @return boolean
 */
function rmdir_recursive($sFilename) {
	if (is_dir($sFilename)) {
		$sFilename = rtrim($sFilename, "\\/");
		foreach(glob($sFilename . '/{,.}*', GLOB_BRACE) as $sFile) {
			$sFile = basename($sFile);			
			if ('.' == $sFile || '..' == $sFile) continue;
			if (!rmdir_recursive($sFilename . '/' . $sFile))
				return false;
		}

		if (@rmdir($sFilename)) {
			return true;
		}
		else {
			// try some extreme measures - this may leave the FS in an inconsistent state!
			// (wrong permissions mainly)
			@chmod($sFilename, 0644);
			return @rmdir($sFilename);			
		}
	}
	elseif (file_exists($sFilename)) {
		if (@unlink($sFilename)) {
			return true;
		}
		else {
			// try some extreme measures - this may leave the FS in an inconsistent state!
			// (wrong permissions mainly)
			@chmod($sFilename, 0644);
			return @unlink($sFilename);
		}
	}
	else
		return true;			
}

