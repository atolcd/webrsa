<?php

    echo $this->Default3->titleForLayout();

    if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
    }

    echo '<br>';

    echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );
    echo $this->Default3->subform(
		array(
            'Titrecreancier.id'=> array('type' => 'hidden', 'value' => $titrecreancier['Titrecreancier']['id']),
			'Titrecreancier.mention'=> array('type' => 'textarea', 'value' => $titrecreancier['Titrecreancier']['mention'])
		)
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "creance_{$this->request->params['action']}_form" ) );