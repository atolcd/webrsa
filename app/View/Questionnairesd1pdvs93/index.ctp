<?php
	echo $this->Default3->titleForLayout( $personne );

	echo $this->element( 'ancien_dossier' );

	App::uses( 'WebrsaAccess', 'Utility' );
	WebrsaAccess::init( $dossierMenu );

	echo $this->Default3->actions(
		array(
			"/Questionnairesd1pdvs93/add/{$personne_id}" => array(
				'disabled' => false === WebrsaAccess::addIsEnabled( "/Questionnairesd1pdvs93/add/{$personne_id}", $ajoutPossible )
			),
		)
	);
?>

<?php if( !empty( $historiquesdroit ) ):?>
   <caption>Historique du droit</caption>
   <table class="aere">
       <thead>
       <tr>
           <th>Etat(s) du dossier RSA</th>
           <th>Soumis à droit et devoir</th>
           <th>Modifié le</th>
       </tr>
       </thead>
       <tbody>
        <?php
            $listeEtat = null;
            $listeSoumis = null;
            $dateChangement = null;

            foreach( $historiquesdroit as $key => $histo ) {
                if( !empty( $histo ) ) {
                    $listeEtat = value( $options['Situationallocataire']['etatdosrsa'], $histo['Historiquedroit']['etatdosrsa'] );
                    @$listeSoumis = value( $options['Situationallocataire']['toppersdrodevorsa'], $histo['Historiquedroit']['toppersdrodevorsa'] );
                    $dateChangement = $histo['Historiquedroit']['created'];

                    echo $this->Xhtml->tableCells(
						array(
                            h( $listeEtat ),
                            h( @$listeSoumis ),
                            h( $this->Locale->date( 'Datetime::full', $dateChangement ) )
                        )
                    );
                }
            }
        ?>
        </tbody>
    </table>
    <?php else :?>
        <p class="notice">Aucun historique trouvé pour cet allocataire</p>
    <?php endif;?>

<?php
	// A-t'on des messages à afficher à l'utilisateur ?
	echo $this->Default3->messages( $messages );

	echo $this->Default3->index(
		$questionnairesd1pdvs93,
		array(
			'Structurereferente.lib_struc',
			'Rendezvous.daterdv',
			'Statutrdv.libelle',
			'Questionnaired1pdv93.date_validation',
            'Historiquedroit.etatdosrsa' => array(
				'label' => __d( 'historiquedroit', 'Historiquedroit.etatdosrsa' )
			),
            'Historiquedroit.toppersdrodevorsa' => array(
				'label' => __d( 'historiquedroit', 'Historiquedroit.toppersdrodevorsa' ),
				'type' => 'boolean'
			),
            'Historiquedroit.modified' => array(
				'label' => __d( 'historiquedroit', 'Historiquedroit.modified' ),
			)
		)
		+ WebrsaAccess::links(
			array(
				'/Questionnairesd1pdvs93/view/#Questionnaired1pdv93.id#',
				'/Questionnairesd1pdvs93/delete/#Questionnaired1pdv93.id#' => array(
					'confirm' => true
				)
			)
		),
		array(
			'options' => $options,
			'paginate' => false
		)
	);
?>