<?php
session_start();
$name=session_name();
	if (!isset($_SESSION['count'])) {
    $_SESSION['count'] = 0;
} else {
    $_SESSION['count']++;
}  
	  
include 'enterForm.php';

	$_SESSION['servername'] = "localhost";
	$_SESSION['username'] = "root";
	$_SESSION['password'] = "";
	$_SESSION['db'] = "mysitedbtest";
	$_SESSION['tbuser'] = "users";
	$_SESSION['tbnews'] = "news";
	$_SESSION['tbcom'] = "comments";
	$_SESSION['tbcategory'] = "category";

// Create connection
$_SESSION['conn'] = mysqli_connect($_SESSION['servername'], $_SESSION['username'], $_SESSION['password']);

// Check connection
if (!$_SESSION['conn']) {
    die("Connection failed: " . mysqli_connect_error());
	}
	echo "Connected successfully";
	echo "<br/>";
	
// Create database
$sql = "CREATE DATABASE ".$_SESSION['db']."";	

	if (mysqli_query($_SESSION['conn'], $sql)) {
    mysqli_query($_SESSION['conn'], $sql);
	echo "Database created successfully";
	} 
	else
	{
	   echo "Error creating database: " . mysqli_error($_SESSION['conn']);
	}
	echo "<br/>";

// select database
mysqli_select_db($_SESSION['conn'], $_SESSION['db']);
echo "Connected to Database";
	echo "<br/>";

// sql to create tables
$sql1 = "CREATE TABLE ".$_SESSION['tbuser']." (id_user INT(11) NOT NULL, first_name VARCHAR(45) NOT NULL, last_name VARCHAR(45) NOT NULL, login VARCHAR(45) NOT NULL, password VARCHAR(45) NOT NULL)";

$sql2 = "CREATE TABLE ".$_SESSION['tbnews']." (id_news INT(11) NOT NULL, login VARCHAR(45) not null, data_time DATETIME NOT NULL, news_header VARCHAR(90) NOT NULL, category VARCHAR(45) NOT NULL, text MEDIUMTEXT NOT NULL)";

$sql3 = "CREATE TABLE ".$_SESSION['tbcom']." (id_comments INT(11) NOT NULL , news_header VARCHAR(90) NOT NULL, login VARCHAR(45) NOT NULL, data_time DATETIME NOT NULL, comment MEDIUMTEXT NOT NULL)";

$sql9 = "CREATE TABLE ".$_SESSION['tbcategory']." (id_category INT(11) NOT NULL, category VARCHAR(45) NOT NULL)";


if (mysqli_query($_SESSION['conn'],$sql1)) {
		mysqli_query($_SESSION['conn'],$sql1);
		echo "Table '".$_SESSION['tbuser']."' created successfully";
		}
	else
	{
    echo "Error creating table: " . mysqli_error($_SESSION['conn']);
	}
	echo "<br>";
if (mysqli_query($_SESSION['conn'],$sql2)) {
		mysqli_query($_SESSION['conn'],$sql2);
		echo "Table $tbnews created successfully";
		}
	else
	{
    echo "Error creating table: " . mysqli_error($_SESSION['conn']);
	}
	echo "<br>";
if (mysqli_query($_SESSION['conn'],$sql3)) {
		mysqli_query($_SESSION['conn'],$sql3);
		echo "Table $tbcom created successfully";
		}
	else
	{
    echo "Error creating table: " . mysqli_error($_SESSION['conn']);
	}	
	echo "<br>";
if (mysqli_query($_SESSION['conn'],$sql9)) {
		mysqli_query($_SESSION['conn'],$sql9);
		echo "Table $tbcategory created successfully";
		}
	else
	{
    echo "Error creating table: " . mysqli_error($_SESSION['conn']);
	}	
	echo "<br>";


// проверка нажатия ввод и регистрация

$op = $_POST['action'];
switch ($op){
	case "enter":
	opensite();
	break;
	case "registration":
	newUser();
	break;
	}

// валидация пароля и пароля и вход на сайт

function opensite()
{

global $tbuser;
global $conn;

$login=$_POST['login'];
$userPassword=$_POST['password'];
		// соединение с БД и проверка наличия пользовател
	$sql="SELECT * FROM ".$_SESSION['tbuser']." WHERE login='".$_POST['login']."'";
	$sqlLog="SELECT login FROM ".$_SESSION['tbuser']." WHERE login='".$_POST['login']."'";
	$sqlPass="SELECT password FROM ".$_SESSION['tbuser']." WHERE login='".$_POST['login']."'";
	$sqlFN="SELECT first_name FROM ".$_SESSION['tbuser']." WHERE login='".$_POST['login']."'";
	$sqlLN="SELECT last_name FROM ".$_SESSION['tbuser']." WHERE login='".$_POST['login']."'";
		
if ($resultLog = mysqli_query($_SESSION['conn'], $sqlLog)) 
{
     while ($rowLog = mysqli_fetch_assoc($resultLog))
	 
		$userLog=$rowLog["login"];

}

if ($resultPass = mysqli_query($_SESSION['conn'], $sqlPass)) 
{
     while ($rowPass = mysqli_fetch_assoc($resultPass))

		$userPass=$rowPass["password"];
		}

if ($resultFN = mysqli_query($_SESSION['conn'], $sqlFN)) 
{
     while ($rowFN = mysqli_fetch_assoc($resultFN))
	 
		$userFN=$rowFN["first_name"];

}

if ($resultLN = mysqli_query($_SESSION['conn'], $sqlLN)) 
{
     while ($rowLN = mysqli_fetch_assoc($resultLN))
	 
		$userLN=$rowLN["last_name"];

}

$result=mysqli_query($_SESSION['conn'],$sql) or trigger_error(mysql_error()." in ".$sql);
		
$userCount=mysqli_num_rows($result);

//закрытие соединения
mysqli_close($_SESSION['conn']);

// валидация логина
if(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['login']))
{
	echo "Логин: только латинские буквы и цифры"."<br>";
}
	elseif(strlen($_POST['login']) < 3 or strlen($_POST['login']) > 30)
   		{
		echo "длина логина от 3 до 30 знаков"."<br>";
		}
		elseif( $userCount=0)
			{
			echo "пользователь не существует";
			}
			elseif ($userLog!=$_POST['login'])
				{
				echo "неверный логин";}
				//валидация пароля				
					elseif(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['password']))
					{
					echo "Пароль: только латинские буквы и цифры"."<br>";
					}
					elseif(strlen($_POST['password']) < 3 or strlen($_POST['password']) > 30)
   						{
						echo "длина пароля от 5 до 30 знаков"."<br>";
						}
						elseif ($userPass==$_POST['password'])
							{
// блок входа на новостную ленту
$_SESSION['login']=$userLog;
$_SESSION['FN']=$userFN;
$_SESSION['LN']=$userLN;
echo "<a href='main.php'>вход на новостную ленту</a>";
}
							else
								{
								echo "неверные логин или пароль"."<br>";
								echo "логин:  ".$userLog ."логин:  ".$_POST['login']."<br>";
					
								echo "пароль: ".$userPass ."логин:  ".$_POST['password']."<br>";	
								}
			

		}

//add new user

function newUser()
{
global $tbuser;
$_SESSION['conn'];
 include 'registrUser.php';


}

?>