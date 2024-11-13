<?php
if(!defined('Module-inc') OR !is_array(@$data))
die('<a href="http://help.rashcms.com/modules" target=_blank>RashCMS</a> :: Invalid calling of '.basename (__FILE__));
$itpl = new Rashcms();
$itpl-> load('module/member/block-theme.html');
$itpl->assign('theme_url','template/main/'.$config['theme'].'/');
	if($config['member_area'] != '1')
	{
	$itpl-> assign('Guest',1);
	$tpl->assign('Members_Area',$itpl->dontshowit());
	}
	else
	{
	define('reg_module_rashcms',1);
		if(!isset($login))
		$login = ($config['member_area'] == '1') ? $user->checklogin() : false;
		if($login)
		{
		$name = empty($info['showname']) ? $info['name'] : $info['showname'];
		$avatar = (empty($info['avatar'])) ? 'rashcms/images/no_avatar.png' : $info['avatar'];
			$itpl-> assign('Member',1);
			$itpl->assign(array(
			'u_id' 		=> $info['u_id'], 
			'name' 		=> $name, 
			'date' 		=> mytime($config['dtype'],$info['date'],$config['dzone']), 
			'avatar' 	=> $avatar, 
			'userposts' => $info['userposts'], 
			'msg' 		=> $info['ur'], 
			));
		}
		else
		{
		$itpl-> assign('Guest',1);
		if(@$_SESSION['tries'] >= $config['tries'])
		$itpl->assign('LoginSec',1);
		}
	$tpl->assign('Members_Area',$itpl->dontshowit());
	unset($itpl);
	$_GET['action'] = (empty($_GET['action'])) ? 'none' : $_GET['action'];
	switch($_GET['action'])
		{
		case 'register':
		include('register.php');
		break;
		case 'login':
		include('login.php');
		break;
		case 'logout':
		$user->logout();
		HEADER("LOCATION: index.php");
		die();
		break;
		case 'list':
		include('list.php');
		break;
		case 'profile':
		include('profile.php');
		case 'none':
		break;
		include('profile.php');
		break;
		case 'inbox':
		include('inbox.php');
		break;
		case 'outbox':
		include('outbox.php');
		break;
		case 'sendpm':
		include('sendpm.php');
		break;
		case 'forget':
		include('forget.php');
		break;
		default:
		require_once('lang.fa.php');
		$show_posts = false;
		$itpl = new Rashcms();
		$itpl-> load('module/member/profile.html');
		$itpl->assign(array('Error'=>1,'Msg'=>$lang['404']));
		$tpl -> block('Rsh',  array(
			'subject'	=> $config['sitetitle'],
			'sub_id'	=> 1,
			'sub_link'	=> 'index.php',
			'link'  	=> 'index.php?module=member',
			'title' 	=> $lang_register['member_area'],
			'body'  	=> $itpl->dontshowit(),
			)
			);
		}
	}
	if(isset($lang_register))
	unset($lang_register);
?>