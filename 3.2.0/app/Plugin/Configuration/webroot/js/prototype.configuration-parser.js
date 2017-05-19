
	if (ConfigurationParser === undefined) {
		var ConfigurationParser = {};
	}
	
	ConfigurationParser.defaultVars = {
		buttonClass: 'configuration-parser-btn',
		infoBlockClass: 'configuration-parser-info-block',
		containerClass: 'configuration-parser-container'
	};
	
	if (ConfigurationParser.vars === undefined) {
		ConfigurationParser.vars = {};
	}
	
	for (var key in ConfigurationParser.defaultVars) {
		if (!ConfigurationParser.defaultVars.hasOwnProperty(key)) {
			continue;
		}
		
		if (ConfigurationParser.vars[key] === undefined) {
			ConfigurationParser.vars[key] = ConfigurationParser.defaultVars[key];
		}
	}
	
	ConfigurationParser._uniqid = function(base, index) {
		if (index === undefined) {
			index = 0;
		}
		
		if ($(base)) {
			if ($(base+'_'+(index +1))) {
				return ConfigurationParser._uniqid(base, index +1);
			} else {
				return base+'_'+(index +1);
			}
		} else {
			return base;
		}
	};
	
	ConfigurationParser.incrustationInfo = function(selector, json) {
		$$(selector).each(function(element) {
			var key = element.innerHTML.trim(),
				infoButton,
				infoBlock,
				id,
				comment,
				exploded,
				i,
				multikey = [];

			if (json[key] !== undefined) {
				comment = json[key].comment;
			} else if (key.indexOf('.')) {
				exploded = key.split('.');
				for (i = 0; i < exploded.length -1; i++) {
					multikey.push(exploded[i]);
					if (json[multikey.join('.')] !== undefined) {
						comment = json[multikey.join('.')].comment;
					}
				}
			}

			if (comment) {
				id = ConfigurationParser._uniqid('info-'+key.replace(/\./g, '-'));
				element.addClassName(ConfigurationParser.vars.containerClass);
				
				infoButton = new Element('div', {'class': ConfigurationParser.vars.buttonClass, 'for': id});
				infoButton.observe('click', function(event){
					var target = $(event.target.getAttribute('for'));
					console.log($(target));
					$(event.target.getAttribute('for')).toggle();
					event.target.toggleClassName('active');
				});
				infoBlock = new Element('div', {'class': ConfigurationParser.vars.infoBlockClass, 'id': id});
				infoBlock.insert(comment).hide();

				element.innerHTML = '';
				element.insert(infoButton)
					.insert(key)
					.insert(infoBlock);
			}
		});
	};