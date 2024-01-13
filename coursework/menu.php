<?php
error_reporting(0);
	$menu= '
	<ul class="main_menu">
	';

	if ( !isset($_SESSION['level'])) $menu.='<li> <a href="reg.php">Регистрация</a> </li>';
	if ( !isset($_SESSION['level'])) $menu.='<li> <a href="login.php">Авторизация</a> </li>';
	else {
		$menu.='Вы вошли под логином '.$_SESSION['login'].' <a href="?do=exit">Выход</a>';
	};

	// пункты, доступные всем без регистрации
	if (empty($_SESSION['level'])) {
	};

	// пункты, доступные всем
	$menu.='<li> <a href="index.php">Главная</a> </li>';
	$menu.='<li> <a href="events.php">Сеансы</a> </li>';

	// меню по уровням доступа: 10 - админ и т.д.
	if ( in_array($_SESSION['level'], array(10)) ) $menu.='<li> <a href="users.php">Пользователи</a> </li>';
	if ( in_array($_SESSION['level'], array(10)) ) $menu.='<li> <a href="levels.php">Уровни доступа</a> </li><br>';

	if ( in_array($_SESSION['level'], array(10, 5, 1)) ) $menu.='<li> <a href="tickets.php">Билеты</a> </li>';
	if ( in_array($_SESSION['level'], array(10, 5, 1)) ) $menu.='<li> <a href="sales.php">Продажи</a> </li>';

	if ( in_array($_SESSION['level'], array(10, 5, 1)) ) $menu.='<li> <a href="pay.php">Купить билет</a> </li>';

	$menu.='</ul>';
	echo $menu;

?>

