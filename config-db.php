<?php
$pdo = new PDO('mysql:host=localhost;dbname=db_MAZOU;charset=UTF8','22507451','230352');
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>