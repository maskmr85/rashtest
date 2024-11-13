<?php
/***************************************************************************
 *                                  Rash CMS
 *                          -------------------
 *   copyright            : (C) 2009 The RashCMS  $Team = "www.rashcms.com";
 *   email                : info@rashcms.com
 *   email                : rashcms@gmail.com
 *   programmer           : Reza Shahrokhian
 ***************************************************************************/
//         Security
if ( !defined('news_security'))
{
 die("You are not allowed to access this page directly!");
}
function mytime($type,$TheTime,$TimeZone){
$mtime = $TheTime;
$mtime += $TimeZone * 3600;
return jdate($type, $mtime);
}
function GEN($num) {
$rashlist = 'ABDEFGHJKMNPRSTZ23456789';
$rashg = '';
$i = 0;
while ($i < $num) {
$rashg .= substr($rashlist, mt_rand(0, strlen($rashlist)-1), 1);
$i++;
}
return $rashg;
}
function newpass() {
return GEN(8);
}
function email($email){
if (preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email))
return true;
return false;
}
// safe function
function safe($value,$type='0'){
$value = trim($value);
$value = str_replace("|nline|","",$value);
$value = str_replace("|rnline|","",$value);
$value = str_replace("\r\n","|rnline|",$value);
$value = str_replace("\n","|nline|",$value);
$value = mysql_real_escape_string( $value );
$value = str_replace("|nline|","\n",$value);
$value = str_replace("|rnline|","\r\n",$value);
if($type != '1')
{$value		= htmlspecialchars($value);
$value		= strip_tags($value);
$value 		= str_replace(array("<",">","'","&#1740;","&amp;","&#1756;"),array("&lt;","&gt;","&#39;","&#1610;","&","&#1610;"),$value);
}
return $value;
}
function safeurl($url,$strict = true){$replace = ($strict) ? array('/','\\','.','http','ftp','www',"'",'"') : array('/','\\','http://','ftp','www',"'",'"');$url = safe(str_ireplace($replace,'',$url));
return $url;
}
function engconv($text){
	$text = safe($text,1);
	$text = str_replace(array('!','@','#','$','%','^','*','(',')','_','=','+','|','/','\\','~','`','\'','"','&','?','>','<'),'-',$text);
    return $text;
}

        function send_mail($mail_to,$mail_from,$body,$mail_subject="Password recovery")
        {
		
		global $config,$d;
		
		
		if(currentpage == 'admin')
		$dir = '../';
		elseif(currentpage == 'ajaxadmin')
		$dir = '../../';
		else
		$dir = '';
		if(!class_exists('Rashcms'))
		require_once($dir.'includes/template.php');
		$tpl = new Rashcms();
		$tpl-> load($dir.'template/main/'.$config['theme'].'/mail.htm');
		$MailHeader  = 'MIME-Version: 1.0' . "\r\n";
		$MailHeader .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    	$MailHeader .= 'From: '.$mail_from.'' . "\r\n"; // Sender's Email Address
		$MailHeader .= 'Return-Path: '.$mail_from.' <'.$mail_from.'> /n'; // Indicates Return-path
		$MailHeader .= 'Reply-To: '.$mail_from.' <'.$mail_from.'> /n'; // Reply-to Address
		$MailHeader .= 'X-Mailer: PHP/' . phpversion(); // For X-Mailer
        $cutime     = mytime($config['dtype'],time(),$config['dzone']);
        $tpl->assign(array(
		'rashcmstopic'	=>	$mail_subject,
		'rashcmsdate'	=>	$cutime,
		'rashcmsweb'	=>	$config['site'],
		'rashcmsmail'	=>	$config['email'],
		'rashcmsbody'	=>	$body,
		));
        $nls = $d->Query("SELECT * FROM nls");
		$nls = $d->fetch($nls);
        if(!empty($nls['SmtpHost']) && !empty($nls['SmtpUser']) && !empty($nls['SmtpPassword'])){
        $SmtpHost                = $nls['SmtpHost'];
        $SmtpUser                = $nls['SmtpUser'];
        $SmtpPassword            = $nls['SmtpPassword'];
		require('smtp.php');
        return smtpmail($mail_to, $mail_from,$mail_subject, $tpl->dontshowit(), $MailHeader);
        //uselsmtp
        }else{
        if(@mail($mail_to, $mail_subject, $tpl->dontshowit(), $MailHeader)) 
        return true;
        return false;
        }
        }
function ajaxvars(&$value,$key){
$value = str_ireplace(array('**rsh**','**reza**','**sh**'),array('&','=','+'),$value);
return $value;
}
function reglink($text)
{
        global $lang;
        $text = preg_replace("#<a(.*?)href=\"(.*?)\"(.*?)>.*?</a>#i","<!-- Rash CMS|Programmed By Reza Shahrokhian -->". $lang['reglink']."<!-- Rash CMS|Programmed By Reza Shahrokhian -->", $text);
        $text = preg_replace("#<a href=\"mailto:(.*?)\">.*?</a>#i", "<!-- Rash CMS|Programmed By Reza Shahrokhian -->". $lang['reglink']."<!-- Rash CMS|Programmed By Reza Shahrokhian -->", $text);
      return($text);
     }



function rashpage($TR,$RPP,$NumPageList,$CurrentPage,$tpl,$pagetag,$ref,$seo = 0)
{
global $tpl,$lang;
//$CurrentPage--;
//TP = total Records
//Rpp = Results Per Page
//NumPageList = number of pages to be shown in the list (except first and last and next_pointer page)
$CurrentPage = (empty($CurrentPage) || ($CurrentPage == 0)) ? 1 : abs($CurrentPage);
$Pages = ceil($TR/$RPP);
$P = array();
for($i=0;$i<$Pages;$i++){
$P[$i] = $i;
}
$prv = 'page=';
$ext = '';
if($seo){$prv = 'page-';
$ext = '.html';
}

	if($CurrentPage != 1 && $Pages>=$CurrentPage){
	$tpl->block($pagetag,array('pagelink'=>$ref.$prv.($CurrentPage-1).$ext,'page'=>$lang['prpage']));
	}
	for($i=0; $i<$Pages; $i++){
	$tpl->block($pagetag,array('pagelink'=>$ref.$prv.($P[$i]+1).$ext,'page'=>($P[$i]+1)));
	}
	if($CurrentPage < $Pages)
	{
	$tpl->block($pagetag,array('pagelink'=>$ref.$prv.($CurrentPage+1).$ext,'page'=>$lang['nextpage']));
    }
}
function get_ext($key) {
	$key = substr($key, stripos($key, '.'));
    $key = substr($key,1);
    //$key = strtolower($key);
	return $key;
}
//getRealIpAddr by ali_sed
function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
function smile($text)
{
return $text;
}

function untrailingslashit($string) {
    return rtrim($string, '/');
}

function trailingslashit($string) {
	return untrailingslashit($string) . '/';
}


function getSelectCats($parent = 0, $join = '' , $id = '', $selected_id = false) 
{
	global $d,$colors;
    $menu_data = $d->Query("SELECT * FROM `cat` WHERE sub = '$parent' ORDER BY `sub`,`id` ASC");
	$out = '';
    if ($d->GetRows($menu_data) > 0) 
	{
		$p_sub = (int)$d->GetRowValue("sub","SELECT `sub` FROM `cat` WHERE `id`='$parent' LIMIT 1", true);
		$join .= '---';
		while($menu = $d->fetch($menu_data))
		{
			//$font = (strlen($join) == 3) ? 'bold' : 'normal';
			$font = '';
			if($p_sub == $parent)
				$join = substr($join , 0 , -3);
			$color = (isset($colors[floor(strlen($join)  / 3)])) ? $colors[floor(strlen($join) /3)] : 'black';
			if($menu['sub'] == 0)
			{
				$catMainId = 0;
				$font = 'bold';
			}
			else
			{
				$catMainId = $menu['id'];
				$font = 'notmal';
			}
			$vid = (!empty($id)) ? "id='".$id . $menu['id']."'" : '';
			$selected = '';
			if($selected_id !== false && $selected_id == $menu['id'])
				$selected = " selected ";
			$out .= "<option style='font-weight:$font' value='$menu[id]' $selected $vid >" . $join.' '.$menu['name'] . "</option>";
			$out .= getSelectCats($menu['id'], $join , $id, $selected_id);
			
		}
    }
	return $out;
}
function getShowCats($parent = 0, $join = '' , $config) 
{
	global $d,$colors;
    $menu_data = $d->Query("SELECT * FROM `cat` WHERE sub = '$parent' ORDER BY `sub`,`id` ASC");
	$out = '';
    if ($d->GetRows($menu_data) > 0) 
	{
		$p_sub = (int)$d->GetRowValue("sub","SELECT `sub` FROM `cat` WHERE `id`='$parent' LIMIT 1", true);
		$join .= '---';
		while($menu = $d->fetch($menu_data))
		{
			//$font = (strlen($join) == 3) ? 'bold' : 'normal';
			$font = '';
			if($p_sub == $parent)
				$join = substr($join , 0 , -3);
			$color = (isset($colors[floor(strlen($join)  / 3)])) ? $colors[floor(strlen($join) /3)] : 'black';
			$url = 'index.php?module=cat&catid='.$menu['id'];
			$menu['name'] = "<a href='$url' title='$menu[name]'>$menu[name]</a>";
			if($menu['sub'] == 0)
			{
				$out .= $config['c_pr'].$config['space'].$config['bold_s'].$join.$menu['name'].$config['bold_e'].$config['nl'];
			}
			else
			{
				$out .= $config['space'].$config['space'].$config['space'].$config['space'].$config['sc_pr'].$config['space'].$join.$menu['name'].$config['nl'];
			}
			$out .= getShowCats($menu['id'], $join , $config);
			
		}
    }
	return $out;
}