<?php
/**
 * Project: DShield "Webhoneypot"
 * File name: install.php
 * Description:  web honeypot install script.
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

/**
 *  Initialization function
 */

function init() {
	/*
 	 * this script will only run on the command line. abort if it is not
     */
	
	if (php_sapi_name () !== "cli") {
		echo "This installer must be run from the command-line.";
		exit ( 1 );
	}
	
	/*
	 * banner.
	 */
	
	echo "Starting the Web honeypot command-line installer...\n";
	echo "\n";
	echo "The following tasks will be performed:\n";
	echo "* Detection of the PHP version\n";
	echo "* Detection of the curl version\n";
	echo "* Detection of a HTTP server\n";
	echo "* Detection of the OS\n";
	echo "* Identify the log directory for the HTTP server\n";
	echo "* Configure DShield user/pass for log submission\n";
	echo "* Configure a cron job for automatic template updates\n";
	echo "\n";
	echo "Please refer to the INSTALL file for more information.\n";
	echo "\n";
	echo " (please wait)...\n";
	sleep ( 3 );
} # end init()


/*
 * Check OS and pass control to the appropriate install function
 */

function checkOS() {
	
	$website = "http://sites.google.com/site/webhoneypotsite/home";
	$os = strtolower ( PHP_OS );
	
	switch ($os) {
		
		case "linux" :
			echo "Preparing for a Linux installation...\n\n";
			install ( $os );
			break;
		
		case "winnt" :
			echo "Preparing for a Windows NT/2000/XP installation...\n\n";
			install ( $os );
			break;
		
		case "darwin" :
			echo "Preparing for an OS X/Darwin installation...\n\n";
			install ( $os );
			break;
		
		default :
			echo "Unknown OS ($os). Please check the requirements at $website or perform a manual installation. \n";
			exit ( 1 );
	
	}

} # end checkOS()


/*
 * Check the PHP version
 */
function checkPHP() {
	
	#
	# Define the minimum version that can be used to run the installer.
	#
	# Reference: http://us.php.net/manual/en/features.commandline.php
	#
	#$phpMinVersion = "4.2.0";
	

	echo "Checking PHP version...	";
	
	$phpMinVersion = "5.0.0";
	$aVersionParts = explode ( '.', PHP_VERSION );
	$aMinVersionParts = explode ( '.', $phpMinVersion );
	$nMax = max ( array_merge ( $aVersionParts + $aMinVersionParts ) );
	$nLog = pow ( 10, ceil ( log10 ( $nMax ) ) );
	$nMinVersion = 0;
	$nVersion = 0;
	$nCount = count ( $aMinVersionParts );
	if (count ( $aVersionParts ) < $nCount) {
		for($i = count ( $aVersionParts ); $i <= $nCount; $i ++) {
			$aVersionParts [$i] = 0;
		}
	}
	for($i = 0; $i < $nCount; $i ++) {
		$nMinVersion += $aMinVersionParts [$i] * pow ( $nLog, $nCount - $i - 1 );
		$nVersion += $aVersionParts [$i] * pow ( $nLog, $nCount - $i - 1 );
	}
	
	if ($nMinVersion <= $nVersion) {
		echo "( " . PHP_VERSION . " > " . $phpMinVersion . ") ";
		echo "[ OK ]" . "\n";
	} else {
		echo "[ FAILED ]" . "\n";
		echo "PhP >= $phpMinVersion is required!\n";
		exit ( 1 );
	}

} # end checkPHP()


/*
 * check what web server we are running.
 */
function checkWWW() {
	
	echo "Checking for a webserver...	";
	
	$host = '127.0.0.1';
	$port = '80';
	$request = "HEAD / HTTP/1.0\n\n";
	
	$socket = @fsockopen ( $host, $port );
	
	if (! $socket) {
		echo "[ FAILED ]" . "\n";
		echo "Could not create a connection to $host : $port!";
		exit ( 1 );
	} else {
		
		@fwrite ( $socket, $request );
		stream_set_timeout ( $socket, 10 );
		$reply = fread ( $socket, 400 );
		$wwwVersion = stream_get_meta_data ( $socket );
		@fclose ( $socket );
		
		if ($wwwVersion ['timed_out']) {
			echo "[ FAILED ]\n";
			echo "Error: connection lost!\n";
		} else {
			list ( $string1, $string2 ) = split ( "Server: ", $reply, 2 );
			list ( $wwwPlatform, $wwwPlatformVersion, $remainder ) = split ( "[/ \n\r]", $string2, 3 );
		}
		
		switch (strtolower ( $wwwPlatform )) {
			
			case NULL :
				echo "[ FAILED ]\n";
				echo "Webserver not found!\n";
				exit ( 1 );
				break;
			
			case "apache" :
				echo "( " . $wwwPlatform . " " . $wwwPlatformVersion . " )" . " [ OK ]\n";
				echo "Please note the following for $wwwPlatform:\n\n";
				echo "Add the following line to httpd.conf for a dedicated server:\n\n";
				echo "AliasMatch .* DOCUMENT_ROOT/index.php\n\n";
				echo "Please change DOCUMENT_ROOT to the appropriate directory.\n";
				echo "Note: this must be the first (or only) AliasMatch directive!\n\n";
				break;
			
			case "iis" :
				echo "( " . $wwwPlatform . " " . $wwwPlatformVersion . " )" . " [ OK ]\n";
				break;
			
			default :
				echo "Detected Possibly Unsupported: ( " . $wwwPlatform . " " . $wwwPlatformVersion . " )" . " [ OK ]\n";
		
		} // end switch
	

	}

} # end checkWWW()


/*
 * Check for the existance of curl
 */
function checkCurl() {
	
	$curlVer = curl_version ();
	
	echo "Checking for curl...		";
	
	if (is_null ( $curlVer ) === TRUE) {
		
		echo "[ FAILED ]\n";
		echo "Curl not found!\n";
		exit ( 1 );
	
	} else {
		
		echo "( " . $curlVer ["version"] . " ) ";
		echo "[ OK ]\n";
	
	}

} # end checkCurl()


/*
 * Perform the installation tasks
 */
function install($os) {

$dirSep = DIRECTORY_SEPARATOR;
$requiredDirs = array("docs", "etc", "logs", "lib", "templates", "update");

	while ( true ) {
		echo "Please enter the full path to DOCUMENT_ROOT\n";
		echo "Press enter to use '" . realpath ( dirname ( __FILE__ ) . $dirSep . '..' . $dirSep . 'html' ) . "'\n";
		$docRoot = fgets ( STDIN );
		$docRoot = trim ( $docRoot );
		if ($docRoot == '') {
			$docRoot = dirname ( __FILE__ ) . $dirSep . '..' . $dirSep . 'html' ;
		}
		$docRoot = realpath ( $docRoot );
		clearstatcache ();
		
		# We need to check:
		#   - does the directory exist
		#   - is it actually a directory and not a file / symlink
		#   - is the directory writable
		

		if (! file_exists ( $docRoot )) {
			echo "Directory $docRoot does not exist\n\n";
			continue;
		}
		
		if (! is_dir ( $docRoot )) {
			echo "$docRoot is not a valid directory\n\n";
			continue;
		}
		
		foreach($requiredDirs as $idx => $dir) {	

		$path = $docRoot . $dirSep . ".." . $dirSep . $dir;		

			if (! is_dir ($path)) {
				echo "$path does not appear to be a valid directory\n\n";
				continue;
			}

			if (! is_readable ($path)) {
				echo "I am not able to read the $path directory\n\n";
				continue;
			}		

			if (! is_writable ($path)) {
				echo "I am not able to write to the $path directory\n\n";
				continue;
			}


		} # end foreach

		break;
	}
	
	echo "DOCUMENT_ROOT set to $docRoot\n";
        echo "setting permissions of logs directory to 1777\n";
	chmod($docRoot.$dirSep."..".$dirSep."logs", 01777 );
	confDShield ( $os, $docRoot );
	confAutoUpdates ( $os, $docRoot );

} # end install()


/*
 * Setup DShield log submission
 */
function confDShield($os, $docRoot) {
	
	$website = "https://secure.dshield.org/register.html";
	$dshieldConf = "config.local";
	$dirSep = DIRECTORY_SEPARATOR;
	
	echo "Configuring DShield (OS: $os)...\n";
	echo "\n";
	echo "If you do not have a DShield login, please register now at $website\n";
	echo "\n";
	
	echo "Please enter your DShield user id: ";
	$userid = rtrim ( fgets ( STDIN ) );
	
	echo "Please enter your password: ";
	$password = sha1 ( rtrim ( fgets ( STDIN ) ) . $userid );
	$dshieldConfPath = $docRoot . $dirSep . ".." . $dirSep . "etc" . $dirSep . $dshieldConf;
	echo "Writing configuration file to $dshieldConfPath ...\n";
	if (! $fileHandle = @fopen ( $dshieldConfPath, "wt" )) {
		echo "Failed to open or create $dshieldConfPath!\n";
		exit ( 1 );
	} else {
		fwrite ( $fileHandle, "[config]\n" . "userid=$userid\n" . "hashpassword=$password\n" );
	}
	
	@fclose ( $fileHandle );

} # end confDShield()


/*
 * Setup automatic template updates
 */
function confAutoUpdates($os, $docRoot) {
	
$dirSep = DIRECTORY_SEPARATOR;

	switch ($os) {
		
		case "linux" :
			echo "Please add the following two lines to your crontab to enable template updates:\n";
			echo "\n";
			$r1=rand(0,23);
			$r2=rand(0,59);
			echo "$r2 $r1 * * *	".realpath($docRoot . $dirSep . "..".$dirSep."update" . $dirSep . "update-templates.php")." > /dev/null\n";
			echo "\n";
			$r1=rand(0,23);
			$r2=rand(0,59);
			echo "$r2 $r1 * * *	".realpath($docRoot . $dirSep . "..".$dirSep."update" . $dirSep . "update-client.php")." > /dev/null\n";

			echo "If you need help, please see 'man cron' for more information.\n";
			echo "\n";
			echo "\n";
			echo "It is highly recommended that you first run these two commands right now to make sure they work. \n";
			break;
		
		case "winnt" :
			
			#
			#	winnt update code goes here
			#
			

			break;
	
	}

} # end confAutoUpdates()


init ();
checkPHP ();
checkCurl ();
checkWWW ();
checkOS ();

echo "Web honeypot installation completed.\n";
