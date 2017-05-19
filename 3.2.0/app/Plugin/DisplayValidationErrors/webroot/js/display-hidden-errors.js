/* global $$, DisplayValidationErrors */

DisplayValidationErrors.errorString = function () {
	'use strict';
	var existingErrors = [],
		errorString = '',
		key,
		i,
		matches,
		prefix = '(?:\\[[\\w]+\\])?',
		index = '(?:\\[[\\d]*\\])?',
		capture = '(?:\\[([\\w]+)\\])',
		regexBase = '^data' + prefix + index + capture + index + capture + index,
		regex = new RegExp(regexBase + '$'),
		regexDate = new RegExp(regexBase + '(?:\\[(?:day|month|year)\\])$');

	$$('div.error-message').each(function(div) {
		div.up().select('input, select, textarea').each(function(editable) {
			if (/\[(day|month|year)\]$/.test(editable.getAttribute('name'))) {
				matches = editable.getAttribute('name').match(regexDate);
			} else {
				matches = editable.getAttribute('name').match(regex);
			}

			if (matches.length
				&& editable.visible()
				&& (editable.getAttribute('type') === null
					|| editable.getAttribute('type').toLowerCase !== 'hidden')
			) {
				existingErrors.push(matches[1] + '.' + matches[2]);
			}
		});
	});

	errorString += '<ul>';
	for (key in DisplayValidationErrors.errors) {
		if (!DisplayValidationErrors.errors.hasOwnProperty(key) || existingErrors.indexOf(key) !== -1) {
			continue;
		}

		errorString += '<li><span title="'+key+'">'+DisplayValidationErrors.traductions[key]+'</span><ul>';

		for (i=0; i<DisplayValidationErrors.errors[key].length; i++) {
			errorString += '<li>'+DisplayValidationErrors.errors[key][i]+'</li>';
		}

		errorString += '</li></ul>';
	}
	errorString += '</ul>';
	
	return errorString;
};

$$(DisplayValidationErrors.identifier).first().insert(DisplayValidationErrors.errorString());