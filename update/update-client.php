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


$whVersion = '$Rev: 115 $'; // updated by subversion. do not change.
$myVersion=preg_replace('/[^0-9]/','',$whVersion);
$whVersion = '0.1.r'.$myVersion;
$sBaseDir=realpath(dirname(__FILE__) . '/../');
require_once "$sBaseDir/lib/common_functions.php";

initialize_default_config(array('logfile' => 'honeypot-update-client-%Y-%m.log'));
read_configuration();
check_config_parameters();

if('cli' != php_sapi_name())
	die_on_error('Update client should only be run from the command line!');

//we use the logs directory because it should be writeable
$sLogsDirectory = "$sBaseDir/logs";
if (!aquire_lock($sLogsDirectory . '/updateclient.lock'))
	die_on_error('Failed to aquire lockfile for update!');

// increase the memory limit to make sure that we have enough memory to 
// construct the query and process the response
ini_set('memory_limit', '128M');
$sUpdateResponse = send_post_request($aConfig['updateurl'],"type=version&myversion=$myVersion");
if ( $sUpdateResponse>$myVersion ) {
  $sUpdateResponse = send_post_request($aConfig['updateurl'],"type=getclient");
  $remotesha1=substr($sUpdateResponse,-41);
  $remotesha1=substr($remotesha1,0,-1);
  $sUpdateResponse=substr($sUpdateResponse,0,-41);
  $localsha1=sha1($sUpdateResponse);
  if ( $localsha1!=$remotesha1 ) {
    die("Remote $remotesha1 and Local $localsha1 SHA1 differ\n");
  }
  $filename = tempnam('/tmp', 'clientupdate');
  $zfilename=$filename. '.gz';
  $fp=fopen($zfilename,'w');
  fwrite($fp,$sUpdateResponse);
  fclose($fp);
  $zp = gzopen($zfilename, "r");
  $sUpdateFile=gzread($zp,1000000);
  gzclose($zp);
  while ( strlen($sUpdateFile)>0 ) {
    $sUpdateFile=untar($sUpdateFile,$sBaseDir);
  }
  unlink($filename);
  unlink($zfilename);
}