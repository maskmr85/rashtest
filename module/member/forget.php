<?php
if(!defined('Module-inc') OR !is_array(@$data))
die('<a href="http://help.rashcms.com/modules" target=_blank>RashCMS</a> :: Invalid calling of '.basename (__FILE__));
include('lang.fa.php');
$itpl = new Rashcms();
$itpl-> load('module/member/forget.html');
$show_posts = false;
if(isset($_GET['submit']))
	{
	$error = array();
	$required = array(
	'username'	=> $lang_register['username'],
	'email'		=> $lang_register['email'],
	'RashCMS'	=> $lang_register['scode'],
	);
	foreach($required as $key=>$value)
		if(empty($_POST[$key]))
			$error[] = str_replace('%name%',$value,$lang_register['required']);
	if(count($error) == '0')
		{
			if(!email($_POST['email']))
				$error[] = $lang_register['wmail'];
			if($user->GetId($_POST['username']) <= 0)
				$error[] = $lang_register['wuser'];
			if( $_SESSION['rash_secimg'] !== $_POST['RashCMS'])
				$error[] = $lang_register['wrongseccode'];
		}
	if(count($error) == '0')
		{
		$m = $d->getrowvalue("email","SELECT `email` FROM `member` WHERE `user`='$_POST[username]' LIMIT 1",true);
		if($m != $_POST['email'])
			$error[] = $lang_register['wmail'];
		}
	if(count($error) == '0')
		{
		$min_pass_length = ($config['min_pass_length']<5) ? 5 : $config['min_pass_length'];
		$new_pass = GEN($min_pass_length);
		$body = str_replace(array('%password%','%user%'),array($new_pass,$_POST['username']),$lang_register['pass_recovery']);
		$new_pass = md5(sha1($new_pass));
		send_mail($m,$config['email'],$body,$lang_register['forget']);
		$d->Query("UPDATE `member` SET `pass`='$new_pass' WHERE `user`='$_POST[username]' LIMIT 1");
		$itpl-> assign(array('Succeed'=>1,'Msg'=>$lang_register['pass_resetted']));
		}
	else
		{
		$msg = '';
		foreach($error as $err)
		$msg .= $err.$lang_register['seprator'];
		$itpl-> assign(array('Error'=>1,'ErrorMsg'=>$msg,'Form'=>1));
		}
	}
	else
	$itpl-> assign('Form',1);
$tpl -> block('Rsh',  array(
			'subject'	=> $config['sitetitle'],
			'sub_id'	=> 1,
			'sub_link'	=> 'index.php',
			'link'  	=> 'index.php?module=member',
			'title' 	=> $lang_register['forget'],
			'body'  	=> $itpl->dontshowit(),
			)
			);
?>