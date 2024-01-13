<?php
	/*	Скрипт регистрации юзера */
	header('Content-type: text/html; charset=utf-8');
	error_reporting(E_ALL);
	include('auth.php');
	include('func.php');
	include "styles.php";
	include "scripts.php";
	$title='Регистрация';
	include "database.php";
	$con=connect();
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
			<h1><?php echo $title;?></h1>

<?php
	// если надо сохранить (если не пусто логин и пароль)
	if (!empty($_POST['login']) && !empty($_POST['password']) ) {
		$fio=mysqli_real_escape_string($con, trim($_POST['fio']));
		$email=mysqli_real_escape_string($con, trim($_POST['email']));
		$rank=mysqli_real_escape_string($con, trim($_POST['rank']));
		$password=mysqli_real_escape_string($con, trim($_POST['password']));
		$login=mysqli_real_escape_string($con, trim($_POST['login']));

		$fields="
				`fio`='$fio',
				`rank`='',
				`email`='$email',
				`level`='1',
				`password`='$password',
				`login`='$login'
		";

		$query="
			SELECT COUNT(*)
			FROM `users`
			WHERE 1
				AND `login`='$login'
		";
		$res=mysqli_query($con, $query) or die(mysqli_error($con));
		if (mysqli_fetch_array($res, MYSQLI_BOTH)[0]) {
			echo '<p>Пользователь с таким логином уже существует!</p>';
		}
		else {
			$query="
				INSERT INTO `users`
				SET
					$fields
			";
			$res=mysqli_query($con, $query);
			if ($res) {
				echo '<p>Регистрация прошла успешно!
				<a href="login.php"><u>Авторизуйтесь в системе</u></a>
				</p>';
			}
			else {
				die(mysqli_error($con));
			};
		};
	}
	else if (!empty($_POST['btn_submit'])){
		echo '<p>Введите логин и пароль!</p>';
	};
?>

<head>
	<title>Авторизация</title>
	<meta charset="UTF-8">
	<link href="style.css" rel="stylesheet" />
</head>

<form name="form" action="reg.php" method="post">
	<table>
		<tr>
			<td>Фамилия и имя</td>
			<td>
				<input id="fio" name="fio" type="text" value="<?php if (!empty($fio)) echo $fio;?>" required>
			</td>
		</tr>

<!--
		<tr>
			<td>Должность</td>
			<td>
				<input id="rank" name="rank" type="text" value="<?php if (!empty($rank)) echo $rank;?>">
			</td>
		</tr>
		-->

		<tr>
			<td>Логин</td>
			<td>
				<input id="login" name="login" type="text" value="<?php if (!empty($login)) echo $login;?>" required>
			</td>
		</tr>

		<tr>
			<td>Пароль</td>
			<td>
				<input id="password" name="password" type="password" value="<?php if (!empty($password)) echo $password;?>" required>
			</td>
		</tr>

		<tr>
			<td>Email (необязательно)</td>
			<td>
				<input id="email" name="email" type="email" value="<?php if (!empty($email)) echo $email;?>">
			</td>
		</tr>

	<tr>
		<td colspan='2'>
			<button id="btn_reset" type="reset">Очистить поля</button>
			<button id="btn_submit" name="btn_submit" type="submit">Сохранить</button>
		</td>
	</tr>
	</table>

</form>


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
