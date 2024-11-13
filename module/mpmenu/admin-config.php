<?php
if(!defined('module_admin_area'))
die('invalid access');
if(!isset($permissions['access_admin_area']))
die('invalid access');
if($permissions['access_admin_area'] != '1')
die('invalid access');		
$information = array(
'name'			=>'منو',
'provider'		=>'رضا شاهرخيان',
'providerurl'	=>'http://rashcms.com',
'install'		=>true,
'uninstall'		=>true,
'activate'		=>true,
'inactivate'	=>true,
);
$tpl->assign('first','');
if(defined('actions'))
{
function defaultop()
{
print_msg('براي اين ماژول تنظيم خاصي در نظر گرفته نشده است.<br>براي فعال/غير فعال سازي ماژول مي توانيد از بخش "ماژول ها" اقدام كنيد.','Info');
}
function inactivateop()
{
global $d;
$d->Query("UPDATE `module` SET `stat`='0' WHERE `name`='mpmenu' LIMIT 1");
print_msg('ماژول با موفقيت غير فعال شد.','Success');
}
function activateop()
{
global $d;
$d->Query("UPDATE `module` SET `stat`='1' WHERE `name`='mpmenu' LIMIT 1");
print_msg('ماژول با موفقيت فعال شد.','Success');
}
function installop()
{
	global $d;
	$q = $d->getrows("SELECT `stat` FROM `module` WHERE `name`='mpmenu' LIMIT 1",true);
	if($q > 0)
	print_msg('اين ماژول قبلا نصب شده است.','Info');
	else
	{
		$oid = $d->getmax('oid','menus');
		$q = $d->Query("INSERT INTO `menus` (`oid`, `name`, `title`, `url`, `type`) VALUES ('$oid', 'mpmenu', '$information[name]', 'menu.php', '0')");
		$q = $d->Query("INSERT INTO `module` (`name`, `title`, `stat`) VALUE ('mpmenu', '$information[name]', '0')");
		$d->Query("CREATE TABLE IF NOT EXISTS `mpmenu` (
		  `id` int(10) NOT NULL auto_increment,
		  `name` varchar(256) NOT NULL,
		  `enname` varchar(256) NOT NULL,
		  `sub` int(10) NOT NULL,
		  `img` varchar(255) default NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"
		);
		print_msg('ماژول با موفقيت نصب شد.','Success');
	}
}
function uninstallop()
{
global $d;
$q = $d->getrows("SELECT `stat` FROM `module` WHERE `name`='mpmenu' LIMIT 1",true);
if($q <= 0)
print_msg('اين ماژول نصب نشده است يا استاندارد نيست.','Info');
else
	{
	$q = $d->Query("DELETE FROM `menus` WHERE `name`='mpmenu' LIMIT 1");
	$q = $d->Query("DELETE FROM `module` WHERE `name`='mpmenu' LIMIT 1");
	$d->Query("DROP TABLE IF EXISTS `mpmenu`;");
	print_msg('ماژول با موفقيت حذف شد.','Success');
	}
}
function print_msg($msg,$type)
{
global $tpl,$information;
$tpl->assign(array(
'module_name' 	=> $information['name'],
$type			=>1,
'msg' 			=> $msg,
));
}
}
?>