<?php
/*
 * Fichier de description des menus
 *
 *	$menuVar = array(
 *		'menuClass'' => str|array(str),		classe du menu
 *		'itemTag' => str|array(str),		defaut = 'li', nom de la balise des éléments du menu
 *		'currentItem' => str|array(str),	nom de la classe utilisée pour l'élément courant du menu
 *		'items' => array(					liste des éléments du menu
 *			str => array(					nom affiché de l'élément du menu
 *				'link' => str,				lien cake du style /nomContoleur/index
 *				'title' => str,				infobulle
 *				'subMenu' => array()		sous-menu qui a la même structure que le menu
 *			)
 *		)
 *	)
 *
 */

$menu= array(
	'menuClass' => array('menuNiveau0', 'menuNiveau1'),
	'currentItem' => 'menuCourant',
	'items' => array('Accueil' => array('link' => '/dossiers/index')));
?>