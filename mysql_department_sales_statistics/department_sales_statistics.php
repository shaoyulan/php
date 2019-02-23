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

	$sql = "SELECT * FROM `dept`";
	$DepResult = mysql_query($sql,$link);
	$DepNameArray = array();
	// 建立代號名稱對應陣列
		// $row[1] : 部門代號   $row[0]:部門名稱
	while($row=mysql_fetch_array($DepResult)){
		$DepNameArray[$row[1]] = $row[0]; 
	}
	// $a = 'A';
	// $re = preg_grep("/^".$a."*/",$DepNameArray);

	// 將指針歸零
	mysql_data_seek($DepResult,0);

	// 首頁選單月
	$sql = "SELECT DISTINCT 資料年度 FROM `employee`";
	$YearResult = mysql_query($sql,$link);

	

	// 是否有接收到選項
	if(isset($_POST['year']) and isset($_POST['dep'])  and isset($_POST['choice']) ){

		// 有接收到->程式開始
		// 拆開部門名稱
		$dep = explode(';',$_POST['dep']);
		// 先拆開choice資料0->代號; 1->名稱
		$choice = explode(';',$_POST['choice']);

		// SQL
		$sql = "SELECT * FROM  `employee` LEFT JOIN  `".$choice[0]."` ON  `employee`.`姓名` =  `".$choice[0]."`.`姓名` 
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
		echo "<tr><td colspan='7'>頂新資訊公司民國".$_POST['year']."年".$dep[1].$DepNameArray[$_POST['dep']]."部門員工".$choice[1]."統計報表</td></tr>";
		echo "<tr><td colspan='7'>民國".$_POST['year']."年一月三十一日</td></tr>";


		// 依報表選擇表格標題
		switch($choice[0]){
			case 'leave':
			$title = "<tr><td>姓名</td><td>職稱</td><td>月薪</td><td>年假天數</td><td>未休天數</td><td>未休假獎金</td><td>備註</td></tr>";
			break;

			case 'overtime':
			$title = "<tr><td>姓名</td><td>職稱</td><td>月薪</td><td>加班時數</td><td>加班費</td><td>佔月薪比例</td></tr>";
			break;
		}
		// 列印表格標題
		echo $title;

		$new_row = array(array());
		$count = 0; // 資料計數
		$base = 0; // 指針基準
		$appd = array(); //放置附表陣列

		// 資料整理
		if($choice[0]=='leave'){
			// 特休
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
		}elseif($choice[0]=='overtime'){
			// 加班
			while($row = mysql_fetch_array($result,MYSQL_NUM)){
				$count = $GLOBALS['count'];
				$base = $GLOBALS['base'];
				// echo "S".$row[0]."=>".$new_row[0][$count].($count)."E";
				// 假如 $row[0]姓名 = 上一個$new_row[0][$count]的姓名則
				if($row[0] == $new_row[0][$base-1] and $count>0){
					$new_row[4][$base-1] += $row[14]; // 未休天數處裡
				}else{
					// $new_row[不可為空][$count] 
					$new_row[0][$base] = $row[0]; // 姓名
					$new_row[1][$base] = $row[1]; // 職稱
					$new_row[2][$base] = $row[7]; // 月薪
					// $new_row[3][$base] = $row[8];  年假天數
					$new_row[4][$base] = $row[14]; // 加班時數
					$new_row[8][$base] = $row[2]; // 部門名稱
					$base++;
				}	
				
				$count++;
			}

			// 資料指針歸0
			mysql_data_seek($result, 0);

			// 加班費附表用
			$sql = "SELECT DISTINCT 現任職稱 FROM `employee` WHERE `部門代號` LIKE '".$dep[0]."%'";
			$PosResult = mysql_query($sql,$link);
			$PosNameArray = array();
			// 建立職稱陣列
			while($row=mysql_fetch_array($PosResult)){
				$PosNameArray[] = $row[0]; 
			}
			mysql_data_seek($PosResult,0);

			// 建立array [[部門] [助理工程師] [研發工程師] [研發副理] [副工程師] [資深工程師]]
			
			$PosNameArray = $GLOBALS['PosNameArray'];
			$count = count($PosNameArray);
			$appd = $GLOBALS['appd']; 
			for ($i=0;$i <$count ; $i++) { 
				// 以職稱為key value為空陣列
				$appd[$PosNameArray[$i]] = array();
			}

			// 從第一個部門開始 加總資料
			$depName_temp = ''; // 記錄部門名稱
			$count = 0;
			while($row = mysql_fetch_array($result,MYSQL_NUM)){
				// $row[2] : 部門名稱
				if($row[2] != $depName_temp && $depName_temp!=''){
					$count++;
				}
				// 若該部門名稱位置沒資料
				if(!$appd['部門'][$count]){
					$appd['部門'][$count] = $row[2];
				}
				// 若該職稱位置沒資料
				if(!$appd[$row[1]][$count]){
					$appd[$row[1]][$count] = 0;
				}
				// 將對應到的職稱位置+1
				 // $row[1] 職稱
				$appd[$row[1]][$count] += $row[14];
				$appd['小計'][$count] += $row[14];
				$depName_temp = $row[2];

			}
			
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
		$depT = 0; // 部門合計
		$grandT =0; //總體合計
		if($choice[0]=='leave'){
			// $base 整理漏的資料項目數
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
		}elseif($choice[0]=='overtime'){
			// 加班費
			while($k<$base){

				$row = $new_row;

				// 部門加總(避免開頭就印)
				if($row[8][$k] != $GLOBALS['dep_name'] and $dep_name != ''){
					$total = number_format((int)$GLOBALS['depT']);
					echo "<tr class='dep-total'><td colspan='4' >".'部門加總'."</td><td>".$total."</td><td></td></tr>";
					$GLOBALS['depT'] = 0;
				}

				// 不同部門，印出名稱
				if($row[8][$k] != $GLOBALS['dep_name']){
					global $DepNameArray;
					echo "<tr><td colspan='6'>".$DepNameArray[$row[8][$k]]."</td></tr>";
				}

				// 姓名
				echo "<tr><td>".$row[0][$k]."</td>";
				// 職稱
				echo "<td>".$row[1][$k]."</td>";
				// 月薪
				echo "<td>".number_format($row[2][$k])."</td>";
				

				// 加班時數
				echo "<td>";
				echo $row[4][$k];
				echo "</td>";


				// 加班費 (月薪/224)*1.5*全年加班總時數
				$bonus = $row[2][$k]/224*1.5*$row[4][$k];
				echo "<td>"; 
				if($bonus<=0){
					echo '-';
				}else{
					$number = $bonus;
					echo number_format((int)($number));
				}"</td>";

				// 佔月薪比例 ($bonus/$row[2][$k]*100)
				
				if($bonus==0){$bonus='-';}else{$bonus=sprintf("%.2f%%",($bonus*100/$row[2][$k]));};
				echo "<td>".$bonus."</td>";

				$GLOBALS['depT'] += $row[2][$k]/224*1.5*$row[4][$k];
				$GLOBALS['grandT'] += $row[2][$k]/224*1.5*$row[4][$k];
				$GLOBALS['dep_name'] = $row[8][$k]; //更新暫存部門名稱

				$k++;

				//最後一次加總
				if($k==$base){
					$total = number_format((int)$GLOBALS['depT']);
					echo "<tr class='dep-total'><td colspan='4'>部門加總</td><td>".$total."</td><td></td></tr>";
					$GLOBALS['grandT'] = number_format((int)$GLOBALS['grandT']);
					echo "<tr class='dep-total'><td colspan='4'>加班費總計金額</td><td>".$GLOBALS['grandT']."</td><td></td></tr>";
				}

			}
				// 列印附表
				echo "<table cellspacing='0' width='600px' border='1'>";
				echo "<tr><td colspan='9'>附表：加班時數摘要</td></tr>";
				echo "<tr><td>部門名稱</td>";
				$PosNameArray = $GLOBALS['PosNameArray'];
				$Poscount = count($PosNameArray);
				for ($i=0; $i <$Poscount ; $i++) { 
					echo "<td>".$PosNameArray[$i]."</td>";
				}
				echo "<td>小計</td></tr>";

				
				$Depcount = count($GLOBALS['appd']['部門']);
				// 導入附表的陣列
				$appd = $GLOBALS['appd'];
				// 導入代號->名稱對應陣列
				$DepNameArray = $GLOBALS['DepNameArray'];
				// 列印每一列資料
				for ($i=0; $i <$Depcount ; $i++) { 
					echo "<tr>";
					echo "<td>".$DepNameArray[$appd['部門'][$i]]."</td>";
					for ($r=0; $r <$Poscount ; $r++) { 

						// 讀不到資料，代表該筆為0
						if(!$appd[$PosNameArray[$r]][$i]){
							$appd[$PosNameArray[$r]][$i] = 0;
						}
						echo "<td>".$appd[$PosNameArray[$r]][$i]."</td>";
					}
					echo "<td>".$appd['小計'][$i]."</td>";
				echo "</tr>";
				}

				// 列印最後一列小計
				echo "<tr><td>小計</td>";
				$hours = 0;
				$Total_hours = 0;
				for ($i=0; $i <$Poscount ; $i++) { 
					for ($r=0; $r <$Depcount ; $r++) { 

						$hours += $appd[$PosNameArray[$i]][$r];
						
					}
					echo "<td>".$hours."</td>";
					
					// 累加最後總計

					$Total_hours += $hours;
					$hours = 0;
				}
				// 列印最後總計
				echo "<td>".$Total_hours."</td>";
				echo "</tr>";
				echo "</table>";
		}
		
		
		
		echo "<input type='submit' value='回選單'>";
		echo "</form></table>";
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

		// 報表種類類 
		echo "<select name='choice'>".
		     "<option value='leave;未休假獎金'>未休假獎金".
			 "<option value='overtime;加班及加班費支領'>加班及加班費支領".
			 "</select>";

		echo "<button type='submit'>送出";
		echo "</form>";
	}
}else{
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>";
	echo "請輸入密碼<input type='text' name='password'>";
	echo "<input type='submit'>";
	echo "</form>";
}


