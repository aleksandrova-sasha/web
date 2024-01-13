<?php
	header('Content-type: text/html; charset=utf-8');
	error_reporting(E_ALL);
	include "auth.php";

	include "database.php";
	include "func.php";
	include "styles.php";
	include "scripts.php";
	$con=connect();
	$title='Купить билет';
?>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $title;?></title>
<script>
	function btn_reset_click() {
		$('input').val('');	};
</script>

</head>

<body>
<table border="0">
	<!-- баннер -->
	<tr>
		<td colspan=2 style="text-align:center">
			<?php
				include('top.php');
			?>
		</td>
	</tr>

	<tr>
		<!-- меню -->
		<td width="40%" class="menu2">
			<?php
				include('menu.php');
			?>
		</td>
	<link rel="stylesheet" type="text/css" href="style.css">

		<!-- контент -->
		<td class="content">

<h1><?php echo $title;?></h1>
<?php
	$event_id=empty($_REQUEST['event_id']) ? 0 : abs(intval(trim($_REQUEST['event_id'])));
	if (!$event_id) { 	// если не было выбрано событие, показываем все события для выбора
		
		$query="
			SELECT id, name, dt
			FROM events
			WHERE 1
				AND CAST(dt AS DATE) >= CAST(NOW() AS DATE)
			ORDER BY dt, name
		";
		$res=mysqli_query($con, $query) or die(mysqli_error($con));
		$buf='
		<form name="form_show_events" action="pay.php" method="POST">
			<select name="event_id">';
		while ($row=mysqli_fetch_array($res, MYSQLI_ASSOC)) {
			$buf.= "
					<option value='$row[id]' $selected>$row[dt] $row[name]</option>
			";
		};
		$buf.='</select>';
		echo $buf;
		echo '

		<input type="submit" value="Выбрать сеанс">
		</form>';
	}
	else { // событие выбрано
		// показываем места, которые заняты
		$query="
			SELECT num
			FROM tickets
			WHERE 1
				AND tickets.event_id=$event_id
			ORDER BY num
		";
		$res=mysqli_query($con, $query) or die(mysqli_error($con));
		$occup=array();
		while ($row=mysqli_fetch_array($res, MYSQLI_ASSOC)) {
			$occup[]=$row['num'];
		};

?>
<script>
	function add_num(id) {
		if (id==' ') return 0; // если выбран уже купленный билет, игнорируем		if ($('#nums').val()!='')
			$('#nums').val($('#nums').val()+','+id)
		else			$('#nums').val(id);
	};
</script>

<?php
		echo "
		<form name='form_buy' action='confirm_pay.php' method='POST'>
		<table>";
		for($i=1; $i<=10; $i++) {
			echo "<tr>";
			for($j=1; $j<=10; $j++) {
				$x=(in_array(($i-1)*10+$j, $occup)) ? " " : ($i-1)*10+$j;
				// красным цветом показать места, которые нельзя забронировать
				// зеленым - которые можно забронировать
				$color= (in_array(($i-1)*10+$j, $occup)) ? "FF0000" : "00FF00";
				echo "
					<td width='20px' bgcolor=#CCCCCC onclick='add_num(\"$x\")'
						onMouseOver=\"this.style.background='#$color'\"
						onMouseOut=\"this.style.background='#CCCCCC'\">".$x."</td>";
			};
			echo "</tr>";
		};
		echo "</table>
		<div>Выбраны номера мест:

			<input name='nums' type='text' id='nums' readonly>
		</div>
		<input name='hidden_event_id' type='hidden' value='".$_POST['event_id']."'>
		<input type='submit' value='Купить билеты'>
		</form>
		";	};

?>
		</td>
	</tr>

	<!-- подвал -->
	<tr>
		<td colspan=2>
			<?php
				include('footer.php');
			?>
		</td>
	</tr>

</table>

</body>
</html>
