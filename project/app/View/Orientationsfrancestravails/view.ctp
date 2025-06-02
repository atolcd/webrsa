<?php

    $paramsElement = [
        'addLink' => false,
        'titleData' => $personne,
        'messages' => empty($orientation_france_travail)
            ? [ __m('Orientationfrancetravail.notice.noinfo') => 'notice' ]
            : []
    ];
    echo $this->element('default_index', $paramsElement);

    if ( isset($orientation_france_travail) && !empty($orientation_france_travail) ) {
?>
    <br>
    <br>
    <h3><?=  __m('Orientationfrancetravail.DerniereMaj') . date('d/m/Y H:m' ,strtotime($orientation_france_travail['Orientationfrancetravail']['modified'])) ?></h3>
    <br>
    <h2><?= __m('Orientationfrancetravail.TitreDerniereOrientation') ?></h2>
    <br>
    <div>
        <p><?= __m('Orientationfrancetravail.Parcours') . ( isset($orientation_france_travail['Orientationfrancetravail']['code_parcours']) ? __m('Orientationfrancetravail.Parcours.' . $orientation_france_travail['Orientationfrancetravail']['code_parcours']) : "") ?></p>
        <p><?= __m('Orientationfrancetravail.Operateurs') . ( isset($orientation_france_travail['Orientationfrancetravail']['organisme']) ? __m('Orientationfrancetravail.Operateurs.' . $orientation_france_travail['Orientationfrancetravail']['organisme']) : "") ?></p>
        <p><?= __m('Orientationfrancetravail.Statut') . ( isset($orientation_france_travail['Orientationfrancetravail']['statut']) ? __m('Orientationfrancetravail.Statut.' . $orientation_france_travail['Orientationfrancetravail']['statut']) : "") ?></p>
        <p><?= __m('Orientationfrancetravail.Etat') . ( isset($orientation_france_travail['Orientationfrancetravail']['etat']) ? __m('Orientationfrancetravail.Etat.' . $orientation_france_travail['Orientationfrancetravail']['etat']) : "") ?></p>
        <p><?= __m('Orientationfrancetravail.Dateentree') . ( isset($orientation_france_travail['Orientationfrancetravail']['date_entree_parcours']) ? date('d/m/Y' ,strtotime($orientation_france_travail['Orientationfrancetravail']['date_entree_parcours'])) : "") ?></p>
        <p><?= __m('Orientationfrancetravail.Datemodification') . ( isset($orientation_france_travail['Orientationfrancetravail']['date_modification']) ? date('d/m/Y H:m' ,strtotime($orientation_france_travail['Orientationfrancetravail']['date_modification'])) : "") ?></p>
        <p><?= __m('Orientationfrancetravail.Stucture') . $orientation_france_travail['Orientationfrancetravail']['struct_libelle'] ?></p>
        <p><?= __m('Orientationfrancetravail.Structuredecision') . $orientation_france_travail['Orientationfrancetravail']['struct_decision_libelle'] ?></p>
    </div>

    <h2><?= __m('Orientationfrancetravail.TitreCritere') ?></h2>
    <br>
    <div id="francetravail">
	<?=	$this->Form->input(
			'Orientationfrancetravail.hideempty',
			[
				'type' => 'checkbox',
				'label' => 'Masquer les critères sans information',
				'onclick' => 'if( $( \'OrientationfrancetravailHideempty\' ).checked ) {
					$$( \'.empty\' ).each( function( elmt ) { elmt.hide() } );
				} else { $$( \'.empty\' ).each( function( elmt ) { elmt.show() } ); }'
            ]
		);
    ?>

    <?= $this->Default->view(
			$orientation_france_travail,
			[
				'Orientationfrancetravail.crit_origine_calcul',
				'Orientationfrancetravail.crit_situation_professionnelle',
                'Orientationfrancetravail.crit_type_emploi',
                'Orientationfrancetravail.crit_niveau_etude',
                'Orientationfrancetravail.crit_capacite_a_travailler',
                'Orientationfrancetravail.crit_projet_pro',
                'Orientationfrancetravail.crit_contrainte_sante',
                'Orientationfrancetravail.crit_contrainte_logement',
                'Orientationfrancetravail.crit_contrainte_mobilite',
                'Orientationfrancetravail.crit_contrainte_familiale',
                'Orientationfrancetravail.crit_contrainte_financiere',
                'Orientationfrancetravail.crit_contrainte_numerique',
                'Orientationfrancetravail.crit_contrainte_admin_jur',
                'Orientationfrancetravail.crit_contrainte_francais_calcul',
                'Orientationfrancetravail.crit_boe',
                'Orientationfrancetravail.crit_baeeh',
                'Orientationfrancetravail.crit_scolarite_etab_spec',
                'Orientationfrancetravail.crit_esat',
                'Orientationfrancetravail.crit_boe_souhait_accompagnement',
                'Orientationfrancetravail.crit_msa_autonomie_recherche_emploi',
                'Orientationfrancetravail.crit_msa_demarches_professionnelles'
            ],
            [
                'options' => $options
            ]
		);
    ?>

    <h2><?= __m('Orientationfrancetravail.TitreSortieParcours') ?></h2>

    <?= $this->Default->view(
			$orientation_france_travail,
			[
				'Orientationfrancetravail.decision_date_sortie_parcours',
				'Orientationfrancetravail.decision_motif_sortie_parcours',
                'Orientationfrancetravail.decision_etat',
                'Orientationfrancetravail.decision_date',
                'Orientationfrancetravail.decision_organisme',
                'Orientationfrancetravail.decision_motif_refus',
                'Orientationfrancetravail.decision_commentaire_refus',
                'Orientationfrancetravail.decision_structure_libelle',
            ],
            [
                'options' => $options
            ]
		);
    ?>
    </div>

<?php
    }
