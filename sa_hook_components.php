<?php

/*
Added parse error catching regardless of error reporting level
Fixed issue with backtracing object classes

Added force on global
*/

/*
* @Plugin Name: sa_hook_components
* @Description: Executes components on hooks
* @Version: 0.1
* @Author: Shawn Alverson
* @Author URI: http://tablatronix.com/getsimple-cms/sa-hook-components/
*/


$PLUGIN_ID = "sa_development";
$PLUGINPATH = $SITEURL.'plugins/sa_hook_components/';
$sa_url = 'http://tablatronix.com/getsimple-cms/sa-dev-plugin/';

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");			// Plugin File
$sa_pname = 		'SA Hook Components';    	//Plugin name
$sa_pversion =	'0.1'; 		       	      	//Plugin version
$sa_pauthor = 	'Shawn Alverson';       	//Plugin author
$sa_purl = 			$sa_url;									//author website
$sa_pdesc =			'Execute components on hooks'; 	//Plugin description
$sa_ptype =			'';                       //page type - on which admin tab to display
$sa_pfunc =			'';                       //main function (administration)
	
# register plugin
register_plugin($thisfile,$sa_pname,$sa_pversion,$sa_pauthor,$sa_url,$sa_pdesc,$sa_ptype,$sa_pfunc);

require_once('sa_hook_components/hooks.php');

function sa_hc_init(){
	global $components,$FRONT_END_HOOKS,$BACK_END_HOOKS;
	if (!$components) {
		$components = sa_hc_get_component();
	}

	// loop components for hook names
	$compnames = $components->xpath('item/title');

	foreach($compnames as $compname){
		$compname = (string)$compname;
		
		if(strpos($compname,'hook_') === false) continue;		
		$compname = substr($compname,5);
		
		if(isset($FRONT_END_HOOKS[$compname])){
			# _debugLog((string)$compname,$FRONT_END_HOOKS[(string)$compname]);
      add_action($compname,'get_component',array('hook_'.$compname));	
		}	
		else if(isset($BACK_END_HOOKS[(string)$compname])){
			// theme_functions is not loaded on backend		
			# _debugLog((string)$compname,$BACK_END_HOOKS[(string)$compname]);
      add_action($compname,'sa_hc_exec_component',array('hook_'.$compname));	
		}			
	}
	
}	

function sa_hc_get_component($id = null){
	$file 		= "components.xml";
	$path 		= GSDATAOTHERPATH;
	$data = getXML($path . $file);
	
	if(!$id){
		return $data; // return all components
	}
	
	# _debugLog($data);
	
	$component = $data->xpath('item/title[.="'.$id.'"]/parent::*'); // return the componenet requested	
	if(!isset($component)) return null;
	return $component;
}

function sa_hc_exec_component($id){
	_debugLog('exec_component '.$id);
	$component = sa_hc_get_component($id);
	# _debugLog($component);
	eval("?>" . strip_decode($component[0]->value) . "<?php "); 
}

sa_hc_init();

?>
