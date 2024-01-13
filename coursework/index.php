<?php
	/*
	Главная страница
	*/
	header('Content-type: text/html; charset=utf-8');
	error_reporting(E_ALL);
	include('auth.php');
	include('func.php');
	include "styles.php";
	include "scripts.php";
	$title='Главная';
?>

<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $title;?></title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
<table id="main_table" border="0">
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
		<td class='menu2'>
			<?php
				include('menu.php');
			?>
		</td>

		<!-- контент -->
		<td class="content">
			<h2>В данной программе осуществляется продажа билетов в кинотеатр  </h2>
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
