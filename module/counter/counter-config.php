<?php
if(!defined('Module-inc') OR !is_array(@$data))
die('<a href="http://help.rashcms.com/modules" target=_blank>RashCMS</a> :: Invalid calling of '.basename (__FILE__));
if($data['stat'] != 3){
$itpl = new Rashcms();
$itpl-> load('module/counter/block-theme.html');
$itpl->assign('theme_url','template/main/'.$config['theme'].'/');
require_once('count.php');
	if($data['stat'] == 1)
	{
	$itpl -> assign( array(
    'total_news'=>  $counter['totalpost'],
    'today'     =>  $counter['todayv'],
    'yes'       =>  $counter['yesterdayv'],
    'total'     =>  $counter['totalv'],
    'ons'       =>  $counter['onlines'],
    'month'     =>  $counter['monthv'],
    'pmonth'    =>  $counter['lastmonthv'],
    'year'      =>  $counter['yearv'],
    'pyear'     =>  $counter['lastyearv'],
    'tmem'      =>  $counter['member'],
    'ncom'      =>  $counter['totalcom'],
    )
    );
	}
}
$tpl->assign('Counter',$itpl->dontshowit());
?>