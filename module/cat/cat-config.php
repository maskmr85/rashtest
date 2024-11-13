<?php
if(!defined('Module-inc') OR !is_array(@$data))
die('<a href="http://help.rashcms.com/modules" target=_blank>RashCMS</a> :: Invalid calling of '.basename (__FILE__));
	$cathtml = array(
	'nl'=>'<br>',
	'bold_s'=>'<b>',
	'bold_e'=>'</b>',
	'space'=>' ',
	'c_pr'=>'<img src="template/main/'.$config['theme'].'/img/arrow.gif">',
	'sc_pr'=>'<img src="template/main/'.$config['theme'].'/img/point.gif">',
	);
	$q = $d->Query("SELECT * FROM `cat` WHERE `sub`='0'");
	$ch = '';
	$stats = true;
		while($data = $d->fetch($q))
		{
		$id = $data['id'];
		$num = -1;
		$cats[$data['id']] = $data['name'];
			$ch .= $cathtml['c_pr'].$cathtml['space'].$cathtml['bold_s'].$data['name'].$cathtml['bold_e'].$cathtml['nl'];
			$iq = $d->Query("SELECT * FROM `cat` WHERE `sub`='$data[id]'");
				while($idata = $d->fetch($iq))
				{
				$cats[$idata['id']] = $idata['name'];
				$url = $config['seo'] == '1' ? 'cat-'.$idata['id'].'.html' : 'index.php?module=cat&catid='.$idata['id'];
				$idata['name'] = "<a href='$url' title='$idata[name]'>$idata[name]</a>";
				$ch .= $cathtml['space'].$cathtml['space'].$cathtml['space'].$cathtml['space'].$cathtml['sc_pr'].$cathtml['space'].$idata['name'];
				if($stats)
					{
					$num = $d->getrows("SELECT `id` FROM `data` WHERE `cat_id`='$idata[id]'",true);
					$ch .= ' ( '.$num.' )'.$cathtml['nl'];
					}
					else
					$ch .= $cathtml['nl'];
				}
		}
	
	$tpl->assign('Cats', getShowCats(0, '', $cathtml));
	unset($cat);unset($cathtml);
	if(isset($_GET['module']))
	{
		if($_GET['module'] == 'cat')
		{
		@$type = ($type !='DESC' && $type != 'ASC') ? 'ASC' : $type;
		@$RPP = (!is_numeric($RPP)) ? 10 : abs($RPP);
		$CurrentPage = (!isset($_GET['page']) || !is_numeric(@$_GET['page']) || (abs(@$_GET['page']) == 0)) ? 1 : abs($_GET['page']);
		$From = ($CurrentPage-1)*$RPP;
		@$From = (!is_numeric($From)) ? 1 : abs($From);
				if(@is_numeric($_GET['catid']))
				{
				if(!defined('custom_p_url'))
				define('custom_p_url',true);
				$ctimestamp = time();
				define('customized_post_query',true);
				$From = 0;
				$post_q = "select * FROM `data` WHERE  `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>'$ctimestamp') AND (`show`!='4' || `show`='2') AND `cat_id`='$_GET[catid]' OR cat_id IN (SELECT `id` FROM `cat` WHERE `sub`='$_GET[catid]')  order by `id` $type LIMIT $From,$RPP";
				define('customized_post_query_value',$post_q);
				$t_pr = $d->getrows("select `id` FROM `data` WHERE  `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>'$ctimestamp') AND (`show`!='4' || `show`='2') AND `cat_id`='$_GET[catid]' OR cat_id IN (SELECT `id` FROM `cat` WHERE `sub`='$_GET[catid]')",true);
				define('customized_post_query_value_t',$t_pr);
				$pages_url = ($config['seo'] == '1') ? $config['site'].'cat-'.$_GET['catid'].'-' : $config['site'].'index.php?module=cat&catid='.$_GET['catid'].'&';
				}
				elseif(@is_numeric($_GET['postid']))
				{
				$single_post = true;
				$ctimestamp = time();
				define('customized_post_query',true);
				$post_q = "select * FROM `data` WHERE  `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>$ctimestamp) AND (`show`!='4' || `show`='2') AND `id`='$_GET[postid]' LIMIT 1";
				define('customized_post_query_value',$post_q);
				define('customized_post_query_value_t',0);
				}
				elseif(@is_numeric($_GET['userid']))
				{
				if(!defined('custom_p_url'))
				define('custom_p_url',true);
				$single_post = true;
				$ctimestamp = time();
				define('customized_post_query',true);
				$post_q = "select * FROM `data` WHERE  `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>$ctimestamp) AND (`show`!='4' || `show`='2') AND `author`='$_GET[userid]' LIMIT $From,$RPP";
				$t_pr = $d->getrows("select * FROM `data` WHERE  `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>$ctimestamp) AND `author`='$_GET[userid]'",true);
				define('customized_post_query_value_t',$t_pr);
				define('customized_post_query_value',$post_q);
				$pages_url = ($config['seo'] == '1') ? $config['site'].'user-'.$_GET['userid'].'-' : $config['site'].'index.php?module=cat&userid='.$_GET['userid'].'&';
				}
		}
	}
?>