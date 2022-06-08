<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Tempstravail.libelle',
				'/Tempstravail/edit/#Tempstravail.id#' => array(
					'title' => true
				),
				'/Tempstravail/delete/#Tempstravail.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Tempstravail.has_linkedrecords#"'
				)
			)
		)
	);