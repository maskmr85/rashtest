<?php
if(!defined('module_admin_area'))
die('invalid access');
if(!isset($permissions['access_admin_area']))
die('invalid access');
if($permissions['access_admin_area'] != '1')
die('invalid access');		
$information = array(
'name'			=>'سيستم كاربري',
'provider'		=>'رضا شاهرخيان',
'providerurl'	=>'http://rashcms.com',
'install'		=>false,
'uninstall'		=>false,
'activate'		=>false,
'inactivate'	=>false,
);
$tpl->assign('first','');
if(defined('actions'))
{
function defaultop()
{
print_msg('تنظيمات اين ناژول از بخش كاربري قابل دسترسي مي باشند.','Info');
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