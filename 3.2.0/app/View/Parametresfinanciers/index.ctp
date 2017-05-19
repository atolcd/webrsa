<?php
	echo $this->Default3->titleForLayout();

	echo $this->Default3->actions(
		array(
			'/Parametrages/index/#apres' => array( 'class' => 'back' ),
			true === empty( $results )
				? '/Parametresfinanciers/add'
				:  '/Parametresfinanciers/edit'
		)
	);

	if( true === empty( $results ) ) {
		echo $this->Html->tag( 'p', 'Aucun paramétrage', array( 'class' => 'notice' ) );
	}
	else {
		echo $this->Default3->view(
			$results[0],
			array(
				'Parametrefinancier.entitefi',
				'Parametrefinancier.engagement',
				'Parametrefinancier.tiers',
				'Parametrefinancier.codecdr',
				'Parametrefinancier.libellecdr',
				'Parametrefinancier.natureanalytique',
				'Parametrefinancier.programme',
				'Parametrefinancier.lib_programme',
				'Parametrefinancier.apreforfait',
				'Parametrefinancier.aprecomplem',
				'Parametrefinancier.natureimput',
				'Parametrefinancier.lib_natureanalytique'
			),
			array(
				'domain' => 'parametrefinancier',
				'th' => true
			)
		);
	}

	echo $this->Default3->actions( array( '/Parametrages/index/#apres' => array( 'class' => 'back' ) ) );
?>