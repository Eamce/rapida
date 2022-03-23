<?php


require_once("class.frajax.php");

$frAjax = new ForRequestAjax($_POST);



if($_GET['request'] == 'l_bu2'):
	$frAjax->l_bu2();
endif;

if($_GET['request'] == 'l_dept2'):
	$frAjax->l_dept2();
endif;


?>