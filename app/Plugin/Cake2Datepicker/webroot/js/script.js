/* global $$, Cake2Datepicker, SelectCalendar, selectOption */

/**
 * Override selectOption() from protocalendar.js
 * Add support for zero filled date numbers
 */
selectOption = function(select, value) {
	var selectEl = $(select);
	var options = selectEl.select('option');
	for (var i = 0; i < options.length; i++) {
		if (parseInt(options[i].value, 10) === parseInt(value.toString(), 10)) {
			options[i].selected = true;
			return;
		}
	}
};

Cake2Datepicker.extractMinMax = function(select) {
	var options = select.select('option'),
		i,
		results = {min: Number.POSITIVE_INFINITY, max: Number.NEGATIVE_INFINITY, "null": false},
		value;
	
	for (i=0; i<options.length; i++) {
		value = parseInt(options[i].getAttribute('value'), 10);
		if (value < results.min) {
			results.min = value;
		}
		if (value > results.max) {
			results.max = value;
		}
		if (isNaN(value)) {
			results.null = true;
		}
	}
	
	return results;
};

$$(Cake2Datepicker.div_input_selector).each(function(div) {
	'use strict';
	var day, month, year, id, minMaxYear, remover;

	div.select('select').each(function(select) {
		'use strict';
		var name = select.getAttribute('name');
		if (!day && /^data\[.*\]\[day\]$/.test(name)) {
			day = select.id;
		}
		else if (!month && /^data\[.*\]\[month\]$/.test(name)) {
			month = select.id;
		}
		else if (!year && /^data\[.*\]\[year\]$/.test(name)) {
			year = select.id;
			minMaxYear = Cake2Datepicker.extractMinMax(select);
		}
	});

	if (day && month && year) {
		id = day.substr(0, day.length-3) + '_Cake2Datepicker';
		div.insert(new Element('img', {src: Cake2Datepicker.img_calendar, "class": 'Cake2Datepicker calendar', id: id}));
		
		SelectCalendar.createOnLoaded(
			{
				yearSelect: year,
				monthSelect: month,
				daySelect: day
			},
			{
				startYear: minMaxYear.min,
				endYear: minMaxYear.max,
				lang: Cake2Datepicker.lang,
				triggers: [id]
			}
		);

		if (minMaxYear.null) {
			remover = new Element('img', {src: Cake2Datepicker.img_remover, "class": 'Cake2Datepicker remover'});
			remover.observe('click', function() {
				$(year).setValue('');
				$(month).setValue('');
				$(day).setValue('');
			});
			div.insert(remover);
		}
	}
});