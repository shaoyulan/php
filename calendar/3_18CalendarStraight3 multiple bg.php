<!-- 輸入年分，產生當年月曆 -->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="short icon" href="">
<meta name="description" content="calendar">
<meta name="keyword" content="calendar">
<meta property="og:title" content="calendar">
<meta property="og:description" content="calendar">
<meta property="og:type" content="website">
<meta property="og:url" content="">
<meta property="og:image" content="">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<title>while 重覆結構</title>
<style>
	:root{--light-orange:rgba(241,165,4);}
	*{
		-webkit-box-sizing: border-box;
	    -moz-box-sizing: border-box;
	    box-sizing: border-box;
	    font-family: "微軟正黑體"
	}
	.clearfix:after{content:'';display: table;clear:both;}
	}
	
	.wrap{width:100%;} /*最外層*/
	.left_bar{width:10%;height:827.68px;display:inline-block;float:left;padding:100px 0 0 0;}
	.container{width:85%;margin: 0 auto;display:inline-block;float:left;position: relative;}
	
	/*所有背景圖片基本格式*/
	.bgimg:after{content:'';}
	.bgimg{
		width: 100%;
		height:2000px;
		position:fixed;
		z-index: -2;
		top: 0;
		left: 0;
		background-position: cover;	
		background-attachment: scroll;
		transition: 2s;
	}
	
	.img1{
		background-image: url('bgimg5.jpg');
/*		z-index: -1;*/
	}
	.img2{
		background-image: url('bgimg4.jpg');

	}
	
	#check1{display: none}
	#check1:checked +.img1{	z-index: -1;transition:6s;}
	#check1:checked ~.img2{	opacity: 0;}

	.check1{width:80px;height: 30px;display: inline-block;text-align: center;border:3px solid rgb(227,97,3);border-radius: 5px}
	.check1:hover{background:var(--light-orange);transition: 1s;cursor:pointer;}

	.wraper{display:flex;flex-wrap:wrap}
	.block{margin: 0 1%;padding-bottom: 20px;}
	table{text-align: center;}
	td{width:50px;}
	.title{text-align: center}
	h1{text-align: center}
	@media only screen and (max-width: 576px){
		.check1{
			position: absolute;
			top: 10px;
		}
	}
</style>
</head>
<body>


<div class="wrap">
	

	<div class="left_bar">
		<input type="checkbox" id="check1">
		
		<!-- <img src="bgimg5.jpg" class="img1">
		<img src="bgimg4.jpg" class="img"> -->

		<div class="bgimg img1"></div>
		<div class="bgimg img2"></div>

		<label for="check1" class="check1">
			<font color="white">下一張</font>
		</label>


	</div>

	<div class="container clearfix">
		<?php 
	    

		include '3_testDays.php';

		$YEAR=$_REQUEST["number"]; 
		$isLeap = False;
		$days=0; //空變數
		// $NowCol = $_POST['Start']; //紀錄現在是在第幾格(1~7)
		$NowCol = 0;
		$Month=""; //空變數


		//判斷今年是否是閏年
		if (($YEAR%400 == 0) or (($YEAR%100 !== 0) and ($YEAR%4 == 0))) {
			$isLeap = True;
		}else{
			$isLeap = False;
		}


		//計算今年第一天星期幾
		$NowCol = (1 + $Add_Days)%7+1; //星期一是第2格，故都要加1

		//網頁標題
		echo "<h1>西元".$YEAR."</h1>";
		echo "<div class='wraper'>";
		//程式開始
		for ($m=1; $m <=12 ; $m++) { 

			//決定個月天數、標題
			switch ($m) {
				case '1':
					$Month = "一月";
					$days = 31;
					break;
				case '2':
					if($isLeap){
						$Month = "二月";
						$days = 29;
					}else{
						$Month = "二月";
						$days = 28;
					}
					break;
				case '3':
					$Month = "三月";
					$days = 31;
					break;
				case '4':
					$Month = "四月";
					$days = 30;
					break;
				case '5':
					$Month = "五月";
					$days = 31;
					break;
				case '6':
					$Month = "六月";
					$days = 30;
					break;
				case '7':
					$Month = "七月";
					$days = 31;
					break;
				case '8':
					$Month = "八月";
					$days = 31;
					break;
				case '9':
					$Month = "九月";
					$days = 30;
					break;
				case '10':
					$Month = "十月";
					$days = 31;
					break;
				case '11':
					$Month = "十一月";
					$days = 30;
					break;
				case '12':
					$Month = "十二月";
					$days = 31;
					break;
			}

			echo "<div class='block'><div class='title'>".$Month."</div>";
			echo "<table align='center' valign='middle' border='1px' cellspacing='0'>";
			echo "<tr>
			          <td><font color='red'>SUN</font></td>
			          <td>MON</td>
			          <td>TUE</td>
			          <td>WEN</td>
			          <td>THR</td>
			          <td>FRI</td>
			          <td><font color='red'>SAT</font></td>
			      </tr>";

			echo "<tr>";

			//決定由第幾欄開始印
			
			//插入空白欄位
			for($s=1;$s<$NowCol;$s++){
				echo "<td></td>";
			}


			 //每次印7欄	

			 For($i=1;$i<=$days;$i++){

		        
			 	if ($NowCol==7){ //每個月最後一天剛好在lastcell，不要新增tr
			 		echo "<td><font color='red'>".$i."</font></td></tr><tr>";
			 		$NowCol = 0; //必須設0，下面+1，下回由1開始
			 	}elseif($NowCol==1) { //第1格紅色
			 		echo "<td><font color='red'>".$i."</font></td>";
			 	}else{
			 		echo "<td>".$i."</td>";
			 	}
			 	$NowCol += 1; //讓定位+1
			 }


			 //決定是否補上格子
			 // if(($NowCol==0)and($i==($days))){
				//  echo ""; //不補:當定位在0，而最後一天已經印了，不要補格子
			 // }else{
			 // 	for($s=$NowCol;$s<=7;$s++){
			 // 	echo "<td></td>"; //補上：最後沒數字的格子
				//  }
			 // }

			  if($NowCol<>1){
			 	 for($s=$NowCol;$s<=7;$s++){
			 	 echo "<td></td>";
			 	 }//不補:當定位在0，而最後一天已經印了，不要補格子
			  }

			 echo "</tr>";
			 echo "</table></div>";

		}
		


		?>
		</div>
	</div>
	
</div>

</body>
</html>
