<?php
if(!defined('Module-inc') OR !is_array(@$data))
die('<a href="http://help.rashcms.com/modules" target=_blank>RashCMS</a> :: Invalid calling of '.basename (__FILE__));
$itpl = new Rashcms();
$itpl-> load('module/member/sendpm.html');
$itpl->assign('theme_url','template/main/'.$config['theme'].'/');
include('lang.fa.php');
$show_posts = false;
if(!$login)
$itpl->assign(array('Error'=>1,'Msg'=>$lang['limited_area']));
else{
	$itpl->assign('User',1);
	$_GET['task']	= empty($_GET['task']) ? 'none' : $_GET['task'];
	switch($_GET['task'])
	{
	case 'send':
	$required = array(
	'reciver'	=> $lang_register['reciver'],
	'title'		=> $lang_register['title'],
	'text'		=> $lang_register['text'],
	);
	$error = array();
	foreach($required as $key=>$value)
		if(empty($_POST[$key]))
			$error[] = str_replace('%name%',$value,$lang_register['required']);
	if($_POST['reciver'] == $user->info['user'])
	$error[] = $lang_register['same_re_send'];
	if(count($error) == 0)
		{
		$q = $d->Query("SELECT `u_id` FROM `member` WHERE `user`='$_POST[reciver]' LIMIT 1");
		if($d->getrows($q) != '1')
		$error[] = $lang_register['wrong_reciver'];
		}
	if(count($error) == 0)
		{
		$q = $d->fetch($q);
		$d->iquery("msg",array(
		'send_id'	=> $user->info['u_id'],
		're_id'		=> $q['u_id'],
		'text'		=> $_POST['text'],
		'title'		=> $_POST['title'],
		));
		$itpl->assign(array('Succeed'=>1,'Msg'=>$lang_register['pm_sent']));
		}
		else
		{
		$itpl-> assign('Form',1);
		$itpl-> assign('Error',1);
		foreach($required as $key=>$value)
			$itpl-> assign($key,@$_POST[$key]);
		$msg = '';
		foreach($error as $err)
		$msg .= $err.$lang_register['seprator'];
		$itpl-> assign('Msg',$msg);
		}

	break;
	case 'none':
	$_GET['user'] = isset($_GET['user']) ? $_GET['user'] : '';
	$_GET['title'] = isset($_GET['title']) ? $_GET['title'] : '';
	$required = array('reciver'=>$_GET['user'],'title'=>'Re : '.str_replace('Re : ','',$_GET['title']),'text'=>'');

	$itpl->assign('Form',1);
			$itpl-> assign($required);
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
			'title' 	=> $lang_register['sendpm'],
			'body'  	=> $itpl->dontshowit(),
			)
			);

?>