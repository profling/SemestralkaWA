<?php
/** @var \PDO $db - připojení k databázi */
$db = new PDO('mysql:host=127.0.0.1;dbname=polo03;charset=utf8', 'polo03', 'ein7ohvaeMei9semoh');
//TODO nezapomeňte v předchozím řádku zadat své xname a heslo k databázi

//při chybě v SQL chceme vyhodit Exception
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
