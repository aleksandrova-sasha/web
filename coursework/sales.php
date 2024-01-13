<?php
	header('Content-type: text/html; charset=utf-8');
	error_reporting(E_ALL);
	include "auth.php";
	if (!in_array($_SESSION['level'], array(10, 5, 1))) { // доступ разрешен только группе пользователей
		header("Location: login.php"); // остальных просим залогиниться
		exit;
	};

	/*
	Скрипт-редактор
	*/
	include "database.php";
	include "func.php";
	include "styles.php";
	include "scripts.php";
	$con=connect();
	$title='Продажи';
	$table='sales';
	$edit=in_array($_SESSION['level'], array(10, 5));

	$param_keys=array('user_id', 'event_id', 'sum'); // названия полей в таблице БД
	$param_str=array('Пользователь', 'Событие', 'Сумма'); // названия столбцов в таблице для отображения
	$param_ext=array('`users`.`fio`', '`events`.`name`', 0); // поля для select
	$param_need='event_id'; // обязательные поля, без которых не сохранять данные
?>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $title;?></title>
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
		<td class="menu2">
			<?php
				include('menu.php');
			?>
		</td>
	<link rel="stylesheet" type="text/css" href="style.css">

		<!-- контент -->
		<td class="content">

<h1><?php echo $title;?></h1>
<?php
	// если надо удалить
	if (!empty($_GET['delete_id'])) {
		$id=intval($_GET['delete_id']);		$query="
			DELETE FROM `$table`
			WHERE id=$id
		";
		mysqli_query($con, $query) or die(mysqli_error($con));
	};

	// если надо редактировать, загружаем данные
	if (!empty($_GET['edit_id'])) {
		$id=intval($_GET['edit_id']);
		$buf='';
		foreach($param_keys as $param_key) {
			$buf.="`$param_key`, ";
		};
		$buf=trim($buf, ', ');
		$query="
			SELECT
				$buf
			FROM `$table`
			WHERE id=$id
		";
		$res=mysqli_query($con, $query) or die(mysqli_error($con));
		$row=mysqli_fetch_array($res);
		foreach($param_keys as $param_key) {
			$param_values[$param_key]=$row[$param_key];
		};
	};

	// если надо сохранить (если не пусто)
	if (!empty($_POST[$param_need])) {
		foreach($param_keys as $param_key) {
			$param_values[$param_key]=mysqli_real_escape_string($con, trim($_POST[$param_key]));
		};


		$fields=''; // собираем строку вида: "`shifr`='$shifr', `organ_id`='$organ_id'";

		for($ind=0; $ind<count($param_keys); $ind++) {
			$fields.='`'.$param_keys[$ind].'`=\''.$param_values[$param_keys[$ind]].'\', ';
		};
		$fields=trim($fields, ', ');
		// если надо сохранить отредактированное
		if (!empty($_REQUEST['hidden_edit_id'])) {
			$id=intval($_REQUEST['hidden_edit_id']);			$query="
				UPDATE `$table`
				SET
					$fields
				WHERE
					id=$id
			";
		}
		else { // добавление новой строки
			$query="
				INSERT INTO `$table`
				SET
					$fields
			";
		};

		mysqli_query($con, $query) or die(mysqli_error($con));	};

	if (isset($_POST['btn_submit'])) // была нажата кнопка сохранить - не надо больше отображать id
		$id=0;

	// добавляем возможность удаления админам
	$delete_confirm="onClick=\"return window.confirm(\'Подтверждаете удаление?\');\"";
	$admin_delete=$edit ? ", CONCAT('<a href=\"$table.php?delete_id=', `$table`.id, '\" $delete_confirm>', 'удалить&nbsp;#', `$table`.id, '</a>') AS 'Удаление'" : '';
	// добавляем возможность редактирования админам
	$admin_edit=$edit ? ", CONCAT('<a href=\"$table.php?edit_id=', `$table`.id, '\">', 'редактировать&nbsp;#', `$table`.id, '</a>') AS 'Редактирование'" : '';
	$buf='';
	for($ind=0; $ind<count($param_keys); $ind++) {		if (!empty($param_ext[$ind])) // если есть дополнительный код
			$buf.=$param_ext[$ind]." AS '".$param_str[$ind]."', ";
		else
			$buf.="`$table`.`".$param_keys[$ind]."` AS '".$param_str[$ind]."', ";
	};
	$buf=trim($buf, ', ');

	$query="
		SELECT
			$buf
			$admin_delete
			$admin_edit
		FROM
			`$table`, users, events
		WHERE 1
			AND `$table`.event_id=events.id
			AND `$table`.user_id=users.id
			AND (
				$_SESSION[level] IN (10, 5)
				OR ($_SESSION[level]=1 AND `users`.`id`=$_SESSION[id])
			)
		ORDER BY `$table`.`id`
	";

	echo SQLResultTable($query, $con, '');
?>

<?php
	// доступ к редактированию только админу
	if ($edit) { // if ($edit)
?>
<form name="form" action="<?php echo $table?>.php" method="post">
	<table>
		<tr>
			<th colspan="2">
				<p>Редактор <?php if (!empty($id)) echo "(редактируется строка с кодом $id)";?></p>
			</th>
		</tr>


<?php
	$buf='';
	for($ind=0; $ind<count($param_keys); $ind++) {		if (!$param_ext[$ind]) { // обычное поле input
			$buf.='
				<tr>
					<td>'.$param_str[$ind].'</td>
					<td>
						<input id="'.$param_keys[$ind].'" name="'.$param_keys[$ind].'" type="text" value="'.$param_values[$param_keys[$ind]].'">
					</td>
				</tr>
			';
		}
		else { // поле с выбором (select)
			$buf.='
				<tr>
					<td>'.$param_str[$ind].'</td>
					<td>
						<select "'.$param_keys[$ind].'" name="'.$param_keys[$ind].'">';
			list($buf_table, $buf_field) =explode('.', $param_ext[$ind]);
			$query="
				SELECT $buf_field AS `name`, `id`
				FROM $buf_table
				WHERE 1
				ORDER BY $buf_field
			";
			$res=mysqli_query($con, $query) or die(mysqli_error($con));
			while ($row=mysqli_fetch_array($res, MYSQLI_ASSOC)) {
				$selected= ($param_values[$param_keys[$ind]]==$row['id']) ? 'selected' : '';
				$buf.= "
							<option value='$row[id]' $selected>$row[name]</option>
				";
			};
			$buf.=  '
						</select>
					</td>
				</tr>
			';
		};
	};

	echo $buf;
?>

	<input name="hidden_edit_id" type="hidden" value="<?php if (!empty($id)) echo $id;?>">

	<tr>
		<td colspan='2'>
			<button id="btn_reset" type="reset">Очистить поля</button>
			<button id="btn_submit" name="btn_submit" type="submit"><?php if (!empty($id)) echo "Сохранить"; else echo "Добавить";?></button>
		</td>
	</tr>
	</table>

</form>
<?php
	}; // if ($edit)
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
