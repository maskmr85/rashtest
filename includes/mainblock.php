<?php
	if(!$login)
	$tpl-> assign('Guest',1);
	else
	$tpl-> assign('Member',1);
	//0:inactive|9:active - but need to be called
	if(!isset($_GET['module']))
	$q = $d->Query("SELECT * FROM `module` WHERE `stat`!='0' AND `stat`!='9'");
	else
	$q = $d->Query("SELECT * FROM `module` WHERE `stat`!='0' AND (`stat`!='9' OR `name`='$_GET[module]')");
	define('Module-inc',true);
	while($data = $d->fetch($q))
		{
		$qtmp = $q;
		$modules[$data['name']] = true;
		$name = safe(safeurl($data['name'],true));
		if(ctype_alnum($name))
			if(is_dir('module/'.$name))
				if(file_exists('module/'.$name.'/'.$name.'-config.php'))
				include_once('module/'.$name.'/'.$name.'-config.php');
		$q = $qtmp;
		}
	$q = $d->Query("SELECT * FROM `block` ORDER BY `order`");
	$fpos = array('1'=>'top','2'=>'down','3'=>'right','4'=>'left');
	while($data = $d->fetch($q))
	{
	if($data['module'] !='none')
		if(!isset($modules[$data['module']]))
			continue;
	//1 : top
		if(($data['users'] == 3 && !$login) || $data['users'] == 2 || ($data['users'] == 1 && $login))
		{
		$tpl->block('Rash'.$fpos[$data['pos']],array(
		'title'=>  $data['name'],
        'content'   => $data['text'],
		));
		}

	}
	$tpl -> assign(array(
	'siteurl'		=>	$config['site'],
	));
?>