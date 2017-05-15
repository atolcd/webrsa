<?php
	$title_for_layout = 'Relances de la personne';
	$this->set( 'title_for_layout', $title_for_layout );

	App::uses( 'WebrsaAccess', 'Utility' );
	WebrsaAccess::init( $dossierMenu );

	echo $this->Html->tag( 'h1', $title_for_layout );

	echo $this->Default3->actions(
		array(
			"/Relancesnonrespectssanctionseps93/add/{$personne_id}" => array(
				'disabled' => false === $this->Permissions->check( 'orientsstructs', 'add' )
					|| false === WebrsaAccess::addIsEnabled( "/Relancesnonrespectssanctionseps93/add/{$personne_id}", $ajoutPossible )
			)
		)
	);
?>
<?php if( !empty( $erreurs ) ):?>
	<div class="error_message">
		<?php if( count( $erreurs ) > 1 ):?>
		<ul>
			<?php foreach( $erreurs as $erreur ):?>
				<li><?php echo __d( 'relancenonrespectsanctionep93', "Erreur.{$erreur}" );?></li>
			<?php endforeach;?>
		</ul>
		<?php else:?>
			<p><?php echo __d( 'relancenonrespectsanctionep93', "Erreur.{$erreurs[0]}" );?></p>
		<?php endif;?>
	</div>
<?php endif;?>
<?php
	echo $this->Default3->index(
		$relances,
		array(
			'Dossier.matricule' => array(
				'label' => __m( 'Dossier.matricule' )
			),
			'Personne.nom' => array(
				'label' => __m( 'Personne.nom' )
			),
			'Personne.prenom' => array(
				'label' => __m( 'Personne.prenom' )
			),
			'Adresse.localite' => array(
				'label' => __m( 'Adresse.localite' )
			),
			'Nonrespectsanctionep93.origine_label' => array(
				'label' => __m( 'Nonrespectsanctionep93.origine' )
			),
			'Nonrespectsanctionep93.date_pivot' => array(
				'label' => __m( 'Nonrespectsanctionep93.date_pivot' ),
				'class' => 'date',
				'type' => 'date'
			),
			'Nonrespectsanctionep93.nb_jours' => array(
				'label' => __m( 'Nonrespectsanctionep93.nb_jours' ),
				'class' => 'number',
			),
			'Relancenonrespectsanctionep93.daterelance' => array(
				'label' => __m( 'Relancenonrespectsanctionep93.daterelance' )
			),
			'Relancenonrespectsanctionep93.numrelance_1' => array(
				'label' => __m( 'Relancenonrespectsanctionep93.numrelance' ),
				'value' => '#Relancenonrespectsanctionep93.numrelance#ère relance',
				'condition' => '"#Relancenonrespectsanctionep93.numrelance#" <= 1',
				'condition_group' => 'Relancenonrespectsanctionep93.numrelance'
			),
			'Relancenonrespectsanctionep93.numrelance_n' => array(
				'label' => __m( 'Relancenonrespectsanctionep93.numrelance' ),
				'value' => '#Relancenonrespectsanctionep93.numrelance#ème relance',
				'condition' => '"#Relancenonrespectsanctionep93.numrelance#" > 1',
				'condition_group' => 'Relancenonrespectsanctionep93.numrelance'
			),
		)
		+ WebrsaAccess::links(
			array(
				'/Relancesnonrespectssanctionseps93/view/#Relancenonrespectsanctionep93.id#' => array(
					'class' => 'button'
				),
				'/Relancesnonrespectssanctionseps93/impression/#Relancenonrespectsanctionep93.id#' => array(
					'class' => 'button'
				),
			)
		),
		array(
			'paginate' => false
		)
	);
?>