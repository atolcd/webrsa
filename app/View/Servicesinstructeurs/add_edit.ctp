<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	};

	echo $this->Default3->titleForLayout( $this->request->data );

	echo $this->Default3->DefaultForm->create();
?>
<fieldset>
<?php
	echo $this->Default3->subform(
		$this->Translator->normalize(
			array(
				'Serviceinstructeur.id',
				'Serviceinstructeur.lib_service',
				'Serviceinstructeur.num_rue',
				'Serviceinstructeur.type_voie' => array( 'empty' => true ),
				'Serviceinstructeur.nom_rue',
				'Serviceinstructeur.complement_adr',
				'Serviceinstructeur.code_insee',
				'Serviceinstructeur.code_postal',
				'Serviceinstructeur.ville',
				'Serviceinstructeur.email'
			)
		),
		array(
			'options' => $options
		)
	);
?>
</fieldset>
<fieldset>
<?php
	echo $this->Default3->subform(
		$this->Translator->normalize(
			array(
				'Serviceinstructeur.numdepins',
				'Serviceinstructeur.typeserins' => array( 'empty' => true ),
				'Serviceinstructeur.numcomins',
				'Serviceinstructeur.numagrins'
			)
		),
		array(
			'options' => $options
		)
	);
?>
</fieldset>
<?php
	if( Configure::read( 'Recherche.qdFilters.Serviceinstructeur' ) ) {
		echo $this->Default3->subform(
			$this->Translator->normalize(
				array(
					'Serviceinstructeur.sqrecherche' => array( 'rows' => 40 )
				)
			)
		);
	}

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	if( isset( $sqlError ) && !empty( $sqlError ) && false === $sqlError['success'] ) {
		echo '<h2>Erreur SQL dans les moteurs de recherche</h2>';
		echo "<div class=\"query\">";
		echo "<div class=\"errormessage\">".nl2br( $sqlError['message'] )."</div>";
		echo "<div class=\"sql\">".nl2br( str_replace( "\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $sqlError['value'] ) )."</div>";
		echo "</div>";
	}

	echo $this->Observer->disableFormOnSubmit();
?>