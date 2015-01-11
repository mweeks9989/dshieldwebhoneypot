<?php

/**
 * Project: DShield "Webhoneypot"
 * File name: common_functions.php
 * Description:  A set of common functions used by other scripts like index.php, update.php and install.php.
 *   
 * $Date$ 
 * $Id$  
 * $Author$
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but 
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY 
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License 
 * for more details.
 * 
 * You should have received a copy of the GNU General Public License along 
 * with this program; if not, write to the Free Software Foundation, Inc., 
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */


// this file can  be only included, not used directly
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit;

/**
 * Calcualtes the SHA1 for a file. If there are errors during calculation,
 * an entry is written to the screen/logfile and the script is terminated
 * @param $sFilename
 * @return string
 */

function get_file_sha1($sFilename, $sBaseDir) {
	$sha1 = sha1_file($sFilename);
	if (!$sha1) {
		logdata("ERROR: Failed to calculate SHA1 for the file $sFilename!", 0);
		die_on_error('Failed to calculate SHA1 for file');
	}
	$sFilename = exclude_basedir($sFilename, $sBaseDir);
	if ( $sFilename=='config.txt' ) {
	  $sha1=sha1('x');
	}
	return "F $sha1 $sFilename";
}

/**
 * Calculates the SHA1 recursively starting from a given directory.
 * Returns an array containing one entry per directory / file 
 * 
 * @param $sDirectory
 * @return array
 */
function get_file_sha1_recursive($sDirectory, $sBaseDir) {
	$aResult = array();
	$aResult[] = 'D ' . sha1('') . ' ' . exclude_basedir($sDirectory, $sBaseDir);
	foreach (glob($sDirectory . '/{,.}*', GLOB_BRACE) as $sFilename) {
		$sFilename = basename($sFilename);
		// skip "hidden" files and folders. This means that we can directly apply
		// this to an SVN checkout and still be ok 
		if ('.' == $sFilename[0]) continue;
		$sFilename =  $sDirectory . '/' . $sFilename;
		if (is_dir($sFilename)) {
			$aResult = array_merge($aResult, get_file_sha1_recursive($sFilename, $sBaseDir));		
		}
		else {
			$aResult[] = get_file_sha1($sFilename, $sBaseDir);
		}
	}
	return $aResult;
}

/**
 * strip a "base directory" from the beginning of a file name
 * @param string $sFilename  complete file name
 * @param string $sBaseDir   based directory to be removed.
 * @return string
 */

function exclude_basedir($sFilename, $sBaseDir) {
	$sFilename = realpath($sFilename);
	$sBaseDir  = realpath($sBaseDir);
	if (!preg_match("/[\\/\\\\]$/", $sBaseDir)) $sBaseDir .= '/';
	$sFilename = str_replace("\\", '/', $sFilename);
	return substr($sFilename, strlen($sBaseDir));
}

/**
 * Calculates the hashes for a basedir used for update
 * SHA1 is calculated for each template file + config.txt (we don't touch config.local)  
 */

function calculate_file_hashes($sTemplateDir) {
	$aFileHashes = array();		
	$aFileHashes[] = get_file_sha1("$sTemplateDir/config.txt", $sTemplateDir);
	foreach (glob($sTemplateDir . '/*') as $sFilename) {
		$sFilename = basename($sFilename);
		if (!preg_match('/^\d+$/D', $sFilename)) continue;
		$sFilename = $sTemplateDir . '/' . $sFilename;
		if (!is_dir($sFilename)) continue;
		$aFileHashes = array_merge($aFileHashes, get_file_sha1_recursive($sFilename, $sTemplateDir));
	}
	return join("\x0d\x0a", $aFileHashes); 
}

/**
 * Parses a line of the format F|D SHA1 filename. Returns false if it can't parse the line 
 * @return boolean
 */

function parse_update_line($sLine, &$sType, &$sSHA1, &$sFilename) {
	if (!preg_match('/^([FD]) ([0-9a-f]{40}) (.*)$/', $sLine, $aMatches)) return false;
	$sType     = $aMatches[1];
	$sSHA1     = $aMatches[2];
	$sFilename = $aMatches[3];
	return true;
}

/**
 * Parses a collection of lines separated by \x0d\x0a
 * @param $sLines
 * @return array
 */
function parse_update_lines($sLines) {
	$aResult = array();
	foreach(explode("\x0d\x0a", $sLines) as $sLine) {
		if (!parse_update_line($sLine, $sType, $sSHA1, $sName)) continue;
		$aResult[$sName] = array(
			'sha1' => $sSHA1,
			'type' => $sType,
		);
	}
	return $aResult;
}

/**
 * Checks if a given file is in the given directory (doesn't try to
 * "escape" using path traversal)
 * @param $file
 * @param $dir
 * @return unknown_type
 */
function is_file_in_directory($sFileName, $sDirName) {
	$sFileName = realpath(dirname($sFileName));
	$sDirName  = realpath($sDirName);
	return $sFileName == $sDirName;
}

/**
 * Checks if a given file is writeable 
 * @return boolean
 */
function is_file_writable($sFileName) {
	$bFileExisted = file_exists($sFileName);
	$fh = @fopen($sFileName, 'a');
	$result = ($fh) ? true : false;
	@fclose($fh);	
	if (!$bFileExisted && file_exists($sFileName) ) unlink($sFileName);
	return $result;
}

/**
 * Tries to aquire the specified lockfile. Non-blocking.
 * Returns true on success, false on failure (in which case you probably stop
 * doing whatever you were doing..)
 * @param $sLockfileName
 * @return boolean
 */
function aquire_lock($sLockfileName) {
	$fh = fopen($sLockfileName, 'w+');
	if (!$fh) return false;
	return flock($fh, LOCK_EX|LOCK_NB);
}

function escape_logline($matches) {
	return sprintf('\x%02x', ord($matches[0]));
}

/**
 * logdata function: This function will write data to the log file.
 *
 * @Param $sMsg string the message that will be written to the file.
 * @Param $nLevel int  the log level. Higher log levels are used for
 *                     debugging.
 *
 * A timestamp and the IP address of the client is added automatically.
 *
 */
function logdata($aMsg,$nLevel=9) {
	global $aConfig;
	if (@$aConfig['loglevel'] < $nLevel) return true;

	$sLogline = '';
	if (!is_array($aMsg)) $aMsg = array($aMsg);
	foreach ($aMsg as $sMsg) {
		if ('' != $sLogline) $sLogline .= "\n\t"; 
		// escape the special characters from the log message to avoid injection attacks
		$sLogline .= preg_replace_callback('/[^\x20-\x7E]/', 'escape_logline', $sMsg);
	}	
	
	
	$sMsg = strftime('%Y-%m-%d %H:%M:%S %z') . ' ' 
		. (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0')
		. ' ' . $sLogline . "\n";
	return error_log($sMsg, 3, @$aConfig['phylogfile']);
}

function die_on_error($sErrorMsg) {
	logdata("FATAL: $sErrorMsg", 1);
	if ('cli' == php_sapi_name()) {
		echo "The following error was encountered:\n$sErrorMsg\n";
	}
	else {
		echo '<font color="red"><b>The following error was encountered:' . "<br><pre>\n" . htmlentities($sErrorMsg) . '</pre></b></font>';
	}

	exit;
}

/**
 * Sets up the default values in the configuration array. In $aOverrides you can put
 * a list of values (as an array) which should override the defaults. Example:
 * initialize_default_config(array('logfile' => 'honeypot-update-client-%Y-%m.log'))
 * 
 * Requires $sBaseDir to be set up correctly
 */
function initialize_default_config($aOverrides) {
	global $aConfig, $sBaseDir;
	global $sConfigDir, $sTemplateDir;

	if (!isset($aConfig))
		$aConfig = array(
			'template_basenames' => array(),
		);
	
	$sConfigDir   = "$sBaseDir/etc";
	$sTemplateDir = "$sBaseDir/templates";
	
	$aConfig['loglevel'] = 1;
	$aConfig['logfile']  = 'honeypot-%Y-%m.log';
	$aConfig['logdir']   = $sBaseDir . '/logs';
	$aConfig['updateurl']= 'http://isc.sans.org/webhoneypot/update.html';
	$aConfig['url']      = 'http://isc1.sans.org/weblogs/post.html';
	$aConfig['fetch_rfi']   = 0;
	$aConfig['emulate_rfi'] = 0;
	$aConfig['ratelimit'] =1 ;         // rate limit to 1 post per second
	$aConfig['deliverydelay']=1000000; // slow down page delivery in micro seconds
	$aConfig['ratelimitfile']=$aConfig['logdir'].'/ratelimit.txt';
	if (isset($aOverrides))
		foreach ($aOverrides as $key => $value)
			$aConfig[$key] = $value;
	
	$aConfig['phylogfile'] = $aConfig['logdir'] . '/' . strftime($aConfig['logfile']);	
}

/**
 * Loads the configuration from the two sources (global and local) 
 */
function read_configuration() {
  global $aTemplateLookup, $aConfig, $sTemplateDir, $sConfigDir, $aTokens, $aLinkURLs;
  $aTokens=array();
  $aLinkURLs=array();
	$aTemplateLookup = array();
	read_config("$sTemplateDir/config.txt", 'global');
	read_config("$sConfigDir/config.local", 'local');
	
	if (!isset($aConfig['restrict_templates']))
		$aConfig['restrict_templates'] = 0;	
	// the logfile might have been overriden in the configuration file
	$aConfig['phylogfile'] = $aConfig['logdir'] . '/' . strftime($aConfig['logfile']);
	
	logdata("Configuration: user           : {$aConfig['userid']}",8);
	logdata("Configuration: hashedpassword : {$aConfig['hashpassword']}",9);
	logdata("Configuration: posturl        : {$aConfig['url']}",5);
	logdata("Configuration: updateurl      : {$aConfig['updateurl']}",5);
}

function regex_validator($errno, $errstr, $errfile, $errline) {
	global $bRxErrorOccurred;	
	$bRxErrorOccurred = true;
	return true;
}

/**
 * read configuration file.
 */
function read_config($sFilename, $sRulePrefix) {
  global $aConfig, $aMimeCheat, $aTemplateLookup, $bRxErrorOccurred, $aTokens, $aLinkURLs;
  $aTemplateLookup[$sRulePrefix] = array();
  if (!file_exists($sFilename)) return;
  
  $hFile=fopen($sFilename, 'r');
  if (!$hFile) die_on_error("Failed to open configuration file \"$sFilename\"!");
  
  // specifies the section ([config], [rules], etc) we are in
  $currentSection = null;
  while ( $hFile && !feof($hFile) ) {
    $line=rtrim(fgets($hFile,4096));
    // skip comments
    if (preg_match('/^\s*[#;].*$/',$line)) 
    	continue;
    // new section
    if (preg_match('/^\s*\[(\w+)\]/i', $line, $matches)) {
    	$currentSection = strtolower($matches[1]);
    	continue;
    }
    // configuration settings - written to $aConfig
    if ('config' == $currentSection && preg_match('/^\s*(\w+)\s*=\s*(.*)/', $line, $aMatches)) {
    	$aConfig[$aMatches[1]] = $aMatches[2];
    	logdata("DEBUG: config parameter {$aMatches[1]} {$aMatches[2]}",9);
    	continue;
    }
    // rules - written to $aTemplateLookup - in the correct section
    if ('rules' == $currentSection && preg_match('/^\s*(.*?)\s*(\d+)(\.\d+)?$/', $line, $aMatches)) {
    	$rx = $aMatches[1];
    	if (!preg_match('/^\/.*\/[a-z]*/i', $rx))
    		$rx = '/' . preg_quote($rx) . '(.*)/i';
    	$template_id = intval($aMatches[2]);
    	$status_code = (isset($aMatches[3])) 
    		? intval(substr($aMatches[3], 1)) 
    		: 200;
    		
    	// test if the regex compiles
    	$bRxErrorOccurred = false;
    	set_error_handler('regex_validator');
    	preg_match($rx, '');
    	restore_error_handler();
    	
    	if ($bRxErrorOccurred) {
    		logdata("Invalid regex \"$rx\" found at line \"$line\"!",1);
    		continue;
    	}

    	$aTemplateLookup[$sRulePrefix][] = array(
    		'rx' => $rx,
    		'template' => $template_id,
    		'status_code' => $status_code,  	  	
    	);
    	logdata("DEBUG: template lookup {$aMatches[1]} {$aMatches[2]}",9);  	  	
    	continue;
    }
    if ('basenames' == $currentSection && preg_match('/^\s*(\d+)\s*=\s*(.*)/', $line, $aMatches)) {
    	if (!isset($aConfig['template_basenames'][$aMatches[1]]))
    		$aConfig['template_basenames'][$aMatches[1]] = array();
    	$aConfig['template_basenames'][$aMatches[1]][] = $aMatches[2]; 
    }
    if ('mimetypes' == $currentSection && preg_match('/^\s*([a-z]+)\s*=\s*(.*)/i', $line, $aMatches)) {
    	$aMimeCheat[$aMatches[1]] = $aMatches[2];
    }
    if ('tokens' == $currentSection && preg_match('/^\s*([A-Z]+)\s*=\s*(.*)/',$line,$aMatches)) {
      $aTokens[$aMatches[1]]=$aMatches[2];
    }
    if  ('linkurls' == $currentSection && preg_match('/^.+$/',$line)) {
      array_push($aLinkURLs,$line);
    }
  }
}

/**
 * Checks the configuration settings. If an error is detected, die_on_error is called
 * and the function never returns.
 */
function check_config_parameters() {
	global $sBaseDir, $aConfig, $sTemplateDir;
	
	if (!is_dir($sBaseDir))
		die_on_error('Base directory not found!');
	if (!is_above_document_root($sBaseDir))
		die_on_error('Base directory must be above document root!');
	if (!is_dir($sBaseDir . '/templates'))
		die_on_error('Templates directory not found!');
	if (!is_dir($sBaseDir . '/logs'))
		die_on_error('logs directory not found!');
	if (!file_exists($sTemplateDir . '/config.txt'))
		die_on_error('Main template configuration file not found!');
	if (!is_file_writable($aConfig['phylogfile']))
		die_on_error('Logfile is not writeable!');
	if (!isset($aConfig['url']))
		die_on_error('Submission URL not specified!');
	if (!isset($aConfig['updateurl']))
		die_on_error('Update URL not specified!');
	if (!isset($aConfig['userid']))
		die_on_error('userid not set! Please set it in config.local!');
	if (!isset($aConfig['hashpassword'])) {
		if (isset($aConfig['password'])) {
			$sHashPass = sha1($aConfig['password'] . $aConfig['userid']);
			logdata("hashpassword=$sHashPass", 0);
			die_on_error('Check logfile for hashpassword. Copy it to config.local.');					
		}
		else		
			die_on_error('password or hashpassword not set! Please set one of them in config.local!');
	}				
}

function get_nonce($bNewNonce = true) {
	global $sUniqueNonce;
	if ($bNewNonce)
	  $sUniqueNonce = sha1(uniqid(rand(), true)); 
	return $sUniqueNonce;
}

function write_to_socket($hSocket, $sData) {
	while (strlen($sData) > 0) {
		$iWritten = fwrite($hSocket, $sData);
		if (false === $iWritten) return false;
		$sData = substr($sData, $iWritten);
	}
	return true;
}

/**
 * Parses a URL into its parts.
 * @param $sUrl
 * @return An array with the following elements:
 * - protocol
 * - host
 * - port
 * - path
 * Or false, if it fails to parse the URL
 */
function parse_url_parts($sUrl) {
	$aUrlParts = parse_url($sUrl);
	if (!$aUrlParts) return false;
	// currently we can fetch only http
	if ('http' != $aUrlParts['scheme']) return false;
	// default port for http
	if (!isset($aUrlParts['port'])) $aUrlParts['port'] = 80;
	// the full query string
	$aUrlParts['queryString'] = $aUrlParts['path'];
	if (isset($aUrlParts['query'])) $aUrlParts['queryString'] .= '?' . $aUrlParts['query'];
	if (isset($aUrlParts['fragment'])) $aUrlParts['queryString'] .= '#' . $aUrlParts['fragment']; 
	
	return $aUrlParts;
}

/**
 * Reads the HTTP response from a socket. Returns the response if the
 * read succeeded and false if it failed. It also closes the socket. 
 * @param $fp
 * @return string or false
 */
function read_request($fp) {
	$bDataReceived = false; $bFirstLine = true; $bEmptyLineRead = false;
	$sResult = '';
	while (!feof($fp)) {
		$sData = fgets($fp, 1024); 
		if ($bFirstLine) {
			$bDataReceived = preg_match('/200\s+OK/m', $sData);
			$bFirstLine = false;
		}
		if ($bEmptyLineRead) {
			// we are past the headers - store data if the headers said 200 OK
			if ($bDataReceived) $sResult .= $sData; 
		}
		else {
			if ('' == trim($sData)) $bEmptyLineRead = true;
		}				
	}
  fclose($fp);  	
  
  if (!$bDataReceived) return false;
  return $sResult;	
}

/**
 * Sends a post request to the URL determined by the parameter. Supports only HTTP
 * currently (no HTTPS). $sPostData must contain pairs of urlencoded name=value
 * joined by &
 * The following variables are added automatically:
 * Returns the response on success, false on failure.
 *   - username
 *   - nonce
 *   - password (the hash of the password)
 *   - version (the version of index.php) 
 * @param $sUrl
 * @param $sPostData
 * @return string or false
 */
function send_post_request($sUrl, $sPostData) {
	global $whVersion;
	global $aConfig;
	
	$aUrlParts = parse_url_parts($sUrl);
	if (!$aUrlParts) return false;

	/*
	 * check throttle
	 */

	$aStat=stat($aConfig['ratelimitfile']);
	if ( $aStat && time()-$aStat[9]<$aConfig['ratelimit'] ) {
	  logdata("Debug: request not posted due to ratelimit", 9);
	  return false;
	}
	$fp=fopen($aConfig['ratelimitfile'],'w');
	fwrite($fp,time());
	fclose($fp);
	
  $sNonce = get_nonce();  
  $sPass  = sha1($sNonce . $aConfig['hashpassword']);
  
  if ('' != $sPostData) $sPostData .= '&';
  $sPostData .= 'username=' . urlencode($aConfig['userid'])
  	. '&nonce=' . urlencode($sNonce)
  	. '&password=' . urlencode($sPass)
  	. '&version=' . urlencode($whVersion)
  	. "\x0d\x0a";	
	
	$fp = fsockopen($aUrlParts['host'], $aUrlParts['port'], $errno, $errstr, 30);
	if (!$fp) {
		logdata("ERROR: failed to connect to host {$aUrlParts['host']}:{$aUrlParts['port']}: $errstr ($errno)", 1);
		return false;
	}
	
	// use HTTP 1.0 to avoid getting chunked encoding which would require us to do more work :-)
	$sRequest = "POST {$aUrlParts['queryString']} HTTP/1.0\x0d\x0a"
		. "Host: {$aUrlParts['host']}\x0d\x0a"
		. "Pragma: no-cache\x0d\x0a"
		. "Accept: */*\x0d\x0a"
		. "User-Agent: hp-honeypot, ver $whVersion\x0d\x0a"
		. "Connection: Close\x0d\x0a"
		. "Content-Length: " . strlen($sPostData) . "\x0d\x0a"
		. "Content-Type: application/x-www-form-urlencoded\x0d\x0a"
		. "\x0d\x0a";	
	if (!write_to_socket($fp, $sRequest)) {
		fclose($fp);
		return false;
	}	
	if (!write_to_socket($fp, $sPostData)) {
		fclose($fp);
		return false;
	}

	return read_request($fp);
}

function send_get_request($sUrl) {
	$aUrlParts = parse_url_parts($sUrl);
	if (!$aUrlParts) return false;
	
	$fp = fsockopen($aUrlParts['host'], $aUrlParts['port'], $errno, $errstr, 30);
	if (!$fp) {
		logdata("ERROR: failed to connect to host {$aUrlParts['host']}:{$aUrlParts['port']}: $errstr ($errno)", 1);
		return false;
	}
	
	// use HTTP 1.0 to avoid getting chunked encoding which would require us to do more work :-)
	$sRequest = "GET {$aUrlParts['queryString']} HTTP/1.0\x0d\x0a"
		. "Host: {$aUrlParts['host']}\x0d\x0a"
		. "Pragma: no-cache\x0d\x0a"
		. "Accept: */*\x0d\x0a"
		. "User-Agent: PHP\x0d\x0a"
		. "Connection: Close\x0d\x0a"
		. "\x0d\x0a";	
	if (!write_to_socket($fp, $sRequest)) {
		fclose($fp);
		return false;
	}	
	
	return read_request($fp);
}

/**
 * Checks if the given path is above the document root.
 */
function is_above_document_root($sPath) {
	$sDocRoot = @$_SERVER['DOCUMENT_ROOT'];
	if (!is_dir($sDocRoot)) {
		// on the command line we don't know where the DocumentRoot is
		// so there is no way we could check it...
		// also, in some cases, it might not be properly configured
		// (example: under default configuration of nginx)
		return true;
	}
	return is_above_directory($sPath, $sDocRoot);
}

function is_above_directory($sPath, $sBase) {
	$sPath = virtual_realpath($sPath);
	$sBase = virtual_realpath($sBase);
	return 0 !== strncmp($sPath, $sBase, strlen($sBase));
}

function is_below_directory($sPath, $sBase) {
	return !is_above_directory($sPath, $sBase);
}

/**
 * Parses the path and resolves .. and .
 * Needed because suhoshin patched realpath works differently than
 * normal PHP realpath
 * @param $sPath
 * @return the path with .. and . rezolved
 */
function virtual_realpath($sPath) {
	// if this doesn't refer to the root, add the current directory
	if (!preg_match("/^(?:[a-z]:)?[\\/\\\\]/i", $sPath))
		$sPath = getcwd() . '/' . $sPath;
	$sPath = str_replace('\\', '/', $sPath);
	$aPathComponents = array();
	foreach (explode('/', $sPath) as $sPart) {
		if ('.' == $sPart) {
			continue;
		}
		else if ('..' == $sPath) {
			if (count($aPathComponents) > 0)
				array_pop($aPathComponents);			 
		}
		else if ('' != $sPath) {
			$aPathComponents[] = $sPart;
		}
	}
	
	return implode('/', $aPathComponents);
}
function untar($sFile,$sBasedir) {
  $sFilename=substr($sFile,0,100);
  $sFilesize=octdec((substr($sFile,124,12)));
  if ( $sFilesize>0 ) {
    print "$sFilesize\t$sFilename\n";
    $fp=fopen("$sBasedir/$sFilename",'w');
    fwrite($fp,substr($sFile,512,$sFilesize));
    fclose($fp);
  }
  return substr($sFile,512+ceil($sFilesize/512)*512);
}
