<?php
if(!defined('Module-inc') OR !is_array(@$data))
die('<a href="http://help.rashcms.com/modules" target=_blank>RashCMS</a> :: Invalid calling of '.basename (__FILE__));
$itpl = new Rashcms();
$itpl-> load('module/simplelink/block-theme.html');
$itpl->assign('theme_url','template/main/'.$config['theme'].'/');
	if($data['stat'] == 1 || $data['stat'] == 2)
	{
	$iq = $d->Query("SELECT * FROM `link` ORDER BY `id` LIMIT $config[nlast]");
	if( $data['stat'] == 2)
		$itpl->assign('Hits',1);
	while($idata = $d->fetch($iq))
		{
		$url = ($data['stat'] ==1) ? $idata['url'] : $config['site'].'module/simplelink/action/redirect/id-'.$idata['id'].'.html';
		$url = ($config['seo'] !=1 && $data['stat'] !=1) ? $config['site'].'index.php?module=simplelink&action=redirect&n='.$idata['id']: $url;
		$itpl->block('links',array(
		'title'=>$idata['title'],
		'desc'=>$idata['des'],
		'url'=>$url,
		'clicks'=>$idata['hits'],
		));
		}
   	}
	$tpl->assign('links',$itpl->dontshowit());
	unset($itpl);
	if(isset($_GET['module']) && $data['stat'] == 2)
		if($_GET['module'] == 'simplelink')
			if(@$_GET['action'] == 'redirect' && is_numeric(@$_GET['n']))
			{
			$d->Query("UPDATE `link` SET `hits`=`hits`+1 WHERE `id`='$_GET[n]' LIMIT 1");
			if($d->affected() <= 0)
				{
				HEADER("LOCATION: index.php");
				die();
				}
			$r = $d->getrowvalue("url","SELECT `url` FROM `link` WHERE `id`='$_GET[n]' LIMIT 1",true);
			if(empty($r))
				{
				HEADER("LOCATION: index.php");
				die();
				}
			HEADER("LOCATION: $r");		
			}
?>