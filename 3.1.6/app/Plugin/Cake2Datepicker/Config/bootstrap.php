<?php

/**
 * @params array
 *		'css' => Router::url() path to style
 *		'img' => Html->script() path to calendar's icon
 *		'div_input_selector' => The <div></div> around date's selects
 */
Configure::write('Cake2Datepicker.config',
	array(
//		'css' => '/cake2_datepicker/css/metal.css',
//		'css' => '/cake2_datepicker/css/paper.css',
		'css' => '/cake2_datepicker/css/simple.css',
//		'css' => '/cake2_datepicker/css/ext-simple.css',
//		'css' => '/cake2_datepicker/css/jquery-calendar.css',
		'img_calendar' => '/cake2_datepicker/img/icon_calendar.gif',
		'img_remover' => '/cake2_datepicker/img/error.gif',
		'div_input_selector' => 'div.input.date, div.input.datetime',
	)
);