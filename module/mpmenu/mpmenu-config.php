<?php
if(!defined('Module-inc') OR !is_array(@$data))
die('<a href="http://help.rashcms.com/modules" target=_blank>RashCMS</a> :: Invalid calling of '.basename (__FILE__));
function ShowMenus($parent = 0) {
	global $d;
    $menu_data = $d->Query("SELECT * FROM `mpmenu` WHERE sub = '$parent' ORDER BY `id` ASC");
	$out = '';
    if ($d->GetRows($menu_data) > 0) 
	{
		$out .="<ul class='menu' id='menu'>";
		while($menu = $d->fetch($menu_data))
		{
			$class = ($parent == 0) ? 'menulink' : '';
			if(empty($class) && $d->GetRows("SELECT `id` FROM `mpmenu` WHERE `sub`='$menu[id]' LIMIT 1", true) > 0)
				$class = 'sub';
			$out .= "<li><a href='$menu[enname]' class='$class'>$menu[name]</a>";
			$out .= ShowMenus($menu['id']);
			$out .= "</li>\n";
		}
		$out .="</ul>\n";
    }
	return $out;
}
$tpl-> assign('MPMENU', ShowMenus(0));