<?php
if(!defined('Module-inc') OR !is_array(@$data))
die('<a href="http://help.rashcms.com/modules" target=_blank>RashCMS</a> :: Invalid calling of '.basename (__FILE__));
$itpl = new Rashcms();
$itpl-> load('module/extra/block-theme.html');
$itpl->assign('theme_url','template/main/'.$config['theme'].'/');
$nopost = false;
	if($data['stat'] != 1)
	{
	$nopost = true;
	$tpl -> block('Rsh',  array(
	'subject'	=> $config['sitetitle'],
	'sub_id'	=> 1,
	'sub_link'	=> 'index.php',
	'link'  	=> 'index.php',
	'title' 	=> $lang['404'],
	'body'  	=> '<div class=error>'.$lang['disabled'].'</div>',
	)
	);
	}
	else
	{
	$q = $d->Query("SELECT * FROM `extra` ORDER BY `id` LIMIT $config[nlast]");
	while($data = $d->fetch($q))
		{
		if(!(($data['users'] == 3 && !$login) || $data['users'] == 2 || ($data['users'] == 1 && $login)))
		continue;
		$url = $config['seo'] !=1 ?  $config['site'].'index.php?module=extra&id='.$data['id'] : $config['site'].'extra-'.$data['id'].'.html';
		$itpl->block('extra',array(
		'title'=>$data['title'],
		'url'=>$url,
		));
		}

	if(isset($_GET['module']))
		if($_GET['module'] == 'extra')
			if(is_numeric(@$_GET['id']))
				{
				$nopost = true;
				$config['nlast'] = intval($config['nlast']);
				$q = $d->Query("SELECT * FROM `extra` WHERE `id`='$_GET[id]' LIMIT $config[nlast]");
				
				if($d->getrows($q)>0)
				{
					$data = $d->fetch($q);
					$url = $config['seo'] !=1 ?  $config['site'].'index.php?module=extra&id='.$data['id'] : $config['site'].'extra-'.$data['id'].'.html';
					$pgtitle = $data['title'];
					$pgtext  = $data['text'];
					if(!(($data['users'] == 3 && !$login) || $data['users'] == 2 || ($data['users'] == 1 && $login)))
					$pgtext = '<div class=error>'.$lang['limited_area'].'<div>';
				}
				else
				{
				$url = 'index.php';
				$pgtitle 	=	$config['sitetitle'];
				$pgtext = '<div class=error>'.$lang['404'].'<div>';
				}

						$tpl -> block('Rsh',  array(
						'subject'	=> $config['sitetitle'],
						'sub_id'	=> 1,
						'sub_link'	=> 'index.php',
						'link'  	=> $url,
						'title' 	=> $pgtitle,
						'body'  	=> $pgtext,
						)
						);
				}
	$tpl->assign('Extra',$itpl->dontshowit());
	}
	if($nopost)
	$show_posts = false;
	unset($itpl);
?>