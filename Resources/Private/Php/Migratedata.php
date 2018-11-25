<?php

/*

created by Michael Perlbach (info@mikelmade.de);

This is a script migrating data from the old extension MW Imagemap
to the newer MM Imagemap. It should be used initially after the first
installation of  MM Imagemap - and, of course, it should be used only
once. For using it, comment line 15 of this script - and after use I
recommend to uncomment it again.

*/

exit;
$conf = require_once('../../../../../LocalConfiguration.php');

$dbc = $conf['DB']['Connections']['Default'];

$database = mysqli_connect($dbc['host'], $dbc['user'], $dbc['password'], $dbc['dbname']);
mysqli_query($database, "SET NAMES 'utf8'", MYSQLI_USE_RESULT);


$sql = 'select * from tx_mwimagemap_map';
$result = mysqli_query($database, $sql, MYSQLI_USE_RESULT);
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach($data as $item){
	$sql = 'insert into tx_mmimagemap_domain_model_map
	(uid,name,imgfile,altfile,folder) values
	('.(int)$item['id'].',
	\''.mysqli_real_escape_string($database,$item['name']).'\',
	\''.mysqli_real_escape_string($database,$item['file']).'\',
	\''.mysqli_real_escape_string($database,$item['alt_file']).'\',
	\''.mysqli_real_escape_string($database,$item['folder']).'\');';
	$result = mysqli_query($database, $sql, MYSQLI_USE_RESULT);
}

$sql = 'select * from tx_mwimagemap_point';
$result = mysqli_query($database, $sql, MYSQLI_USE_RESULT);
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach($data as $item){
	$sql = 'insert into tx_mmimagemap_domain_model_point
	(uid,areaid,num,x,y) values
	('.(int)$item['id'].',
	'.(int)$item['aid'].',
	'.(int)$item['num'].',
	'.(int)$item['x'].',
	'.(int)$item['y'].');';
	$result = mysqli_query($database, $sql, MYSQLI_USE_RESULT);
}

$sql = 'select * from tx_mwimagemap_area';
$result = mysqli_query($database, $sql, MYSQLI_USE_RESULT);
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach($data as $item){
	$sql = 'insert into tx_mmimagemap_domain_model_area
	(uid,mapid,areatype,arealink,description,color,param,febordercolor,fevisible,feborderthickness,fealtfile) values
	('.(int)$item['id'].',
	'.(int)$item['mid'].',
	\''.mysqli_real_escape_string($database,$item['link']).'\',
	\''.mysqli_real_escape_string($database,$item['description']).'\',
	\''.mysqli_real_escape_string($database,str_replace('#','',$item['color'])).'\',
	\''.mysqli_real_escape_string($database,$item['param']).'\',
	\''.mysqli_real_escape_string($database,str_replace('#','',$item['fe_bordercolor'])).'\',
	'.(int)$item['fe_visible'].',
	'.(int)$item['fe_borderthickness'].',
	\''.mysqli_real_escape_string($database,$item['fe_altfile']).'\');';
	$result = mysqli_query($database, $sql, MYSQLI_USE_RESULT);
}



$sql = 'select * from tx_mwimagemap_bcolors';
$result = mysqli_query($database, $sql, MYSQLI_USE_RESULT);
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach($data as $item){
	$sql = 'insert into tx_mmimagemap_domain_model_bcolors
	(uid,mapid,colorname,color) values
	('.(int)$item['id'].',
	'.(int)$item['mid'].',
	\''.mysqli_real_escape_string($database,$item['colorname']).'\',
	\''.mysqli_real_escape_string($database,str_replace('#','',$item['color'])).'\');';
	$result = mysqli_query($database, $sql, MYSQLI_USE_RESULT);
}


$sql = 'select * from tx_mwimagemap_contentpopup';
$result = mysqli_query($database, $sql, MYSQLI_USE_RESULT);
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach($data as $item){
	$sql = 'insert into tx_mmimagemap_domain_model_contentpopup
	(uid,areaid,contentid,popupwidth,popupheight,popupx,popupy,popupbordercolor,popupbackgroundcolor,popupborderwidth,active) values
	('.(int)$item['id'].',
	'.(int)$item['aid'].',
	'.(int)$item['content_id'].',
	'.(int)$item['popup_width'].',
	'.(int)$item['popup_height'].',
	'.(int)$item['popup_x'].',
	'.(int)$item['popup_y'].',
	\''.mysqli_real_escape_string($database,str_replace('#','',$item['popup_bordercolor'])).'\',
	\''.mysqli_real_escape_string($database,str_replace('#','',$item['popup_backgroundcolor'])).'\',
	'.(int)$item['popup_borderwidth'].',
	'.(int)$item['active'].'\');';
	$result = mysqli_query($database, $sql, MYSQLI_USE_RESULT);
}

// copy all overlay images
$abspath = explode('typo3conf',__FILE__);
$oldpath = $abspath[0].'uploads/tx_mwimagemap';
$newpath = $abspath[0].'uploads/tx_mmimagemap';

$dh = opendir($oldpath)) {
  while (($file = readdir($dh)) !== false) {
		if($file != '.' && $file != '..'){
			copy($oldpath.'/'.$file,$newpath.'/'.$file);
		}
	}
}
closedir($oldpath);





