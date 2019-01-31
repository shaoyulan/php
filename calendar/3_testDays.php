<?php 

//設定閏年變數
$number = $_POST['number'];
$number_minus1 = $number-1;
$Leap = 0; //儲存閏年次數
$Common = 0; //儲存平年次數
$Howmany_Days = 0;
$Add_Days = 0; //總共多少天


$Db4 = (int)($number_minus1/4); 
$Db100 = (int)($number_minus1/100);
$Db400 = (int)($number_minus1/400);

//計算Leap年、Common年多少次
$Leap = $Db4 - $Db100 + $Db400;
$Common = $number_minus1 - $Leap;

//從西元1年1/1到今天共幾天
$Howmany_Days = ($Leap+$number_minus1); 

/* 公式推導

經過總年數 = (1+閏年*366+平年*365)

= (1+閏年*(52*7+2)+平年*(52*7+1))
= (1+閏年+閏年+平年)
= (1+閏年+前一年總年數)

*/


//計算要加幾天
$Add_Days = $Howmany_Days%7;


?>
