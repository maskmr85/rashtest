<?php
if(!defined('Module-inc') OR !is_array(@$data))
die('<a href="http://help.rashcms.com/modules" target=_blank>RashCMS</a> :: Invalid calling of '.basename (__FILE__));
$itpl = new Rashcms();
$itpl-> load('module/member/profile.html');
include('lang.fa.php');
$show_posts = false;
$itpl->assign('theme_url','template/main/'.$config['theme'].'/');
if(!$login)
$itpl->assign(array('Error'=>1,'Msg'=>$lang['limited_area']));
else{
	$itpl->assign('User',1);
	$_GET['task'] = empty($_GET['task']) ? 'none' : $_GET['task'];
	switch($_GET['task'])
	{
	case 'edit':
	$itpl->assign(array(
	'Edit'					=>1,
	'pro_name'				=>$info['name'],
	'pro_username'			=>$info['user'],
	'pro_email'				=>$info['email'],
	'pro_yid'				=>$info['yid'],
	'pro_gid'				=>$info['gid'],
	'pro_avatar'			=>$info['avatar'],
	'pro_about'				=>$info['about'],
	'pro_showname'			=>$info['showname'],
	'pro_tell'				=>$info['tell'],
	'register_date'			=>mytime($config['dtype'],$info['date'],$config['dzone']),
	));
	break;
	case 'doedit':
	$rsh = array();
	$required = array(
//	'user'		=>$lang_register['username'],
	'email'		=>$lang_register['email'],
	'name'		=>$lang_register['name'],
	);
	$error = array();
	$optional = array('showname','yid','gid','tell','about','avatar');
	foreach($required as $key=>$value)
			if(empty($_POST[$key]))
				$error[] = str_replace('%name%',$value,$lang_register['required']);
			else
				$rsh[$key] = $_POST[$key];
		
		foreach($optional as $key)
			if(!isset($_POST[$key]))
				$_POST[$key] = '';
			else
			$rsh[$key] = $_POST[$key];
		if(!empty($_POST['pro_pass']))
			if(!empty($_POST['pro_new_pass']))
				{
				$u_id = $user->info['u_id'];
				$p = $d->getrowvalue("pass","SELECT `pass` FROM `member` WHERE `u_id`='$u_id' LIMIT 1",true);
				if($p == md5(sha1($_POST['pro_pass'])))
					{
						if(strlen($_POST['pro_new_pass']) < $config['min_pass_length'])
							$error[] = str_replace(array('%name%','%least%'),array($lang_register['password'],$config['min_pass_length']),$lang_register['short']);
						else
						$rsh['pass'] = md5(sha1($_POST['pro_new_pass']));
					}
					else
					$error[] = $lang_register['wp'];
				}
			
		if(count($error) != 0)
		{
		$msg = ''; 
		foreach($error as $err)
		$msg .= $err.$lang_register['seprator'];
		$itpl->assign(array('Error'=>1,'Msg'=>$msg));
		}
		else
		{
		$u_id = $user->info['u_id'];
		$d->uQuery("member",$rsh,"`u_id`='$u_id' LIMIT 1");
		$itpl->assign(array('Succeed'=>1,'Msg'=>$lang['ok']));
		}
	break;
	case 'none':
	$itpl->assign(array(
	'Main'				=>1,
	'name'				=>$info['name'],
	'username'			=>$info['user'],
	'email'				=>$info['email'],
	'register_date'		=>mytime($config['dtype'],$info['date'],$config['dzone']),
	));
	break;
	default:
	$itpl->assign(array('Error'=>1,'Msg'=>$lang['404']));
	}
$tpl -> block('Rsh',  array(
			'subject'	=> $config['sitetitle'],
			'sub_id'	=> 1,
			'sub_link'	=> 'index.php',
			'link'  	=> 'index.php?module=member',
			'title' 	=> $lang_register['profile'],
			'body'  	=> $itpl->dontshowit(),
			)
			);
}
?>