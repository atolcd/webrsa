<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
	echo $this->Default3->actions( array( '/Configurations/index' => array( 'class' => 'back' ) ) );
	echo $this->Default3->titleForLayout( $this->request->data );
	echo $this->Default3->DefaultForm->create();
?>
<fieldset>
    <table class="wide noborder">
        <tr>
            <td class="noborder" style="width: 50%">
			<?php
			$nb_lignes = substr_count($this->request->data['Configuration']['comments_variable'], "\n")+1;
			if($nb_lignes < 4)
				$nb_lignes = $nb_lignes*3;
				echo $this->Default3->subform(
					$this->Translator->normalize(
						array('Configuration.comments_variable' => array(
							'type' => 'textarea',
							'style' => 'height: '.strval(intval(1.3*$nb_lignes)).'em; resize: none;'),
							'legend' => 'hidden'
					)
			),
			array(
				'options' => $options,
			)
		);
			?>
            </td>
            <td class="noborder">
				<?php
				$value = json_encode(json_decode($this->request->data['Configuration']['value_variable'], true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
				$nb_lignes = substr_count($value, "\n")+1;
				if($nb_lignes < 4)
					$nb_lignes = $nb_lignes*3;
				echo $this->Default3->subform(
					$this->Translator->normalize(
						array('Configuration.value_variable_a_modifier' =>
							array(
								'type' => 'textarea',
								'style' => 'height: '.strval(intval(1.3*$nb_lignes)).'em; resize: none;',
								'value' => $value,
								'id' => 'editor',
							)
						)
					),
					array(
						'options' => $options
					)
				);
		?>
            </td>
		</tr>
	</table>
</fieldset>
<?php
	echo $this->Default3->subform(
		$this->Translator->normalize(
			array('Configuration.id' => array( 'type' => 'hidden' ))
		),
	array(
		'options' => $options
	)
	);

	echo $this->Default3->subform(
		$this->Translator->normalize(
			array('Configuration.value_variable' =>
				array(
					'type' => 'hidden',
					'id' => 'valueChanged',
				)
			)
		),
		array(
			'options' => $options
		)
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();
	echo $this->Default3->actions( array( '/Configurations/index' => array( 'class' => 'back' ) ) );
	echo $this->Observer->disableFormOnSubmit();

	// Partie historique
	if( isset($histo) && !empty($histo) ) {
		echo '<br><br> <h1>' . __m('Configuration.historique') .  '</h1>';
		echo $this->Default3->index(
			$histo,
			$this->Translator->normalize(
				array(
					'Configuration.created' => array('type' => 'date', 'dateFormat' => 'DMY'),
					'Configurationhistorique.created' => array('type' => 'date', 'dateFormat' => 'DMY'),
					'Configurationhistorique.username',
				)
				),
				array('paginate' => false)
		);
	}
?>
<script src="<?php echo ((isset($_SERVER['HTTPS']) ? 'https://' : 'http://').$_SERVER['HTTP_HOST']); ?>/js/ace/ace.js" type="text/javascript" charset="utf-8"></script>
<script>
	var nbLignes = <?php echo $nb_lignes ?>;
    var editor = ace.edit("editor", {
		theme: "ace/theme/tomorrow",
		mode: "ace/mode/json",
		maxLines: 100,
		minLines: 2
	});
	editor.renderer.setScrollMargin(10, 10, 10, 10);
	var JSONcontent = <?php echo json_encode(json_decode($this->request->data['Configuration']['value_variable'], true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?>;
	JSONcontent = JSON.stringify(JSONcontent, null, '\t');

	regex = /\\"/gm;
	JSONcontent = JSONcontent.replace(regex, '"');
	regex = /\"\[/gm;
	JSONcontent = JSONcontent.replace(regex, '[');
	regex = /\]\"/gm;
	JSONcontent = JSONcontent.replace(regex, ']');

	editor.setValue(JSONcontent);

	editor.session.on('change', function(delta) {
		document.getElementById('valueChanged').setAttribute('value', editor.getValue());
	});

</script>