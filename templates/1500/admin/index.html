<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
	<title>FreePBX administration</title>
	<meta http-equiv="Content-Type" content="text/html">
	<link href="common/mainstyle.css" rel="stylesheet" type="text/css">
	<style type="text/css">
		body {
		  background-image: url(images/shadow-side-background.png);
			background-repeat: repeat-y;
			background-position: left;
		}
	</style>
	<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="favicon.ico">
	<!--[if IE]>
	<link href="common/ie.css" rel="stylesheet" type="text/css">
	<![endif]-->	
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
	<link href="/admin/config.php?handler=file&amp;module=dashboard&amp;file=dashboard.css" rel="stylesheet" type="text/css">

	<script type="text/javascript" src="common/script.js.php"></script>
	<script type="text/javascript" src="common/libfreepbx.javascripts.js" language="javascript"></script>
	
<!--[if IE]>
    <style type="text/css">div.inyourface a{position:absolute;}</style>
<![endif]-->
</head>

<body onload="body_loaded();"   >

<script type="text/javascript">
	function freepbx_show_reload() {
		/*
		$.blockUI($('#reloadBox')[0], { width: '400px' });
		$(document.body).append('<div style="width:100%; height:100%; background:#ccc; opacity:50%;"></div>'); 
		*/
		
		$("#reload_confirm").show();
		$("#reload_reloading").hide();
		$("#reload_response").hide();
		
		freepbx_modal_show('reloadBox', function() {
			// have to use DOM method (not jquery), hence [0]
			$("#reload_confirm_continue_btn")[0].focus();
		});
		
		// add keyboard handler
		$('#reloadBox').keydown( function(e) {
			if ( $('#reload_confirm').css('display') == 'block') {
				// handler for when "confirm" screen is shown
				switch (e.keyCode) {
					case 13: case 10: case 32: // enter, spacebar = yes
						run_reload();
					break;
					case 27:
						freepbx_stop_reload(); // esc = cancel
					break;	
				}
			} else if ($('#reload_response').css('display') == 'block') {
				switch (e.keyCode) {
					case 27: case 13: case 10: case 32: // enter, esc, spacebar = close
						freepbx_stop_reload();
					break;
				}

			}
		});
	}
	function freepbx_stop_reload() {
		freepbx_modal_close('reloadBox');
	}
	
	function run_reload() {
		// figure out which div to slideup (hide): 
		// reload_confirm (normally), or reload_response (on a Retry)
		closeobj = $('#reload_confirm');
		if (closeobj.css('display') == 'none') {
			closeobj = $('#reload_response');
		}

		closeobj.slideUp(150, function() {
			$("#reload_reloading").slideDown(150, function() {
			
				$.ajax({
					type: 'POST',
					url: "/admin/config.php", 
					data: "handler=reload",
					dataType: 'json',
					success: function(data) {
						if (data.status) {
							// successful reload
							$('#need_reload_block').fadeOut();
							freepbx_stop_reload();
						} else {
							// there was a problem
							var responsetext = '<h3>' + data.message + '</h3>  <div class="moreinfo">';

							responsetext += '<p><pre>' + data.retrieve_conf + '</pre></p>';
												
							if (data.num_errors) {
								responsetext += '<p>' + data.num_errors + '  error(s) occured, you should view the notification log on the dashboard or main screen to check for more details.</p>';
							}
						
							responsetext += '</div>' +
							                '<div class="buttons"><a id="reload_response_close_btn" href="#" onclick="freepbx_stop_reload();"><img src="images/cancel.png" height="16" width="16" border="0" alt="Close" />&nbsp;Close</a>'+
							                '&nbsp;&nbsp;&nbsp;<a id="reload_retry_btn" href="#" onclick="run_reload();"><img src="images/arrow_rotate_clockwise.png" height="16" width="16" border="0" alt="Retry" />&nbsp;Retry</a> </div>';

							$('#reload_response').html(responsetext);
	
							$("#reload_reloading").slideUp(150, function() {
								$("#reload_response").slideDown(150);
								$("#reload_response_close_btn")[0].focus();
							});
						}
					},
					error: function(reqObj, status) {
						$('#reload_response').html(
							'<p>Error: Did not receive valid response from server</p>' + 
							'<div class="buttons"><a id="reload_response_close_btn" href="#" onclick="freepbx_stop_reload();"><img src="images/cancel.png" height="16" width="16" border="0" alt="Close" />&nbsp;Close</a></div>'
						);
						$("#reload_reloading").slideUp(150, function() {
							$("#reload_response").slideDown(150);
							$("#reload_response_close_btn")[0].focus();
						});
					}
				});
				
			});
		});
		
	}


</script>
<div id="reloadBox" style="display:none;">
	<div id="reload_confirm">
		<h3>Apply Configuration Changes</h3>
		Reloading will apply all configuration changes made in FreePBX to your PBX engine and make them active.		<ul>
		<li><a id="reload_confirm_continue_btn" href="#" onclick="run_reload();"><img src="images/accept.png" height="16" width="16" border="0" alt="Accept" /> Continue with reload</a></li>
			<li><a href="#" onclick="freepbx_stop_reload();"><img src="images/cancel.png" height="16" width="16" border="0" alt="Cancel" /> Cancel reload and go back to editing</a></li>
		</ul>
	</div>
	
	<div id="reload_reloading" style="display:none;">
		<h3>Please wait, reloading..</h3>
		<img src="images/loading.gif" alt="Loading..." />
	</div>
	
	<div id="reload_response" style="display:none;">
	</div>
</div>
<!-- module process box, used by module admin (page.modules.php) - has to be here because of IE6's z-order stupidity -->
<div id="moduleBox" style="display:none;"></div>

<div id="page">
	<div id="header">
		<div id="freepbx"><a href="http://www.freepbx.org" target="_blank" title="FreePBX"><img src="images/freepbx_large.png" alt="FreePBX" /></a></div>
		<div id="version"><a href="http://www.freepbx.org" target="_blank">FreePBX</a> 2.5.0.1 on <a href="http://mythtv.van.murrell.ca:8081">mythtv.van.murrell.ca</a></div>
		<ul id="metanav">
		<li class="first-current"><a href="config.php">Admin</a></li>
		<li class="noselect"><a href="reports.php">Reports</a></li>
		<li class="noselect"><a href="panel.php">Panel</a></li>
		<li class="noselect"><a target="ari" href="../recordings/index.php">Recordings</a></li>
		<li class="noselect"><a target="help" href="http://www.freepbx.org/freepbx-help-system?freepbx_version=2.5.0.1">Help</a></li>
<li class="last"><a >&nbsp</a></li>		</ul>
		<div id="logo"><a href="http://www.freepbx.org" target="_blank" title="FreePBX"><img src="images/logo.png" alt="FreePBX" /></a></div>

		<div class="attention" id="need_reload_block"><a href="#" onclick="freepbx_show_reload();" class="info"><img src="images/database_gear.png" height="16" width="16" border="0" alt="Reload Required" title="Reload Required" />&nbsp;Apply Configuration Changes<span>You have made changes to the configuration that have not yet been applied. When you are finished making all changes, click on <strong>Apply Configuration Changes</strong> to put them into effect.</span></a></div>

	<div id="login_message">	</div>	</div>

<div id="content">

<!-- begin menu -->
<div id="nav">
<ul id="nav-tabs">
<li><a href="#nav-setup"><span>Setup</span></a></li><li><a href="#nav-tool"><span>Tools</span></a></li><li class="last"><a><span> </span></a></li></ul>
<script type="text/javascript">
 $(function() {
   $('#nav').tabs(1);
 });
</script>
<div id="nav-setup"><ul>		<li class="category">Admin</li>
	<li class="menuitem current"><a href="config.php?type=setup&amp;display=index"  >FreePBX System Status</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=modules"  >Module Admin</a></li>
		<li class="category">Basic</li>
	<li class="menuitem disabled">Devices</li>
	<li class="menuitem disabled">Users</li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=featurecodeadmin"  >Feature Codes</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=general"  >General Settings</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=routing"  >Outbound Routes</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=trunks"  >Trunks</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=ampusers"  >Administrators</a></li>
		<li class="category">Inbound Call Control</li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=did"  >Inbound Routes</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=zapchandids"  >Zap Channel DIDs</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=announcement"  >Announcements</a></li>
	<li class="menuitem disabled">Blacklist</li>
	<li class="menuitem disabled">Day/Night Control</li>
	<li class="menuitem disabled">Follow Me</li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=ivr"  >IVR</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=queueprio"  >Queue Priorities</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=queues"  >Queues</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=ringgroups"  >Ring Groups</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=timeconditions"  >Time Conditions</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=timegroups"  >Time Groups</a></li>
		<li class="category">Internal Options &amp; Configuration</li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=callback"  >Callback</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=conferences"  >Conferences</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=disa"  >DISA</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=languages"  >Languages</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=miscapps"  >Misc Applications</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=miscdests"  >Misc Destinations</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=music"  >Music on Hold</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=pinsets"  >PIN Sets</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=paging"  >Paging and Intercom</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=parking"  >Parking Lot</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=recordings"  >System Recordings</a></li>
	<li class="menuitem"><a href="config.php?type=setup&amp;display=vmblast"  >VoiceMail Blasting</a></li>
</ul></div><div id="nav-tool"><ul>		<li class="category">Admin</li>
	<li class="menuitem current"><a href="config.php?type=tool&amp;display=index"  >FreePBX System Status</a></li>
	<li class="menuitem"><a href="config.php?type=tool&amp;display=modules"  >Module Admin</a></li>
		<li class="category">Support</li>
	<li class="menuitem"><a href="config.php?type=tool&amp;display=logfiles"  >Asterisk Logfiles</a></li>
	<li class="menuitem"><a href="config.php?type=tool&amp;display=irc"  >Online Support</a></li>
	<li class="menuitem"><a href="http://freepbx.org"  target="_blank" >FreePBX Support</a></li>
		<li class="category">System Administration</li>
	<li class="menuitem"><a href="config.php?type=tool&amp;display=manager"  >Asterisk API</a></li>
	<li class="menuitem disabled">Asterisk Phonebook</li>
	<li class="menuitem"><a href="config.php?type=tool&amp;display=backup"  >Backup & Restore</a></li>
	<li class="menuitem"><a href="config.php?type=tool&amp;display=customdests"  >Custom Destinations</a></li>
	<li class="menuitem"><a href="config.php?type=tool&amp;display=customextens"  >Custom Extensions</a></li>
	<li class="menuitem"><a href="config.php?type=tool&amp;display=dundicheck"  >DUNDi Lookup</a></li>
	<li class="menuitem"><a href="config.php?type=tool&amp;display=javassh"  >Java SSH</a></li>
	<li class="menuitem"><a href="config.php?type=tool&amp;display=phpinfo"  >PHP Info</a></li>
	<li class="menuitem"><a href="config.php?type=tool&amp;display=phpagiconf"  >PHPAGI Config</a></li>
		<li class="category">Third Party Addon</li>
	<li class="menuitem"><a href="config.php?type=tool&amp;display=customerdb"  >Customer DB</a></li>
	<li class="menuitem"><a href="config.php?type=tool&amp;display=gabcast"  >Gabcast</a></li>
	<li class="menuitem"><a href="config.php?type=tool&amp;display=inventorydb"  >Inventory</a></li>
	<li class="menuitem"><a href="config.php?type=tool&amp;display=printextensions"  >Print Extensions</a></li>
</ul></div>
</div>

<!-- end menu -->

<div id="wrapper"><div id="background-wrapper">

<div id="left-corner"></div>
<div id="right-corner"></div>


<div id="language">
	
&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<select onchange="javascript:changeLang(this.value)">
		<option value="en_US" selected >English</option>
		<option value="fr_FR"  >Fran&ccedil;ais</option>
		<option value="de_DE"  >Deutsch</option>
		<option value="it_IT"  >Italiano</option>
		<option value="es_ES"  >Espa&ntilde;ol</option>
		<option value="ru_RU"  >Russki</option>
		<option value="pt_PT"  >Portuguese</option>
		<option value="he_IL"  >Hebrew</option>
		<option value="sv_SE"  >Svenska</option>
		</select>

<script type="text/javascript">
<!--
function changeLang(lang) {
	document.cookie='lang='+lang;
	window.location.reload();
}
//-->
</script>

	</div>
	
	
<div class="content">

<noscript>
	<div class="attention"></div>
</noscript>

<!-- begin generated page content  -->
	
	<script language="javascript">
	$(document).ready(function(){
		$.ajaxTimeout( 20000 );
		scheduleInfoUpdate();
		scheduleStatsUpdate();
		
		makeSyslogClickable();
	});
	
	function makeSyslogClickable() {
		$('#syslog h4 span').click(function() {
			$(this).parent().next('div').slideToggle('fast');
		});
	}
	
	var syslog_md5;
	var webserver_fail = 0;
	var info_timer = null;
	var stats_timer = null;

	function updateFailed(reqObj, status) {
		// stop updating 
		clearTimeout(stats_timer);
		stats_timer = null;
		clearTimeout(info_timer);
		info_timer = null;

		webserver_fail += 1;
		webobj = $('#datavalue_Web_Server')

		if (webserver_fail == 1) {
			webobj.text("Timeout");
			webobj.removeClass("graphok");
			webobj.addClass("graphwarn");	
		} else {
			webobj.text("ERROR");
			webobj.removeClass("graphok");
			webobj.removeClass("graphwarn");
			webobj.addClass("grapherror");
		}
		scheduleInfoUpdate();
	}


	function updateInfo() {
		$.ajax({
			type: 'GET',
			url: "/admin/config.php?type=tool&display=index&quietmode=1&info=info&restrictmods=core/dashboard", 
			dataType: 'json',
			success: function(data) {
				$('#procinfo').html(data.procinfo);
				$('#sysinfo').html(data.sysinfo);
				// only update syslog div if the md5 has changed
				if (syslog_md5 != data.syslog_md5) {
					$('#syslog').html(data.syslog);
					makeSyslogClickable();
					syslog_md5 = data.syslog_md5;
				}

				// webserver is ok
				webserver_fail = 0;

				scheduleInfoUpdate();
				if (stats_timer == null) {
					// restart stats updates
					scheduleStatsUpdate();
				}
			},
			error: updateFailed
		});
	}
	function scheduleInfoUpdate() {
		info_timer = setTimeout('updateInfo();',30000);
	}
	
	
	function updateStats() {
		$.ajax({
			type: 'GET',
			url: "/admin/config.php?type=tool&display=index&quietmode=1&info=stats&restrictmods=core/dashboard", 
			dataType: 'json',
			success: function(data) {
				$('#sysstats').html(data.sysstats);
				$('#aststats').html(data.aststats);
				scheduleStatsUpdate();
			},
			error: updateFailed
		});
	}
	function scheduleStatsUpdate() {
		stats_timer = setTimeout('updateStats();',6000);
	}
	
	
	function changeSyslog(showall) {
		$('#syslog_button').text('loading...');
		$('#syslog').load("/admin/config.php?type=tool&display=index&quietmode=1&restrictmods=core/dashboard&info=syslog&showall="+showall,{}, function() {
			makeSyslogClickable();
		});
	}

	function hide_notification(domid, module, id) {
		$('#'+domid).fadeOut('slow');
		$.post('config.php', {display:'index', quietmode:1, info:'syslog_ack', module:module, id:id, restrictmods:'core/dashboard'});
	}
	function delete_notification(domid, module, id) {
		$('#'+domid).fadeOut('slow');
		$.post('config.php', {display:'index', quietmode:1, info:'syslog_delete', module:module, id:id, restrictmods:'core/dashboard'});
	}
	</script>

	<h2>FreePBX System Status</h2>
	</div>
	<div id="dashboard">
	<div id="sysinfo-left"><div id="syslog" class="infobox"><h3>FreePBX Notices</h3><ul><li id="notify_item_core_MQGPC"  class="notify_error"><div><h4 class="syslog_text"><span><img src="images/notify_error.png" alt="Error" title="Error" width="16" height="16" border="0" />&nbsp;Magic Quotes GPC</span><div class="notification_buttons"><a class="notify_ignore_btn" title="Ignore this" onclick="hide_notification('notify_item_core_MQGPC', 'core', 'MQGPC');"><img src="/admin/images/notify_delete.png" width="16" height="16" border="0" alt="Ignore this" /></a></div></h4><div class="syslog_detail">You have magic_quotes_gpc enabled in your php.ini, http or .htaccess file which will cause errors in some modules. FreePBX expects this to be off and runs under that assumption<br/><span>Added  ago<br/>(core.MQGPC)</span></div></div></li><li id="notify_item_core_AMPDBPASS"  class="notify_warning"><div><h4 class="syslog_text"><span><img src="images/notify_warning.png" alt="Warning" title="Warning" width="16" height="16" border="0" />&nbsp;Default SQL Password Used</span><div class="notification_buttons"><a class="notify_ignore_btn" title="Ignore this" onclick="hide_notification('notify_item_core_AMPDBPASS', 'core', 'AMPDBPASS');"><img src="/admin/images/notify_delete.png" width="16" height="16" border="0" alt="Ignore this" /></a></div></h4><div class="syslog_detail">You are using the default SQL password that is widely known, you should set a secure password<br/><span>Added  ago<br/>(core.AMPDBPASS)</span></div></div></li><li id="notify_item_core_AMPMGRPASS"  class="notify_warning"><div><h4 class="syslog_text"><span><img src="images/notify_warning.png" alt="Warning" title="Warning" width="16" height="16" border="0" />&nbsp;Default Asterisk Manager Password Used</span><div class="notification_buttons"><a class="notify_ignore_btn" title="Ignore this" onclick="hide_notification('notify_item_core_AMPMGRPASS', 'core', 'AMPMGRPASS');"><img src="/admin/images/notify_delete.png" width="16" height="16" border="0" alt="Ignore this" /></a></div></h4><div class="syslog_detail">You are using the default Asterisk Manager password that is widely known, you should set a secure password<br/><span>Added  ago<br/>(core.AMPMGRPASS)</span></div></div></li></ul><div id="syslog_button"><a href="#" onclick="changeSyslog(1);">show all</a></div><script type="text/javascript"> syslog_md5 = "d4b962b4ce49410b0982dd04fcc0604e"; </script></div><div id="aststats" class="infobox"><h3>FreePBX Statistics</h3><div class="databox graphbox" style="width:400px;" title="Total active calls: 0 / 0 (0%)">
 <div class="bargraph graphok" style="width:0px;"></div>
 <div class="dataname">Total active calls</div>
 <div class="datavalue">0</div>
</div>
<div class="databox graphbox" style="width:400px;" title="Internal calls: 0 / 0 (0%)">
 <div class="bargraph graphok" style="width:0px;"></div>
 <div class="dataname">Internal calls</div>
 <div class="datavalue">0</div>
</div>
<div class="databox graphbox" style="width:400px;" title="External calls: 0 / 0 (0%)">
 <div class="bargraph graphok" style="width:0px;"></div>
 <div class="dataname">External calls</div>
 <div class="datavalue">0</div>
</div>
<div class="databox graphbox" style="width:400px;" title="Total active channels: 0 / 0 (0%)">
 <div class="bargraph graphok" style="width:0px;"></div>
 <div class="dataname">Total active channels</div>
 <div class="datavalue">0</div>
</div>
<h4>FreePBX Connections</h4></div><div id="sysinfo" class="infobox"><h3>Uptime</h3></br><table><tr><th>System Uptime:</th><td>1 week, 5 days, 58 minutes</td></tr><tr><th>Asterisk Uptime:</th><td>0 minutes</td></tr><tr><th>Last Reload:</th><td>0 minutes</td></tr></table></div></div><div id="sysinfo-right"><div id="sysstats" class="infobox"><h3>System Statistics</h3><h4>Processor</h4><div class="databox" style="width:200px;">
 <div class="dataname">Load Average</div>
 <div class="datavalue"><a href="#" title="Load Average: 0.06">0.06</a></div>
</div>
<div class="databox graphbox" style="width:200px;" title="CPU: 1.98 / 100 (2%)">
 <div class="bargraph graphok" style="width:4px;"></div>
 <div class="dataname">CPU</div>
 <div class="datavalue">2%</div>
</div>
<h4>Memory</h4><div class="databox graphbox" style="width:200px;" title="App Memory: 537.30MB / 878.56640625MB (61%)">
 <div class="bargraph graphok" style="width:122px;"></div>
 <div class="dataname">App Memory</div>
 <div class="datavalue">61%</div>
</div>
<div class="databox graphbox" style="width:200px;" title="Swap: 44.08MB / 1953.17578125MB (2%)">
 <div class="bargraph graphok" style="width:4px;"></div>
 <div class="dataname">Swap</div>
 <div class="datavalue">2%</div>
</div>
<h4>Disks</h4><div class="databox graphbox" style="width:200px;" title="/: 4.61GB / 9.24GB (50%)">
 <div class="bargraph graphok" style="width:100px;"></div>
 <div class="dataname">/</div>
 <div class="datavalue">50%</div>
</div>
<div class="databox graphbox" style="width:200px;" title="/var/run: 0.00GB / 0.43GB (0%)">
 <div class="bargraph graphok" style="width:0px;"></div>
 <div class="dataname">/var/run</div>
 <div class="datavalue">0%</div>
</div>
<div class="databox graphbox" style="width:200px;" title="/var/lock: 0.00GB / 0.43GB (0%)">
 <div class="bargraph graphok" style="width:0px;"></div>
 <div class="dataname">/var/lock</div>
 <div class="datavalue">0%</div>
</div>
<div class="databox graphbox" style="width:200px;" title="/dev: 0.00GB / 0.43GB (0%)">
 <div class="bargraph graphok" style="width:0px;"></div>
 <div class="dataname">/dev</div>
 <div class="datavalue">0%</div>
</div>
<div class="databox graphbox" style="width:200px;" title="/dev/shm: 0.00GB / 0.43GB (0%)">
 <div class="bargraph graphok" style="width:0px;"></div>
 <div class="dataname">/dev/shm</div>
 <div class="datavalue">0%</div>
</div>
<div class="databox graphbox" style="width:200px;" title="/lib/modules/2.6.24-19-generic/volatile: 0.04GB / 0.43GB (9%)">
 <div class="bargraph graphok" style="width:18px;"></div>
 <div class="dataname">/lib/modules/2.6.24-19-..</div>
 <div class="datavalue">9%</div>
</div>
<div class="databox graphbox" style="width:200px;" title="/mythtv: 105.01GB / 698.51GB (15%)">
 <div class="bargraph graphok" style="width:30px;"></div>
 <div class="dataname">/mythtv</div>
 <div class="datavalue">15%</div>
</div>
<div class="databox graphbox" style="width:200px;" title="/mythtv/recordings: 36.46GB / 137.76GB (26%)">
 <div class="bargraph graphok" style="width:52px;"></div>
 <div class="dataname">/mythtv/recordings</div>
 <div class="datavalue">26%</div>
</div>
<h4>Networks</h4><div class="databox" style="width:200px;">
 <div class="dataname">eth0 receive</div>
 <div class="datavalue"><a href="#" title="eth0 receive: 0.00 KB/s">0.00 KB/s</a></div>
</div>
<div class="databox" style="width:200px;">
 <div class="dataname">eth0 transmit</div>
 <div class="datavalue"><a href="#" title="eth0 transmit: 0.00 KB/s">0.00 KB/s</a></div>
</div>
</div><div id="procinfo" class="infobox"><h3>Server Status</h3><div class="databox statusbox" style="width:200px;">
 <div class="dataname">Asterisk</div>
 <div id="datavalue_Asterisk" class="datavalue grapherror"><a href="#" title="Asterisk is not running, this is a critical service!">ERROR</a></div>
</div>
<div class="databox statusbox" style="width:200px;">
 <div class="dataname">Op Panel</div>
 <div id="datavalue_Op_Panel" class="datavalue graphwarn"><a href="#" title="FOP Operator Panel Server is not running, you will not be able to use the operator panel, but the system will run fine without it.">Warn</a></div>
</div>
<div class="databox statusbox" style="width:200px;">
 <div class="dataname">MySQL</div>
 <div id="datavalue_MySQL" class="datavalue graphok"><a href="#" title="MySQL Server is running">OK</a></div>
</div>
<div class="databox statusbox" style="width:200px;">
 <div class="dataname">Web Server</div>
 <div id="datavalue_Web_Server" class="datavalue graphok"><a href="#" title="Web Server is running">OK</a></div>
</div>
<div class="databox statusbox" style="width:200px;">
 <div class="dataname">SSH Server</div>
 <div id="datavalue_SSH_Server" class="datavalue graphok"><a href="#" title="SSH Server is running">OK</a></div>
</div>
</div><div style="clear:both;"></div></div></div><div class="content"><!-- end generated page content -->

</div> <!-- .content -->

<div id="footer">
	<hr />
	<a target="_blank" href="http://www.freepbx.org"><img id="footer_logo" src="images/freepbx_small.png" alt="FreePBX&reg;"/></a><h3>Freedom to Connect<sup>&reg</sup></h3>		<a href="http://www.freepbx.org" target="_blank">FreePBX</a> is a registered trademark of <a href="http://www.freepbx.org/copyright.html" target="_blank">Atengo, LLC.</a><br/>
		<a href="http://www.freepbx.org" target="_blank">FreePBX 2.5.0</a> is licensed under <a href="http://www.gnu.org/copyleft/gpl.html" target="_blank">GPL</a></div>

</div></div> <!-- background-wrapper, background -->
</div> <!-- content -->
</div> <!-- page -->
</body>
</html>
