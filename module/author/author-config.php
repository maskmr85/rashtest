<?php
if(!defined('Module-inc') OR !is_array(@$data))
die('<a href="http://help.rashcms.com/modules" target=_blank>RashCMS</a> :: Invalid calling of '.basename (__FILE__));
$iq = $d->Query("SELECT `u_id` FROM `permissions` WHERE  `access_admin_area` = '1' ORDER BY `u_id` ASC");
$itpl = new Rashcms();
$itpl-> load('module/author/block-theme.html');
$itpl->assign('theme_url','template/main/'.$config['theme'].'/');
while ($idata = mysql_fetch_array($iq))
	{
	$iiq = $d->Query("SELECT `name`,`showname` FROM `member` WHERE  `u_id` = '$idata[u_id]' LIMIT 1");
	$iiq = $d->fetch($iiq);
	$pinfo = $d->Query("SELECT COUNT(*) as `num` FROM `data` WHERE  `author` = '$idata[u_id]'");
	$pinfo = $d->fetch($pinfo);
	$name = (empty($iiq['showname'])) ? $iiq['name'] : $iiq['showname'];
	$url = ($config['seo'] == '1') ? 'user-'.$idata['u_id'].'.html' : 'index.php?module=cat&userid='.$idata['u_id'];
	$authors[$idata['u_id']] = $name;
	$itpl->block('Authors',array(
	'name'	=>	$name,
	'num'	=>	$pinfo['num'],
	'url'	=>	$url,
	));
	}
	$tpl->assign('Authors',$itpl->dontshowit());
	unset($itpl);
?>