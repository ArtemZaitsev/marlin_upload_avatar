<?php
session_start();
include 'function.php';

$user_id = $_POST["user_id"];
$new_name_avatar = $_FILES['new_avatar']['name'];


//
//echo $user_id;
//echo "<br>";
//echo $new_name_avatar;
//echo "<hr>";
//echo $new_name_avatar;
//echo "<hr>";
$name_without_ext = substr($new_name_avatar, 0, strrpos($new_name_avatar, '.'));
//echo $name_without_ext;
//echo "<hr>";
//print_r($_FILES);
//die;


//Загрузка файла
$uploaddir = 'img/demo/avatars/avatar-';
$uploadfile = $uploaddir . basename($_FILES['new_avatar']['name']);

//echo '<pre>';
move_uploaded_file($_FILES['new_avatar']['tmp_name'], $uploadfile);


//print_r($_FILES);

//Обновление значения в БД

//$new_name_avatar;


//die;
//echo $path;
$ext = pathinfo($new_name_avatar, PATHINFO_EXTENSION); // мы получим jpg
//echo "<br>";
//echo $ext;
//осталось сделать uniqid() и добавить $ext, в итоге получим уникальное название для файла
//echo "<br>";

$uniq = uniqid();
//echo $uniq;
//echo "<hr>";
//$full_uniq_name = $uniq . "_" . $new_name_avatar . "_". $ext;
//$full_uniq_name = $uniq . "_" . $new_name_avatar;
//echo $full_uniq_name;
//echo "<hr>";
//print_r($_FILES);
//upload_name_avatar($user_id, $name_without_ext);
upload_name_avatar($user_id, $new_name_avatar);

redirect_to("media.php?id=$user_id");