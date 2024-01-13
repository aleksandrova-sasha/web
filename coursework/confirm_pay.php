<?php
	header('Content-type: text/html; charset=utf-8');
	error_reporting(E_ALL);
	include "auth.php";

	include "database.php";
	include "func.php";
	include "styles.php";
	include "scripts.php";
	$con=connect();
	$title='Подтверждение покупки';
?>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $title;?></title>
<script>
	function btn_reset_click() {
		$('input').val('');
	};
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

	$nums=htmlentities(trim($_POST['nums']), ENT_QUOTES, 'UTF-8');
	$nums=array_unique(explode(',', $nums));
	if (count($nums)==0) {
		// ничего не выбрано

	}
	else {
		$cnt=0; // количество продаваемых пользователю билетов
		foreach($nums as $num) {
			$query="
				INSERT INTO tickets
				SET
					user_id=$_SESSION[id],
					event_id=".abs(intval($_POST['hidden_event_id'])).",
					num=$num
			";
			$res=mysqli_query($con, $query) or die(mysqli_error($con));
			if ($res) {
				$cnt++;
			};
		};

		// если все прошло успешно, фиксируем факт продажи, подсчитываем сумму с учетом скидки
		$event_id=abs(intval($_POST['hidden_event_id']));
		$query="
			INSERT INTO sales
			SET
				user_id=$_SESSION[id],
				event_id=$event_id,
				sum=$cnt
					*(SELECT price FROM events WHERE id=$event_id LIMIT 1) # цена за 1 билет
					*ROUND(1-
						(SELECT discount_value FROM users WHERE id=$_SESSION[id] LIMIT 1) # величина индивидуальной скидки
					/100,2)
		";
		$res=mysqli_query($con, $query) or die(mysqli_error($con));
		if ($res) {
			echo "<p>Успешно куплено билетов: $cnt. Ваши билеты можно просмотреть <a href='tickets.php'>здесь</a><br>
			Суммы покупок  <a href='sales.php'>здесь</a></p>";
		};
	};


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
