<?php
	$this->pageTitle = 'Paramètres financiers pour la gestion de l\'APRE';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );

	echo $this->Xform->create( 'Parametrefinancier' );
	if( isset( $this->request->data['Parametrefinancier']['id'] ) ) {
		echo '<div>'.$this->Xform->input( 'Parametrefinancier.id', array( 'type' => 'hidden' ) ).'</div>';
	}
	echo $this->Xform->input( 'Parametrefinancier.entitefi', array(  'required' => true, 'domain' => 'apre' ) );
	echo $this->Xform->input( 'Parametrefinancier.engagement', array(  'domain' => 'apre' ) );
	echo $this->Xform->input( 'Parametrefinancier.tiers', array(  'required' => true, 'domain' => 'apre' ) );
	echo $this->Xform->input( 'Parametrefinancier.codecdr', array(  'required' => true, 'domain' => 'apre' ) );
	echo $this->Xform->input( 'Parametrefinancier.libellecdr', array(  'required' => true, 'domain' => 'apre' ) );
	echo $this->Xform->input( 'Parametrefinancier.natureanalytique', array(  'required' => true, 'domain' => 'apre' ) );
	echo $this->Xform->input( 'Parametrefinancier.lib_natureanalytique', array(  'required' => true, 'domain' => 'apre' ) );
	echo $this->Xform->input( 'Parametrefinancier.programme', array(  'required' => true, 'domain' => 'apre' ) );
	echo $this->Xform->input( 'Parametrefinancier.lib_programme', array(  'required' => true, 'domain' => 'apre' ) );
	echo $this->Xform->input( 'Parametrefinancier.apreforfait', array(  'required' => true, 'domain' => 'apre' ) );
	echo $this->Xform->input( 'Parametrefinancier.aprecomplem', array(  'domain' => 'apre' ) );
	echo $this->Xform->input( 'Parametrefinancier.natureimput', array(  'required' => true, 'domain' => 'apre' ) );

	echo '<div class="submit">';
		echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
		echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
	echo '</div>';

	echo $this->Xform->end();
?>