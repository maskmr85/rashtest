<?php
if(!defined('Module-inc') OR !is_array(@$data))
die('<a href="http://help.rashcms.com/modules" target=_blank>RashCMS</a> :: Invalid calling of '.basename (__FILE__));
$itpl = new Rashcms();
$itpl-> load('module/member/inbox.html');
$itpl->assign('theme_url','template/main/'.$config['theme'].'/');
include('lang.fa.php');
$show_posts = false;
if(!$login)
$itpl->assign(array('Error'=>1,'Msg'=>$lang['limited_area']));
else{
	$itpl->assign('User',1);
	@$type			= ($type !='DESC' && $type != 'ASC') ? 'ASC' : $type;
	@$From			= (!is_numeric($From)) ? 1 : abs($From);
	@$RPP			= (!is_numeric($RPP)) ? 10 : abs($RPP);
	@$CurrentPage	= (!is_numeric($CurrentPage)) ? 1 : abs($CurrentPage);
	$_GET['task']	= empty($_GET['task']) ? 'none' : $_GET['task'];
	switch($_GET['task'])
	{
	case 'read':
	if(isset($_GET['id']))
		if(is_numeric($_GET['id']))
			{
				$u_id = $user->info['u_id'];
				$q = $d->Query("SELECT `msg`.*,`member`.`name` AS `sendername`,`member`.`user` AS `senderuser` FROM `msg`,`member` WHERE `member`.`u_id` = `msg`.`re_id` AND `msg`.`re_id` ='".$u_id."'  AND `msg`.`pm_id`='".$_GET['id']."'  LIMIT 1");
				if($d->getrows($q) != '1')
				$itpl->assign(array('Error'=>1,'Msg'=>$lang_register['wpmid']));
				else
				{
				$data = $d->fetch($q);
				$q = $d->Query("SELECT `member`.`name` AS `sendername`,`member`.`user` AS `senderuser` FROM `member` WHERE `member`.`u_id` = '$data[send_id]'  LIMIT 1");
				$udata = $d->fetch($q);
				$q = $d->Query("UPDATE `msg` SET `reade`='1' WHERE `reade`='0' AND `pm_id`='$_GET[id]' AND `msg`.`re_id` ='".$u_id."' LIMIT 1");
				$itpl->assign('Read',1);
				$itpl -> assign(array(
				'title' 	=> safe($data['title']),
				'text' 		=> nl2br($data['text']),
				'author' 	=> safe($udata['sendername']),
				'senduser'	=> safe($udata['senderuser']),
				'id'		=> intval($data['pm_id']),
				)
				);
				}
			}
			else
			$itpl->assign(array('Error'=>1,'Msg'=>$lang_register['wpmid']));
			else
			$itpl->assign(array('Error'=>1,'Msg'=>$lang_register['wpmid']));

	break;
	case 'delete';
		if(isset($_GET['id']))
		if(is_numeric($_GET['id']))
			{
				$u_id = $user->info['u_id'];
				$q = $d->Query("SELECT `msg`.`reade`,`member`.`u_id` FROM `msg`,`member` WHERE `member`.`u_id` = `msg`.`re_id` AND `msg`.`re_id` ='".$u_id."'  AND `msg`.`pm_id`='".$_GET['id']."'  LIMIT 1");
				if($d->getrows($q) != '1')
				$itpl->assign(array('Error'=>1,'Msg'=>$lang_register['wpmid']));
				else
				{
				$q = $d->Query("DELETE FROM `msg` WHERE `pm_id`='$_GET[id]' AND `msg`.`re_id` ='".$u_id."' LIMIT 1");
				$itpl->assign(array('Succeed'=>1,'Msg'=>$lang['pm_deleted']));
				}
			}
			else
			$itpl->assign(array('Error'=>1,'Msg'=>$lang_register['wpmid']));
			else
			$itpl->assign(array('Error'=>1,'Msg'=>$lang_register['wpmid']));
	break;
	case 'none':
	$itpl->assign('Inbox',1);
	$tpl->assign('Page',1);
	$u_id = $user->info['u_id'];
	$q = $d->Query("SELECT `msg`.*,`member`.`name` AS `sendername`,`member`.`user` AS `senderuser` FROM `msg`,`member` WHERE `member`.`u_id` = `msg`.`send_id` AND `msg`.`re_id` ='".$u_id."'  ORDER BY `msg`.`pm_id` ".$type." LIMIT ".$From.",".$RPP);
	while($data = $d->fetch($q))
	{
	$title = ($data['reade'] == '0') ? '<b>'.$data['title'].'</b>' : $data['title'];
	$itpl -> block('Rsh',  array(
        'title' 	=> $title,
        'author' 	=> $data['sendername'],
        'senduser'	=> $data['senderuser'],
        'id'		=> $data['pm_id'],
        )
        );

	}
	$t_m = $d->getrows("SELECT `msg`.`pm_id`,`member`.`u_id` FROM `msg`,`member` WHERE `member`.`u_id` = `msg`.`send_id` AND `msg`.`re_id` ='".$u_id."' ",true);
	rashpage($t_m,$RPP,5,$CurrentPage,$tpl,'pages','index.php?module=member&action=inbox&');
	break;
	default:
	$itpl->assign(array('Error'=>1,'Msg'=>$lang['404']));
	}
}
$tpl -> block('Rsh',  array(
			'subject'	=> $config['sitetitle'],
			'sub_id'	=> 1,
			'sub_link'	=> 'index.php',
			'link'  	=> 'index.php?module=member',
			'title' 	=> $lang_register['inbox'],
			'body'  	=> $itpl->dontshowit(),
			)
			);

?>