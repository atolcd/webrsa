<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'permissions' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	App::uses( 'WebrsaPermissions', 'Utility' );
	$options['Permissions'] = array(
		WebrsaPermissions::ACCES_HERITE => 'Hérité',
		WebrsaPermissions::ACCES_OUI => 'Oui',
		WebrsaPermissions::ACCES_NON => 'Non'
	);
?>
<div>
	<input type="button" id="btn-all-non" value="Accès refusé" />
	<input type="button" id="btn-all-herite" value="Accès herité" />
	<input type="button" id="btn-all-oui" value="Accès accepté" />
	<input type="button" id="btn-all-actual" value="Accès actuel" />
	<input type="button" id="btn-all-parent" value="Copie parent" />
</div>
<?php
	echo $this->WebrsaPermissions->table(
		$this->WebrsaPermissions->acosTree($acos),
		array(
			'parentPermissions' => $parentPermissions,
			'options' => $options
		)
	);
?>
<script type="text/javascript">
	//<![CDATA[
	/**
	 * @todo init avec des paramètres (parentField - Group.parent_id, User.group_id - tableClassName - permissions)
	 */
	var WebrsaPermission = {
			images: <?php echo json_encode( $this->WebrsaPermissions->images() );?>,
			// @todo: faudrait l'utiliser
			accesses: <?php echo json_encode( array(
				WebrsaPermissions::ACCES_OUI => 'yes',
				WebrsaPermissions::HERITE_OUI => 'yes',
				WebrsaPermissions::ACCES_NON => 'no',
				WebrsaPermissions::HERITE_NON => 'no',
				WebrsaPermissions::ACCES_HERITE => 'inherit'
			) );?>,
			domId: function(path) {
				return fieldId('Permission.' + path.replace(/[\/\.]/g, '_'))
			},
			removeValue: function( tr, column ) {
				$(tr)
					.down('td.' + column)
					.removeClassName('yes')
					.removeClassName('no')
					.removeClassName('inherit')
					.removeClassName('inherit_yes')
					.removeClassName('inherit_no')
					.update();
			},
			setValue: function( tr, column, value ) {
				this.removeValue(tr, column);
				$(tr)
					.down('td.' + column)
					.addClassName(value)
					.update(this.images[value]);
				if('real' === column) {
					$(tr)
						.removeClassName('yes')
						.removeClassName('no')
						.removeClassName('inherit')
						.removeClassName('inherit_yes')
						.removeClassName('inherit_no')
						.addClassName(value);
				}
			},
			getValue: function( tr, column ) {
				var classNames = $(tr).down('td.' + column).readAttribute('class'),
					matches = classNames.match(/\b(inherit_yes|inherit_no|yes|no|inherit)\b/);
				return (null === matches) ? null : matches[0];
			},
			getClassName: function( tr ) {
				var classes = $(tr).readAttribute('class'),
					re = /\b(aco_[^ ]*)\b/g,
					match,
					result = [];

					do {
						match = re.exec(classes);
						if (null !== match) {
							result.push(match[1]);
						}
					} while (null !== match);

				return result.join('.');
			},
			getLevel: function(tr) {
				return parseInt($(tr).readAttribute('class').replace(/.*\blevel([0-9]+)\b.*$/g, '$1'), 10);
			},
			getParent: function(tr) {
				var level = this.getLevel(tr), selector, parents;
				if(0 === level) {
					return null;
				}
				selector = 'level' + ( level - 1 ) + '.' + this.getClassName(tr).replace( /\.[^\.]+$/g, '' );
				parents = $$('tr.' + selector);

				return parents.length > 0 ? parents[0] : null;
			},
			getChildren: function(tr) {
				var level = this.getLevel(tr),
					selector = 'level' + ( level + 1 ) + '.' + this.getClassName(tr);

				return $$('tr.' + selector);
			},
			compute: function(tr) {
				var real = null,
					value = $F($(tr).down('select'));

				if( '<?php echo WebrsaPermissions::ACCES_OUI;?>' === value.toString() ) {
					real = 'yes';
				}
				else if( '<?php echo WebrsaPermissions::ACCES_NON;?>' === value.toString() ) {
					real = 'no';
				}
				else {
					real = getHerited(tr);
				}

				return real;
			},
			init: function() {
				var $this = this;
				$$('table.permissions tbody tr').each(function(tr) {
					$this.setValue(tr, 'real', $this.compute(tr));
				});
				// @todo une fonction, faire de même pour le niveau précédent
				$$('table.permissions tbody tr.level1').each(function(tr) {
					var children = $this.getChildren(tr),
						parentPermission = WebrsaPermission.getValue(tr, 'parent'),
						hideChildren = true === $(children).every( function (element) {
							return '<?php echo WebrsaPermissions::ACCES_HERITE;?>' == $F($(element).select('select')[0]);
						} )
						&& true === $(children).every( function (element) {
							return parentPermission == WebrsaPermission.getValue(element, 'parent');
						} ),
						th = $(tr).select('th')[0];

						if(true === hideChildren) {
							$(th).update( $this.images['plus'] + ' ' + $(th).innerHTML );
							$(children).each( function( child ) { $(child).hide(); } );
						} else {
							$(th).update( $this.images['minus'] + ' ' + $(th).innerHTML );
							$(children).each( function( child ) { $(child).show(); } );
						}
				});
			},
			refresh: function() {
				var $this = this;
				$$('table.permissions tr.level0').each(function(tr) {
					$this.setValue(tr, 'real', $this.compute(tr));
					propagate(tr);//@todo this
				});
			},
			column2Select(tr, column) {
				var select = $(tr).down('select'),
					value = this.getValue(tr, column);
				if (/^inherit/.test(value)) {
					$(select).setValue('<?php echo WebrsaPermissions::ACCES_HERITE;?>');
				} else if ('yes' === value) {
					$(select).setValue('<?php echo WebrsaPermissions::ACCES_OUI;?>');
				} else {
					$(select).setValue('<?php echo WebrsaPermissions::ACCES_NON;?>');
				}
			},
			reset: function() {
				var $this = this;
				$$('table.permissions tbody tr').each(function(tr) {
					$this.column2Select(tr, 'actual');
				});
				$this.refresh();
			},
			parent: function() {
				var $this = this;
				$$('table.permissions tbody tr').each(function(tr) {
					$this.column2Select(tr, 'parent');
				});
				$this.refresh();
			}
		},
		// ---------------------------------------------------------------------
		getHerited = function(tr) {
			var level = WebrsaPermission.getLevel(tr),
				real = null,
				levels = [],
				i,
				prev,
				value;

			if(0 === level) {
				real = WebrsaPermission.getValue(tr, 'parent').replace(/inherit_{0,1}/g, '');
				real = null === real || '' === real
					? 'inherit_no'
					: 'inherit_' + real;
			}
			else {
				levels[level] = tr;
				prev = WebrsaPermission.getParent(tr);
				while(null !== prev) {
					levels[WebrsaPermission.getLevel(prev)] = prev;
					prev = WebrsaPermission.getParent(prev);
				}

				// select parent ?
				for(i = level - 1 ; i >= 0 ; i--) {
					if(null === real) {
						value = $F(levels[i].down('select'));
						if( '<?php echo WebrsaPermissions::ACCES_OUI;?>' === value ) {
							real = 'inherit_yes';
						}
						else if( '<?php echo WebrsaPermissions::ACCES_NON;?>' === value ) {
							real = 'inherit_no';
						}
					}
				}

				// Parent
				for(i = level ; i >= 0 ; i--) {
					if(null === real) {
						//value = getPermission(levels[i].down('td.parent'));
						value = WebrsaPermission.getValue(levels[i], 'parent');
						if('yes' === value || 'no' === value) {
							real = 'inherit_' + value;
						}
					}
				}

				if(null === real) {
					real = 'inherit_no';
				}
			}

			return real;
		},
		setAll = function( value ) {
			// Niveau 0
			$$('table.permissions tr.level0 select').each(function(element) {
				if (element.getValue() !== value) {
					element.setValue(value);
				}
			});
			// Niveaux 1 et 2
			$$('table.permissions tr.level1 select', 'table.permissions tr.level2 select').each(function(element) {
				element.setValue('<?php echo WebrsaPermissions::ACCES_HERITE;?>');
			});

			WebrsaPermission.refresh();
		},
		propagate = function(tr) {
			var level = WebrsaPermission.getLevel(tr),
				selector = 'level' + ( level + 1 ) + '.' + WebrsaPermission.getClassName(tr);

			// Petits enfants, si on change la racine
			$$('tr.' + selector).each(function(child) {
				WebrsaPermission.setValue(child, 'real', WebrsaPermission.compute(child));

				if( 0 === level ) {
					propagate(child);
				}
			});
		},
		directChildren = function( tr ) {
			var level = WebrsaPermission.getLevel(tr),
				selector = WebrsaPermission.getClassName(tr);

			return $$( 'tr.level' + ( level + 1 ) + '.' + selector );
		},
		toggleChildren = function( link ) {
			var th = $(link).up( 'th' ),
				tr = $(th).up( 'tr' ),
				hidden = $(link).hasClassName('plus'),
				children = directChildren( tr );

			if(true === hidden) {
				$(children).each( function( child ) {
					$(child).show();
				} );
				$(link).remove();
				$(th).update( WebrsaPermission.images['minus'] + ' ' + $(th).innerHTML );
			} else {
				$(children).each( function( child ) {
					$(child).hide();
				} );
				$(link).remove();
				$(th).update( WebrsaPermission.images['plus'] + ' ' + $(th).innerHTML );
			}
			return false;
		};

	// Initialisation
	document.observe("dom:loaded", function() {
		WebrsaPermission.init();
		$$( 'table.permissions select' ).each( function( select ) {
			$(select).observe( 'change', function(event) {
				$('loading-wait').show();
				setTimeout(function(){
					var elmt = event.findElement(),
						tr = $(elmt).up('tr');
					WebrsaPermission.setValue(tr, 'real', WebrsaPermission.compute(tr));
					propagate(tr);
					$('loading-wait').hide();
				}, 100);
			} );
		} );

		$('btn-all-oui').observe('click', function() {
			$('loading-wait').show();
			setTimeout(function(){
				setAll( '<?php echo WebrsaPermissions::ACCES_OUI;?>' );
				$('loading-wait').hide();
			}, 100);
			return false;
		});

		$('btn-all-non').observe('click', function() {
			$('loading-wait').show();
			setTimeout(function(){
				setAll( '<?php echo WebrsaPermissions::ACCES_NON;?>' );
				$('loading-wait').hide();
			}, 100);
			return false;
		});

		$('btn-all-herite').observe('click', function() {
			$('loading-wait').show();
			setTimeout(function(){
				setAll( '<?php echo WebrsaPermissions::ACCES_HERITE;?>' );
				$('loading-wait').hide();
			}, 100);
			return false;
		});

		$('btn-all-actual').observe('click', function() {
			$('loading-wait').show();
			setTimeout(function(){
				WebrsaPermission.reset();
				$('loading-wait').hide();
			}, 100);
			return false;
		});

		$('btn-all-parent').observe('click', function() {
			$('loading-wait').show();
			setTimeout(function(){
				WebrsaPermission.parent();
				$('loading-wait').hide();
			}, 100);
			return false;
		});

		// Suivant que l'on est dans /groups ou /users
		<?php
			if('groups' === $this->request->params['controller']) {
				$parentId = $this->Html->domId('Group.parent_id');
			} else {
				$parentId = $this->Html->domId('User.group_id');
			}
		?>
		$('<?php echo $parentId;?>').observe( 'change', function(event) {
			var elmt = event.findElement(),
				url = '<?php echo Router::url(array('controller' => $this->request->params['controller'], 'action' => 'ajax_get_permissions_light') );?>/'+$F(elmt);
				console.log(url);
			$('loading-wait').show();
			console.log('apres show');
			setTimeout(function(){
				console.log('dans setTimeout');
				new Ajax.Request(
					url,
					{
						asynchronous:true,
						evalScripts:true,
						requestHeaders: {Accept: 'application/json'},
						onSuccess:function(request, json) {
							var json = request.responseText;
							var permissions = JSON.parse(json),
								key,
								select,
								tr,
								parentId = $F(elmt);

							for(key in permissions) {
								if(false === permissions.hasOwnProperty(key)) {
									continue;
								}

								try {
									select = $(WebrsaPermission.domId('controllers/' + key));
									if(null !== select) {
										tr = select.up('tr');

										// @info: si on enlève le parent, /groups/ajax_get_permissions/0 renvoit tout en -10
										( parseInt(parentId, 10) > 0 )
											? WebrsaPermission.setValue(tr, 'parent', permissions[key] > 0 ? 'yes' : 'no')
											: WebrsaPermission.setValue(tr, 'parent', 'inherit')
									}
								} catch(e) {
									console.error(e);
								}
							}

							WebrsaPermission.refresh();
							$('loading-wait').hide();
						}
					}
				);
			}, 100);
		} );
	});
	//]]>
</script>