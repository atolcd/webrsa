<?php
	$dsp_id = Hash::get($dsp, 'Dsp.id');

	$paramsElement = array(
		'addLink' => false,
		'titleData' => $personne,
		'messages' => empty($dsp_id) 
			? array('Cette personne ne possède pas encore de données socio-professionnelles.' => 'notice')
			: array()
	);
	echo $this->element('default_index', $paramsElement);
	
	echo $this->Default3->actions(
		WebrsaAccess::actions(
			array(
				'/Dsps/add/'.$personne_id => array(
					'add' => $this->action === 'view_revs' ? null : $ajoutPossible,
					'hidden' => !empty($dsp_id)
				),
				'/Dsps/edit/'.$personne_id => array(
					'hidden' => empty($dsp_id) || !isset($rev) || $rev
				),
				'/Dsps/revertTo/'.$dsp_id => array(
					'hidden' => $this->action !== 'view_revs',
					'class' => 'arrow_undo',
				),
			),
			$dsp
		)
	);
	
	if (!empty($dsp['Dsp']['id'])) {
		echo '<div id="dsps">';
		echo $this->Form->input(
			'Dsp.hideempty',
			array(
				'type' => 'checkbox',
				'label' => 'Cacher les questions sans réponse',
				'onclick' => 'if( $( \'DspHideempty\' ).checked ) {
					$$( \'.empty\' ).each( function( elmt ) { elmt.hide() } );
				} else { $$( \'.empty\' ).each( function( elmt ) { elmt.show() } ); }'
			)
		);

		echo $this->Xhtml->tag( 'h2', 'Généralités' );
		echo $this->Default->view(
			$dsp,
			array(
				'Dsp.sitpersdemrsa',
				'Dsp.topisogroouenf',
				'Dsp.topdrorsarmiant',
				'Dsp.drorsarmianta2',
				'Dsp.topcouvsoc'
			),
			array(
				'options' => $options
			)
		);

		echo $this->Xhtml->tag( 'h2', 'Situation sociale' );
		echo $this->Xhtml->tag( 'h3', 'Généralités' );
		echo $this->Default->view(
			$dsp,
			array(
				'Dsp.accosocfam',
				'Dsp.libcooraccosocfam',
				'Dsp.accosocindi',
				'Dsp.libcooraccosocindi',
				'Dsp.soutdemarsoc'
			),
			array(
				'options' => $options
			)
		);

		// SituationSociale - DetailDifficulteSituationSociale (0-n)
		echo $this->Dsphm->details( $dsp, 'Detaildifsoc', 'difsoc', 'libautrdifsoc', $options['Detaildifsoc']['difsoc'] );

		// SituationSociale - DetailDifficulteSituationSocialeProfessionnel (0-n)
		if ($cg=='cg58') {
			echo $this->Dsphm->details( $dsp, 'Detaildifsocpro', 'difsocpro', 'libautrdifsocpro', $options['Detaildifsocpro']['difsocpro'] );
			echo $this->Default->view(
				$dsp,
				array(
					'Dsp.suivimedical'
				),
				array(
					'options' => $options
				)
			);
		}

		// SituationSociale - DetailAccompagnementSocialFamilial (0-n)
		echo $this->Dsphm->details( $dsp, 'Detailaccosocfam', 'nataccosocfam', 'libautraccosocfam', $options['Detailaccosocfam']['nataccosocfam'] );

		// SituationSociale - DetailAccompagnementSocialIndividuel (0-n)
		echo $this->Dsphm->details( $dsp, 'Detailaccosocindi', 'nataccosocindi', 'libautraccosocindi', $options['Detailaccosocindi']['nataccosocindi'] );

		// SituationSociale - DetailDifficulteDisponibilite (0-n)
		echo $this->Dsphm->details( $dsp, 'Detaildifdisp', 'difdisp', null, $options['Detaildifdisp']['difdisp'] );

		// Niveau d'étude
		echo $this->Xhtml->tag( 'h2', 'Niveau d\'étude' );
		echo $this->Default->view(
			$dsp,
			array(
				'Dsp.nivetu',
				'Dsp.nivdipmaxobt',
				'Dsp.annobtnivdipmax',
				'Dsp.topqualipro',
				'Dsp.libautrqualipro',
				'Dsp.topcompeextrapro',
				'Dsp.libcompeextrapro'
			),
			array(
				'options' => $options
			)
		);

		// Disponibilités emploi
		echo $this->Xhtml->tag( 'h2', 'Disponibilités emploi' );
		echo $this->Default->view(
			$dsp,
			array(
				'Dsp.topengdemarechemploi'
			),
			array(
				'options' => $options
			)
		);

		// Situation professionnelle
		echo $this->Xhtml->tag( 'h2', 'Situation professionnelle' );
		$fields = array_merge(
			Hash::normalize(
				array(
					'Dsp.hispro',
				)
			),
			// Dernière activité
			$this->Romev3->fields( 'Deractromev3' ),
			// Libellés dernière activité
			Hash::normalize(
				array(
					'Dsp.libsecactderact',
					'Dsp.libderact',
				)
			),
			// Codes ROME V2
			(
				Configure::read( 'Cg.departement' ) == 66
				? Hash::normalize(
					array(
						'Libsecactderact66Secteur.intitule' => array( 'label' => __d( 'dsps', 'Libsecactderact66Secteur.name' ), 'type' => 'text' ),
						'Libderact66Metier.intitule' => array( 'label' => __d( 'dsps', 'Libderact66Metier.name' ), 'type' => 'text' ),
					)
				)
				: array()
			),
			Hash::normalize(
				array(
					'Dsp.cessderact',
					'Dsp.topdomideract',
				)
			),
			// Dernière activité dominante
			$this->Romev3->fields( 'Deractdomiromev3' ),
			Hash::normalize(
				array(
					'Dsp.libsecactdomi',
					'Dsp.libactdomi',
				)
			),
			// Codes ROME V2
			(
				Configure::read( 'Cg.departement' ) == 66
				? Hash::normalize(
					array(
						'Libsecactdomi66Secteur.intitule' => array( 'label' => __d( 'dsps', 'Libsecactdomi66Secteur.name' ), 'type' => 'text' ),
						'Libactdomi66Metier.intitule' => array( 'label' => __d( 'dsps', 'Libactdomi66Metier.name' ), 'type' => 'text' ),
					)
				)
				: array()
			),
			Hash::normalize(
				array(
					'Dsp.duractdomi',
					'Dsp.inscdememploi',
					'Dsp.topisogrorechemploi',
					'Dsp.accoemploi',
					'Dsp.libcooraccoemploi',
					'Dsp.topprojpro',
				)
			)
		);
		echo $this->Default->view( $dsp, $fields, array( 'options' => $options ) );

		// INFO: à ajouter pour le 58
		if ($cg=='cg58') {
			echo $this->Dsphm->details( $dsp, 'Detailprojpro', 'projpro', 'libautrprojpro', $options['Detailprojpro']['projpro'] );
		}

		$fields = array_merge(
			// Emploi recherché
			$this->Romev3->fields( 'Actrechromev3' ),
			Hash::normalize(
				array(
					'Dsp.libsecactrech',
					'Dsp.libemploirech',
				)
			),
			// Codes ROME V2
			(
				Configure::read( 'Cg.departement' ) == 66
				? Hash::normalize(
					array(
						'Libsecactrech66Secteur.intitule' => array( 'label' => __d( 'dsps', 'Libsecactrech66Secteur.name' ), 'type' => 'text' ),
						'Libemploirech66Metier.intitule' => array( 'label' => __d( 'dsps', 'Libemploirech66Metier.name' ), 'type' => 'text' ),
					)
				)
				: array()
			),
			Hash::normalize(
				array(
					'Dsp.topcreareprientre',
					'Dsp.concoformqualiemploi'
				)
			)
		);
		echo $this->Default->view( $dsp, $fields, array( 'options' => $options ) );

		if ($cg=='cg58') {
			echo $this->Default->view(
				$dsp,
				array(
					'Dsp.libformenv'
				),
				array(
					'options' => $options
				)
			);
			echo $this->Dsphm->details( $dsp, 'Detailfreinform', 'freinform', null, $options['Detailfreinform']['freinform'] );
		}

		// Mobilité
		echo $this->Xhtml->tag( 'h2', 'Mobilité' );
		echo $this->Default->view(
			$dsp,
			array(
				'Dsp.topmoyloco'
			),
			array(
				'options' => $options
			)
		);

		if ($cg=='cg58') {
			echo $this->Dsphm->details( $dsp, 'Detailmoytrans', 'moytrans', 'libautrmoytrans', $options['Detailmoytrans']['moytrans'] );

			echo $this->Default->view(
				$dsp,
				array(
					'Dsp.toppermicondub',
					'Dsp.topautrpermicondu',
					'Dsp.libautrpermicondu'
				),
				array(
					'options' => $options
				)
			);
		}

		// Mobilite - DetailMobilite (0-n)
		echo $this->Dsphm->details( $dsp, 'Detailnatmob', 'natmob', null, $options['Detailnatmob']['natmob'] );


		// Difficultés logement
		echo  $this->Xhtml->tag( 'h2', 'Difficultés logement' );
		echo $this->Default->view(
			$dsp,
			array(
				'Dsp.natlog',
				'Dsp.statutoccupation'
			),
			array(
				'options' => $options
			)
		);

		if ($cg=='cg58')
			echo $this->Dsphm->details( $dsp, 'Detailconfort', 'confort', null, $options['Detailconfort']['confort'] );

		echo $this->Default->view(
			$dsp,
			array(
				'Dsp.demarlog'
			),
			array(
				'options' => $options
			)
		);

		// DifficulteLogement - DetailDifficulteLogement

		echo $this->Dsphm->details( $dsp, 'Detaildiflog', 'diflog', 'libautrdiflog', $options['Detaildiflog']['diflog'] );
		echo '</div>';
	}
?>
<?php if( $this->action == 'view_revs' ):?>
	<div class="submit">
		<?php echo $this->Form->submit( 'Retour', array( 'name' => 'Cancel', 'div' => false ) ); ?>
	</div>
	<?php echo $this->Form->end();?>
<?php endif;?>