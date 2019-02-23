<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>salesDep</title>
	<style>
		form{
			width: 500px;
			margin: 0 auto;
		}

		table{
			margin: 0 auto;
			width: 500px;
		}
		tr:first-child{
			text-align: center;
			font-weight: bold;
			font-size: 30px;
			text-decoration: underline;
		}
		tr:nth-child(2){
			text-align: right;
		}
		.dep-total td, tr:nth-child(3) td{
			border-bottom:1px solid black;
			border-top:1px solid black;
			border-collapse: collapse;
		}
	</style>
</head>
<body>
	
</body>
</html>

<?php
if(isset($_POST['password'])){
	$PASSWORD = $_POST['password'];
	$DB_NAME = 'b13_22914069_php_department_sales_statistics';
	$TABLE_NAME = 'price';
	$MYSQL_SERVER = 'sql300.byethost.com';
	$USER_NAME = 'b13_22914069';
	// End of Basic Setting
	try{
		 // 建立連線物件
		 $link = $link = mysql_connect("{$MYSQL_SERVER}", "{$USER_NAME}", "{$PASSWORD}");;
		 // 選擇資料庫
	     $db_selected = mysql_select_db("{$DB_NAME}", $link);
	     // 設定編碼
	     mysql_query("SET NAMES UTF8");
	     // 回傳狀態
	}catch(Exception $e){
		echo "Database connection failed.".$e->getMessage();
		exit;
	}

	//取得部門資料
	$sql = "SELECT * FROM `dept`";
	$DepResult = mysql_query($sql,$link);
	$DepNameArray = array();
	// 建立部門名稱=>代號陣列
	 // $row[1] : 部門代號   $row[0]:部門名稱
	while($row=mysql_fetch_array($DepResult)){
		$DepNameArray[$row[1]] = $row[0]; 
	}
	$a = 'A';
	$re = preg_grep("/^".$a."*/",$DepNameArray);

	// 將指針歸零
	mysql_data_seek($DepResult,0);

	// 取得有哪些年度
	$sql = "SELECT DISTINCT 資料年度 FROM `employee`";
	$YearResult = mysql_query($sql,$link);

	// 是否有接收到選項
	if(isset($_POST['year']) and isset($_POST['dep'])){
		// 有接收到->程式開始
		// SQL
		 // $dep[0] : A / A01 / B / B01   $dep[1] : 研發課 / 業務課
		$dep = explode(';',$_POST['dep']);
		 // 取得由SQL已排序過的資料 
		$sql = "SELECT * FROM  `employee` LEFT JOIN  `leave` ON  `employee`.`姓名` =  `leave`.`姓名` 
		WHERE  `部門代號` 
		LIKE '".$dep[0]."%' AND `資料年度` = ".$_POST['year']."
		ORDER BY  `部門代號` ASC ,  `目前月薪資` DESC ,  `employee`.`姓名` ASC 
		LIMIT 0 , 200";

		// SQL 查詢
		$result = mysql_query($sql,$link);
		echo "<form method='post' action='".$_SERVER['PHP_SELF']."'><table cellspacing='0'>";
		// password
		echo "<input type='hidden' name='password' value='".$_POST['password']."'>";
		$DepNameArray = $GLOBALS['DepNameArray'];
		echo "<tr><td colspan='7'>頂新資訊公司民國".$_POST['year']."年".$dep[1].$DepNameArray[$_POST['dep']]."部門員工未休假奬金統計報表</td></tr>";
		echo "<tr><td colspan='7'>民國".$_POST['year']."年一月三十一日</td></tr>";
		echo "<tr><td>姓名</td><td>職稱</td><td>月薪</td><td>年假天數</td><td>未休天數</td><td>未休假獎金</td><td>備註</td></tr>";

		$new_row = array(array());
		$count = 0; // 資料計數
		$base = 0; // 指針基準
		while($row = mysql_fetch_array($result,MYSQL_NUM)){
			$count = $GLOBALS['count'];
			$base = $GLOBALS['base'];
			// echo "S".$row[0]."=>".$new_row[0][$count].($count)."E";
			// 假如 $row[0]姓名 = 上一個$new_row[0][$count]的姓名則
			if($row[0] == $new_row[0][$base-1] and $count>0){
				$new_row[4][$base-1] -= $row[14]; // 未休天數處裡
			}else{
				// $new_row[不可為空][$count] 
				$new_row[0][$base] = $row[0]; // 姓名
				$new_row[1][$base] = $row[1]; // 職稱
				$new_row[2][$base] = $row[7]; // 月薪
				$new_row[3][$base] = $row[8]; // 年假天數
				$new_row[4][$base] = $row[8]-$row[14]; // 未休天數
				$new_row[8][$base] = $row[2]; // 部門名稱
				$base++;
			}	
			$count++;
		}
		/* 列印除錯用
		for ($i=0; $i <$count ; $i++) { 
			echo "name".$new_row[0][$i];
			echo "posi".$new_row[1][$i];
			echo "sala".$new_row[2][$i];
			echo "yearo".$new_row[3][$i];
			echo "<br>";
		}
		echo 'CHENG'.$new_row[4][4];
		*/
		$dep_name = ''; // 部門名稱暫存區
		$k=0; // 資料從1開始
		$depT = 0; // 部門未休假獎金
		$grandT =0; //總體未休假獎金
		while($k<$base){
			$row = $new_row;
			// 部門加總(避免開頭就印)
			if($row[8][$k] != $GLOBALS['dep_name'] and $dep_name != ''){
				$total = number_format((int)$GLOBALS['depT']);
				echo "<tr class='dep-total'><td colspan='5' >".'部門加總'."</td><td>".$total."</td><td></td></tr>";
				$GLOBALS['depT'] = 0;
			}
			// 不同部門，印出名稱
			if($row[8][$k] != $GLOBALS['dep_name']){
				global $DepNameArray;
				echo "<tr><td colspan='7'>".$DepNameArray[$row[8][$k]]."</td></tr>";
			}
			// 姓名
			echo "<tr><td>".$row[0][$k]."</td>";
			// 職稱
			echo "<td>".$row[1][$k]."</td>";
			// 月薪
			echo "<td>".number_format($row[2][$k])."</td>";
			// 年假天數
			echo "<td>".$row[3][$k]."</td>";

			// 未休天數
			echo "<td>"; 
			if($row[4][$k]<0){
				$row[4][$k]=0 ;echo '-';
			}else{
				echo $row[4][$k];
			}
			echo "</td>";

			// 未休假獎金
			$bonus = number_format((int)($row[2][$k]/28*$row[4][$k]));
			echo "<td>"; 
			if($bonus<=0){
				echo '-';
			}else{
				echo $bonus;
			}"</td>";

			// 備註
			echo "<td>";if($row[3][$k]-$row[4][$k]==0){echo "未休";}"</td>";

			$GLOBALS['depT'] += $row[2][$k]/28*$row[4][$k];
			$GLOBALS['grandT'] += $row[2][$k]/28*$row[4][$k] ;
			$GLOBALS['dep_name'] = $row[8][$k]; //更新暫存部門名稱
			$k++;

			//最後一次加總
			if($k==$base){
				$total = number_format((int)$GLOBALS['depT']);
				echo "<tr class='dep-total'><td colspan='5'>部門加總</td><td>".$total."</td><td></td></tr>";
				$GLOBALS['grandT'] = number_format((int)$GLOBALS['grandT']);
				echo "<tr class='dep-total'><td colspan='5'>未休假獎金總計金額</td><td>".$GLOBALS['grandT']."</td><td></td></tr>";
			}
		}
		echo "</form></table>";
		echo "<input type='submit' value='回選單'>";
		// echo "回選單<a style='color:red' href='".$_SERVER['PHP_SELF']."'>點擊</a>";
	}else{
		// 沒接收到 -> 列印選單頁
		echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>";
		// Hidden password
		if(isset($_POST['password'])){
			echo "<input type='hidden' name='password' value='".$_POST['password']."'>";
		}
		echo "<h2>頂新資訊公司部門員工未休假奬金統計報表</h2><p>";
		echo "<h4>請輸入以下資料</h4>";
		// 列印年度欄位
		echo "<select name='year'>";
		while ($row = mysql_fetch_array($GLOBALS['YearResult'])) {
			echo "<option value='".$row[0]."'>".$row[0];
		}
		echo "</select>";
		// 列印部門欄位
		$opt_val_temp = ''; // 用於暫存部門主代號資料
		echo "<select name='dep'>";
			while($row = mysql_fetch_array($GLOBALS['DepResult'])){
				$n = substr($row[0],0,6);
				// 第一列 列出部門不分課別
				if($opt_val_temp ==''){
					 $n = substr($row[0],0,6); // 中文一個字3字元
					echo "<option value='".$row[1][0]."'>".$n;
				}
				// value = 代號 A B C D
				if($row[1][0] != $opt_val_temp && $opt_val_temp!=''){
					echo "<option value='".$row[1][0]."'>".$n;
				}
				echo "<option value='".$row[1]."'>".$row[0];
				$opt_val_temp = $row[1][0]; // 部門主代號
			}
		echo "</select>";
		echo "<button type='submit'>送出";
		echo "</form>";
	}
}else{
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>";
	echo "請輸入密碼<input type='text' name='password'>";
	echo "<input type='submit'>";
	echo "</form>";
}


