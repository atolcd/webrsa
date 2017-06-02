<?php
	$this->pageTitle = 'Référents';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Referent', array( 'type' => 'post' ) );
		echo '<div>';
		echo $this->Form->input( 'Referent.id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
	else {
		echo $this->Form->create( 'Referent', array( 'type' => 'post' ) );
		echo '<div>';
		echo $this->Form->input( 'Referent.id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
?>
<?php if ((integer)Configure::read('Cg.departement') === 66) {?>
	<fieldset><legend>Référent lié</legend>
		<?php
			// Ajout de l'option manquante si besoin
			$prev_id = Hash::get($this->request->data, 'Dernierreferent.prevreferent_id');
			if ($prev_id && !isset($options['Dernierreferent']['prevreferent_id'][$prev_id])) {
				$options['Dernierreferent']['prevreferent_id'][$prev_id] = Hash::get($options, "Referent.id.$prev_id");
			}

			echo $this->Xform->input('Rechercher', array('id' => 'search_referent'));
			echo $this->Default2->subform(
				array(
					'Dernierreferent.id' => array('type' => 'hidden'),
					'Dernierreferent.prevreferent_id' => array(
						'type' => 'select',
						'options' => $options['Dernierreferent']['prevreferent_id'],
						'empty' => true,
						'id' => 'list_referent',
					)
				)
			);
			echo $this->Xform->input('Charger', array('type' => 'button', 'id' => 'load_referent'));
		?>
	</fieldset>
<?php } ?>
	<fieldset>
		<?php
			echo $this->Default2->subform(
				array(
					'Referent.qual' => array( 'options' => $options['Referent']['qual'] ),
					'Referent.nom',
					'Referent.prenom',
					'Referent.fonction',
					'Referent.numero_poste' => array( 'maxlength' => 10 ),
					'Referent.email',
					'Referent.actif' => array( 'label' => 'Actif ?', 'type' => 'radio', 'options' => $options['Referent']['actif'] )
				)
			);
		?>
	</fieldset>
	<fieldset class="col2">
		<legend>Structures référentes</legend>
		<?php echo $this->Form->input( 'Referent.structurereferente_id', array( 'label' => required( false ), 'type' => 'select' , 'options' => $options['Referent']['structurereferente_id'], 'empty' => true ) );?>
	</fieldset>

	<div class="submit">
		<?php
			echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
			echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
		?>
	</div>
<?php echo $this->Form->end();?>

<script>
	var index = [];

	function format_approchant(text) {
		return text.toLowerCase().replace(/[àâä]/g, 'a').replace(/[éèêë]/g, 'e')
				.replace(/[ïî]/g, 'i').replace(/[ôö]/g, 'o').replace(/[ùüû]/g, 'u').replace('-', ' ');
	}

	$$('#list_referent option').each(function(option){
		index.push({
			value: option.getAttribute('value'),
			textlo: format_approchant(option.innerHTML),
			text: option.innerHTML
		});
	});

	$('search_referent').observe('keypress', function(event){
		'use strict';
		var value = $('search_referent').getValue(),
			regex = /^[a-zA-Z éèï\-ç]$/,
			i,
			newValue = ''
		;

		// Ajoute à la valeur du champ, la "lettre" utilisé
		if (regex.test(event.key)) {
			value += event.key;
		} else if (event.key === 'Backspace') {
			value = value.substr(0, value.length -1);
		}

		// Recherche la valeur à selectionner
		for (i=0; i<index.length; i++) {
			if (index[i].text.indexOf(value) >= 0) {
				newValue = index[i].value;
				break;
			} else if (index[i].textlo.toLowerCase().indexOf(format_approchant(value)) >= 0) {
				newValue = index[i].value;
			}
		}

		// Set de la valeur
		$('list_referent').setValue(newValue);
	});

	$('load_referent').observe('click', function(event){
		event.preventDefault();

		new Ajax.Request('<?php echo Router::url(array('controller' => 'referents', 'action' => 'ajax_getreferent')); ?>/', {
			asynchronous:true,
			evalScripts:true,
			parameters: {
				id: $('list_referent').getValue()
			},
			requestHeaders: {Accept: 'application/json'},
			onComplete:function(request, json) {
				for (var key in json) {
					var model_field = key.split('__');
					var inputs = $$('[name="data['+model_field[0]+']['+model_field[1]+']"]');

					if (model_field[0] !== 'Dernierreferent' && key !== 'Referent__id' && inputs.length > 0) {
						inputs[0].setValue(json[key]);
					}
				}
			},
			onFail:function() { console.error('error : load_referent fail'); },
			onException:function() { console.error('error : load_referent exception'); }
		});
	});
</script>