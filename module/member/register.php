<?php
if(!defined('Module-inc') OR !is_array(@$data))
die('<a href="http://help.rashcms.com/modules" target=_blank>RashCMS</a> :: Invalid calling of '.basename (__FILE__));
if(isset($login))
if($login)
{
HEADER('LOCATION: index.php');
die('RashCMS.com');
}
$itpl = new Rashcms();
$itpl-> load('module/member/reg.html');
include('lang.fa.php');
if($config['new_member'] == '2')
{
$show_posts = false;
$itpl-> assign(array('Error'=>1,'ErrorMsg'=>$lang['disabled']));
}
else{
$required = array(
	'user'		=>$lang_register['username'],
	'pass'		=>$lang_register['password'],
	're_pass'	=>$lang_register['re_pass'],
	'email'		=>$lang_register['email'],
	'RashCMS'	=>$lang_register['scode'],
	'name'		=>$lang_register['name'],
	);
$optional = array('show','yahoo','gmail','tell','text_about');
$show_posts = false;
if(isset($_POST['user']))
$_POST['user'] = ($_POST['user'] == 'guest') ? '' : $_POST['user'];
if(isset($_GET['task'])){
	if(($_GET['task'] == 'dopost'))
	{
	$error = array();
	$itpl -> assign('Register_Send',1);
		foreach($required as $key=>$value)
			if(empty($_POST[$key]))
				$error[] = str_replace('%name%',$value,$lang_register['required']);
		foreach($optional as $key=>$value)
			if(!isset($_POST[$key]))
				$_POST[$key] = '';
	
	if(count($error) == 0)
	{
		if( $_SESSION['rash_secimg'] !== $_POST['RashCMS'])
		$error[] = $lang_register['wrongseccode'];
		else
		$_SESSION['rash_secimg'] = md5(rand(1000,100000));
		if(!eregi("^[[:alnum:]]+$", $_POST['user']))
		$error[] = $lang_register['userg'];
		elseif(strlen($_POST['user']) < $config['min_user_length'])
		$error[] = str_replace(array('%name%','%least%'),array($lang_register['username'],$config['min_user_length']),$lang_register['short']);
		elseif($d->getrows("SELECT `u_id` FROM `member` WHERE `user`='$_POST[user]' LIMIT 1",true) > 0)
		$error[] = $lang_register['taken'];
		if($_POST['pass'] !== $_POST['re_pass'])
		$error[] = $lang_register['mpass'];
		elseif(strlen($_POST['pass']) < $config['min_pass_length'])
		$error[] = str_replace(array('%name%','%least%'),array($lang_register['password'],$config['min_pass_length']),$lang_register['short']);
		$_POST['pass'] = md5(sha1($_POST['pass']));
		if(!email($_POST['email']))
		$error[] =$lang_register['wmail'];
	}
	if(count($error) != 0)
	{
	$itpl-> assign('Error',1);
	$itpl-> assign('register_form',1);
	foreach($optional as $key)
		if(isset($_POST[$key]))
			$itpl-> assign('reg_'.$key,@$_POST[$key]);
	foreach($required as $key=>$value)
		if(isset($_POST[$key]))
			$itpl-> assign('reg_'.$key,@$_POST[$key]);
	$msg = ''; 
	foreach($error as $err)
	$msg .= $err.$lang_register['seprator'];
	$itpl-> assign('ErrorMsg',$msg);
	}
	else
	{
	$d->iquery("member",array(
	'prv'		=>	'',
	'name'		=>	$_POST['name'],
	'user'		=>	$_POST['user'],
	'pass'		=>	$_POST['pass'],
	'date'		=>	time(),
	'ip'		=>	getRealIpAddr(),
	'email'		=>	$_POST['email'],
	'yid'		=>	$_POST['yahoo'],
	'gid'		=>	$_POST['gmail'],
	'tell'		=>	$_POST['tell'],
	'about'		=>	$_POST['text_about'],
	'showname'	=>	$_POST['show'],
	'color'		=>	'#000000',
	'stat'		=>	'1',
	'avatar'	=>	'',
	));
	$itpl-> assign(array('Succeed'=>1,'Msg'=>$lang_register['registred']));
	}
	}
	
}
else
{
$itpl-> assign('register_form',1);
	foreach($optional as $key)
			$itpl-> assign('reg_'.$key,'');
	foreach($required as $key=>$value)
			$itpl-> assign('reg_'.$key,'');
}
}
$tpl -> block('Rsh',  array(
			'subject'	=> $config['sitetitle'],
			'sub_id'	=> 1,
			'sub_link'	=> 'index.php',
			'link'  	=> 'index.php?module=member',
			'title' 	=> $lang_register['register'],
			'body'  	=> $itpl->dontshowit(),
			)
			);

?>