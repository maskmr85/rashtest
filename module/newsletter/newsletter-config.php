<?php
if(!defined('Module-inc') OR !is_array(@$data))
die('<a href="http://help.rashcms.com/modules" target=_blank>RashCMS</a> :: Invalid calling of '.basename (__FILE__));
$itpl = new Rashcms();
$itpl-> load('module/newsletter/block-theme.html');
$tpl->assign('Newsletter',$itpl->dontshowit());
$itpl->assign('theme_url','template/main/'.$config['theme'].'/');
unset($itpl);
	if(isset($_GET['module']) && $data['stat'] == 1)
		if($_GET['module'] == 'newsletter')
			if(isset($_POST['email']))
			{
			$show_posts = false;
			if(empty($_POST['email']))
			$error = $lang['allneed'];
			elseif(!email($_POST['email']))
			$error = $lang['wmail'];
			else
			{
			$ex = $d->getrows("SELECT `mail` FROM `nl` WHERE `mail`='$_POST[email]'",true);
			if($ex != '0')
			$error = $lang['exit_mail'];
			}
			if(!empty($error))
			{
			$error .='<center><a target="_self" href="javascript:history.go(-1);"><font color="#FF0000"><b>['.$lang['back'].']</b></font></a></center>';
			$tpl -> block('Rsh',  array(
			'subject'  => $lang['newsletter'],
			'sub_id'     => 1,
			'sub_link'  => 'nl.php',
			'link'  => 'index.php',
			'title' => $config['sitetitle'],
			'body'  => $error
			)
			);
			}
			else
			{
			$query = $d->Query("INSERT INTO `nl` SET `mail`='$_POST[email]'");
			$msg = ($query) ? $lang['email_sub'] : $lang['Error'];
			$tpl -> block('Rsh',  array(
			'subject'	=> $lang['newsletter'],
			'sub_id'	=> 1,
			'sub_link'	=> 'nl.php',
			'link'  	=> 'index.php',
			'title' 	=> $config['sitetitle'],
			'body'  	=> $msg
			)
			);
			}
			}
?>