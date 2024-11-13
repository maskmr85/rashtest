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
function timeboxgen($id='',$yearp=13,$yearn=0){
$dfun = defined('notfarsi') ?  'date' : 'jdate';
$thishour = $dfun('G');
$thismonth = $dfun('m');
$thisyear = $dfun('Y');
$thisday = $dfun('d');
$Hourb = "<select name='".$id."[hour]'  id='".$id."_hour' class=\"select\">";
for ($i=1;$i < 25; $i++){
$Hourb.="<option "; if($thishour == $i) {$Hourb .=" selected "; }  $Hourb .="value=$i>$i</option>";
}
$Hourb .="</select>";
$Monthb = "<select name='".$id."[month]'  id='".$id."_month' class=\"select\">";
for ($i=1;$i <= 12; $i++){
$Monthb.="<option "; if($thismonth == $i) {$Monthb .=" selected "; }  $Monthb .="value=$i>$i</option>";
}
$Monthb .="</select>";
$Yearb = "<select name='".$id."[year]'  id='".$id."_year' class=\"select\">";
for ($i=$thisyear-$yearn;$i <= $thisyear+$yearp; $i++){
$Yearb.="<option "; if($thisyear == $i) {$Yearb .=" selected "; }  $Yearb .="value=$i>$i</option>";
}
$Yearb .="</select>";
$Dayb = "<select name='".$id."[day]' id='".$id."_day' class=\"select\">";
for ($i=1;$i <=31; $i++){
$Dayb.="<option "; if($thisday == $i) {$Dayb .=" selected "; }  $Dayb .="value=$i>$i</option>";
}
$Dayb .="</select>";
return $Hourb.' - '.$Dayb.' / '.$Monthb.' / '.$Yearb;
}

?>