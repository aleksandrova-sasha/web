<?php
	include 'database.php';
	function connect() {
		global $hostname;
		global $mysql_login;
		global $mysql_password;
		global $database;
		$con = mysqli_connect($hostname, $mysql_login, $mysql_password, $database) or die(mysqli_error($con));
		if (!$con) die('<h2>Ошибка подключения к серверу базы данных!</h2>');
		mysqli_set_charset($con,'utf8') or die(mysqli_error($con));
		return $con;
	};

// функция печатает результат запроса в виде html-таблицы
function SQLResultTable($Query, $con, $mask) {

	function mysqli_field_name($result, $field_offset) {
    $properties = mysqli_fetch_field_direct($result, $field_offset);
    return is_object($properties) ? $properties->name : null;
	};

	$Table = "";  //инициализировать табличную переменную

	$Table.= "<table id='myTable' border='1' style=\"border-collapse: collapse;\" class=\"tablesorter\">"; //Открыть HTML-таблицу

	$Result = mysqli_query($con, $Query); //Выполнение запроса
	if(mysqli_error($con)) {
		$Table.= "<tr><td>MySQL ERROR: " . mysqli_error($con) . "</td></tr>";
	}
	else {
		//Строка заголовка с именами полей
		$NumFields = mysqli_num_fields($Result);
		$Table.= "<thead>";
		$Table.= "<tr style=\"background-color: #4D84F9; color: #FFFFFF;\">";
		for ($i=0; $i < $NumFields; $i++)
		{
			$Table.= "<th>" . mysqli_field_name($Result, $i) . "</th>";
		}
		$Table.= "</tr>";
		$Table.= "</thead>";

		//Цикл по результатам
		$Table.= "<tbody>";
		$RowCt = 0; //Счетчик строк
		while($Row = mysqli_fetch_assoc($Result))
		{
			//Чередуем цвета для строк
			if($RowCt++ % 2 == 0) $Style = "background-color: #ADFFFF;";
			else $Style = "background-color: #F0FFAD;";

			$Table.= "<tr style=\"$Style\">";
			//Пройдем по каждому полю
			foreach($Row as $field => $value)
			{
				// делаем подсветку найденного
				$value=str_replace($mask, "<font color='red'>$mask</font>", $value);
				// отображаем значение
				$Table.= "<td>$value</td>";
			}
			$Table.= "</tr>";
		}
//		$Table.= "<tr style=\"background-color: #000066; color: #FFFFFF;\"><td colspan='$NumFields'>Найдено записей: " . mysqli_num_rows($Result) . "</td></tr>";
	}
	$Table.= "</tbody>";
	$Table.= "</table>";

	return $Table;
};

	function make_upload($file, $id){
		// формируем уникальное имя name
		$name = $id.'.jpg';
		copy($file['tmp_name'], 'upload/' . $name);
	}

error_reporting(0);
?>