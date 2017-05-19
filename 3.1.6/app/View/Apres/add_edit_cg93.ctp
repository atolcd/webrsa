<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	$this->modelClass = Inflector::classify( $this->request->params['controller'] );

	$this->pageTitle = 'APRE';

	if( $this->action == 'add' ) {
		$this->pageTitle = 'Ajout APRE';
	}
	else {
		$this->pageTitle = 'Édition APRE';
	}

	function radioApre( $view, $path, $value, $label ) {
		$name = 'data['.implode( '][', explode( '.', $path ) ).']';
		$notEmptyValues = Hash::filter( (array)Set::classicExtract( $view->request->data, $value ) );
		$checked = ( ( !empty( $notEmptyValues ) ) ? 'checked="checked"' : '' );
		return "<label><input type=\"radio\" name=\"{$name}\" value=\"{$value}\" {$checked} />{$label}</label>";
	}

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		dependantSelect(
			'<?php echo $this->modelClass;?>ReferentId',
			'<?php echo $this->modelClass;?>StructurereferenteId'
		);
	});
</script>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		//Données pour la nature du logement
		['P', 'L', 'H', 'S'].each( function( letter ) {
			observeDisableFieldsOnValue( 'ApreNaturelogement' + letter, [ 'AprePrecisionsautrelogement' ],  letter, true );
		} );
		observeDisableFieldsOnValue( 'ApreNaturelogementA', [ 'AprePrecisionsautrelogement' ], 'A', false );

		//Données pour le type d'activité du bénéficiare
		['F', 'C', 'P'].each( function( letter ) {
			observeDisableFieldsOnValue(
				'<?php echo $this->modelClass;?>Activitebeneficiaire' + letter,
				[
					'<?php echo $this->modelClass;?>DateentreeemploiDay',
					'<?php echo $this->modelClass;?>DateentreeemploiMonth',
					'<?php echo $this->modelClass;?>DateentreeemploiYear',
					'<?php echo $this->modelClass;?>TypecontratCDI',
					'<?php echo $this->modelClass;?>TypecontratCDD',
					'<?php echo $this->modelClass;?>TypecontratCON',
					'<?php echo $this->modelClass;?>TypecontratAUT',
					'<?php echo $this->modelClass;?>Precisionsautrecontrat',
					'<?php echo $this->modelClass;?>Nbheurestravaillees',
					'<?php echo $this->modelClass;?>Nomemployeur',
					'<?php echo $this->modelClass;?>Adresseemployeur',
					'<?php echo $this->modelClass;?>Secteuractivite'
				],
				letter,
				true
			);
		} );
		observeDisableFieldsOnValue(
			'<?php echo $this->modelClass;?>ActivitebeneficiaireE',
			[
				'<?php echo $this->modelClass;?>DateentreeemploiDay',
				'<?php echo $this->modelClass;?>DateentreeemploiMonth',
				'<?php echo $this->modelClass;?>DateentreeemploiYear',
				'<?php echo $this->modelClass;?>TypecontratCDI',
				'<?php echo $this->modelClass;?>TypecontratCDD',
				'<?php echo $this->modelClass;?>TypecontratCON',
				'<?php echo $this->modelClass;?>TypecontratAUT',
				'<?php echo $this->modelClass;?>Precisionsautrecontrat',
				'<?php echo $this->modelClass;?>Nbheurestravaillees',
				'<?php echo $this->modelClass;?>Nomemployeur',
				'<?php echo $this->modelClass;?>Adresseemployeur',
				'<?php echo $this->modelClass;?>Secteuractivite'
			],
			'E',
			false
		);
		<?php
			echo $this->Ajax->remoteFunction(
				array(
					'update' => 'StructurereferenteRef',
					'url' => array(
						'action' => 'ajaxstruct',
						Set::extract( $this->request->data, "{$this->modelClass}.structurereferente_id" )
					)
				)
			).';';
			echo $this->Ajax->remoteFunction(
				array(
					'update' => 'ReferentRef',
					'url' => array(
						'action' => 'ajaxref',
						Set::extract( $this->request->data, "{$this->modelClass}.referent_id" )
					)
				)
			).';';
		?>

	});
</script>

<h1>Formulaire de demande de l'APRE COMPLÉMENTAIRE</h1>
<br />
<?php
	echo $this->Form->create( 'Apre', array( 'type' => 'post', 'id' => 'Apre' ) );
	$ApreId = Set::classicExtract( $this->request->data, "{$this->modelClass}.id" );
	if( $this->action == 'edit' ) {
		echo '<div>';
		echo $this->Form->input( "{$this->modelClass}.id", array( 'type' => 'hidden' ) );
		echo '</div>';
	}
	echo '<div>';
	echo $this->Form->input( "{$this->modelClass}.personne_id", array( 'type' => 'hidden', 'value' => $personne_id ) );
	echo '</div>';
?>

<div class="aere">
	<fieldset>
		<table class="wide noborder">
			<tr>
				<td class="mediumSize noborder">
					<?php echo $this->Form->input( "{$this->modelClass}.numeroapre", array( 'type' => 'hidden', 'value' => $numapre ) ); ?>
					<strong>Numéro de l'APRE : </strong><?php echo $numapre; ?>
				</td>
				<td class="mediumSize noborder">
					<?php echo $this->Xform->enum( "{$this->modelClass}.typedemandeapre", array(  'legend' => required( __d( 'apre', 'Apre.typedemandeapre' ) ), 'type' => 'radio', 'separator' => '<br />', 'options' => $options['typedemandeapre'] ) );?>
				</td>
			</tr>
		</table>
	</fieldset>
		<fieldset>
		<table class="wide noborder">
				<tr>
					<td class="mediumSize noborder">
					</td>
				</tr>
				<tr>
					<td colspan="2" class="wide noborder">
						<?php echo $this->Xform->input( "{$this->modelClass}.datedemandeapre", array( 'domain' => 'apre', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear'=>date('Y')+5, 'minYear'=>date('Y')-1 ) );?>
					</td>
				</tr>
			</table>
		</fieldset>
	<fieldset>
		<legend>Identité du bénéficiaire de la demande</legend>
		<table class="wide noborder">
			<tr>
				<td class="mediumSize noborder">
					<strong>Statut de la personne : </strong><?php echo Set::extract( $rolepers, Set::extract( $personne, 'Prestation.rolepers' ) ); ?>
					<br />
					<strong>Nom : </strong><?php echo Set::enum( Set::classicExtract( $personne, 'Personne.qual') , $qual ).' '.Set::classicExtract( $personne, 'Personne.nom' );?>
					<br />
					<strong>Prénom : </strong><?php echo Set::classicExtract( $personne, 'Personne.prenom' );?>
					<br />
					<strong>Date de naissance : </strong><?php echo date_short( Set::classicExtract( $personne, 'Personne.dtnai' ) );?>
					<br />
					<strong>Situation familiale : </strong><?php echo Set::enum( Set::classicExtract( $personne, 'Foyer.sitfam' ), $sitfam );?>
				</td>
				<td class="mediumSize noborder">
					<strong>N° Service instructeur : </strong><?php echo Set::classicExtract( $personne, 'Serviceinstructeur.lib_service' );?>
					<br />
					<strong>N° demandeur : </strong><?php echo Set::classicExtract( $personne, 'Dossier.numdemrsa' );?>
					<br />
					<strong>N° CAF/MSA : </strong><?php echo Set::classicExtract( $personne, 'Dossier.matricule' );?>
					<br />
					<strong>Inscrit au Pôle emploi</strong>
					<?php
						$isPoleemploi = Set::classicExtract( $personne, 'Activite.act' );
						if( $isPoleemploi == 'ANP' )
							echo 'Oui';
						else
							echo 'Non';
					?>
					<br />
					<strong>N° identifiant : </strong><?php echo Set::classicExtract( $personne, 'Personne.idassedic' );?>
					<br />
					<strong>Nbre d'enfants : </strong><?php echo $nbEnfants;?>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="mediumSize noborder">
					<strong>Adresse : </strong><br /><?php echo Set::classicExtract( $personne, 'Adresse.numvoie' ).' '.Set::classicExtract( $personne, 'Adresse.libtypevoie' ).' '.Set::classicExtract( $personne, 'Adresse.nomvoie' ).'<br /> '.Set::classicExtract( $personne, 'Adresse.codepos' ).' '.Set::classicExtract( $personne, 'Adresse.nomcom' );?>
				</td>
			</tr>
			<tr>
				<td class="mediumSize noborder">
					<strong>Tél. fixe : </strong><?php echo Set::extract( $personne, 'Foyer.Modecontact.0.numtel' );?>
				</td>
				<td class="mediumSize noborder">
					<strong>Tél. portable : </strong>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="mediumSize noborder">
					<strong>Adresse mail : </strong><?php echo Set::extract( $personne, 'Foyer.Modecontact.0.numtel' );?>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>Situation administrative du bénéficiaire</legend>
		<table class="wide noborder">
			<tr>
				<td class="mediumSize noborder">
					<?php echo $this->Xform->enum( 'Apre.naturelogement', array( 'div' => false, 'legend' => __d( 'apre', 'Apre.naturelogement' ), 'type' => 'radio', 'separator' => '<br />', 'options' => $options['naturelogement'] ) );?>
				</td>
				<td class="noborder">
					<?php echo $this->Xform->input( 'Apre.precisionsautrelogement', array( 'domain' => 'apre', 'type' => 'textarea' ) );?>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="mediumSize noborder">
					<strong>Date de validation du contrat d'insertion par le <?php echo __d('default'.Configure::read('Cg.departement'), 'Président du Conseil Général');?>  </strong> <?php echo date_short( Set::classicExtract( $personne, 'Contratinsertion.dernier.datevalidation_ci' ) );?>
					<br />(joindre obligatoirement la copie du contrat d'insertion)
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>Parcours du bénéficiaire</legend>
		<table class="wide noborder">
			<tr>
				<td class="mediumsize noborder"><strong>Date de dernière cessation d'activité : </strong></td>
				<td class="mediumsize noborder">
					<?php echo $this->Xform->input( 'Apre.cessderact', array( 'label' => false, 'type' => 'select', 'options' => $optionsdsps['cessderact'], 'empty' => true ) );?>
				</td>
			</tr>
			<tr>
				<td class="mediumsize noborder"><strong>Ancienneté pôle emploi </strong></td>
				<td class="mediumsize noborder"><?php echo $this->Xform->input(  'Apre.anciennetepoleemploi', array( 'domain' => 'apre', 'label' => false, 'type' => 'text' ) );?></td>
			</tr>
			<tr>
				<td class="mediumsize noborder"><strong>Niveau d'étude </strong></td>
				<td class="mediumsize noborder">
					<?php echo $this->Xform->input( 'Apre.nivetu', array( 'label' => false, 'type' => 'select', 'options' => $optionsdsps['nivetu'], 'empty' => true ) );?>
				</td>
			</tr>
			<tr>
				<td class="mediumsize noborder"><strong>Projet professionnel </strong></td>
				<td class="mediumsize noborder"><?php echo $this->Xform->input(  'Apre.projetprofessionnel', array( 'domain' => 'apre', 'label' => false, 'type' => 'textarea' ) );?></td>
			</tr>
			<tr>
				<td class="mediumsize noborder"><strong><?php echo $this->Xform->required( 'Secteur professionnel en lien avec la demande' ); ?></strong></td>
				<td class="mediumsize noborder"><?php echo $this->Xform->input( 'Apre.secteurprofessionnel', array( 'domain' => 'apre', 'label' => false, 'type' => 'textarea' ) );?></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>Activité du bénéficiaire</legend>
		<table class="wide noborder">
			<tr>
				<td class="mediumsize noborder"><strong>Type d'activité </strong></td>
				<td class="mediumsize noborder"><?php echo $this->Xform->enum( "{$this->modelClass}.activitebeneficiaire", array( 'legend' => required( __d( 'apre', 'Apre.activitebeneficiaire' ) ), 'type' => 'radio', 'separator' => '<br />', 'options' => $options['activitebeneficiaire'] ) );?></td>
			</tr>
			<tr>
				<td class="mediumsize noborder"><strong>Date de l'emploi prévu </strong></td>
				<td class="mediumsize noborder"><?php echo $this->Xform->input( "{$this->modelClass}.dateentreeemploi", array( 'domain' => 'apre', 'label' => false, 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear'=>date('Y')+5, 'minYear'=>date('Y')-1, 'empty' => true ) );?></td>
			</tr>
			<tr>
				<td class="mediumsize noborder"><strong>Type de contrat </strong></td>
				<td class="mediumsize noborder"><?php echo $this->Xform->enum( "{$this->modelClass}.typecontrat", array( 'div' => false, 'legend' => false, 'type' => 'radio', 'separator' => '<br />', 'options' => $options['typecontrat'] ) );?></td>
			</tr>
			<tr>
				<td class="mediumsize noborder"><strong>Si autres, préciser  </strong></td>
				<td class="mediumsize noborder"><?php echo $this->Xform->input( "{$this->modelClass}.precisionsautrecontrat", array( 'domain' => 'apre', 'label' => false, 'type' => 'textarea' ) );?></td>
			</tr>
			<tr>
				<td class="activiteSize noborder" colspan="2"><strong>Secteur d'activité  </strong></td>
			</tr>
			<tr>
				<td class="activiteSize noborder" colspan="2"><?php echo $this->Xform->input( "{$this->modelClass}.secteuractivite", array( 'domain' => 'apre', 'label' => false, 'type' => 'select', 'class' => 'activiteSize', 'options' => $sect_acti_emp, 'empty' => true ) );?></td>
			</tr>
			<tr>
				<td class="mediumsize noborder"><strong>Nombres d'heures travaillées </strong></td>
				<td class="mediumsize noborder"><?php echo $this->Xform->input(  "{$this->modelClass}.nbheurestravaillees", array( 'domain' => 'apre', 'label' => false, 'type' => 'text' ) );?></td>
			</tr>
			<tr>
				<td class="mediumsize noborder"><strong>Nom et adresse de l'employeur </strong></td>
				<td class="mediumsize noborder"><?php echo $this->Xform->input(  "{$this->modelClass}.nomemployeur", array( 'domain' => 'apre', 'label' => false, 'type' => 'text' ) );?><?php echo $this->Xform->input(  "{$this->modelClass}.adresseemployeur", array( 'domain' => 'apre', 'label' => false, 'type' => 'textarea' ) );?></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>Structure référente</legend>
		<table class="wide noborder">
			<tr>
				<td class="noborder">
					<strong><?php echo required( 'Nom de l\'organisme' ); ?></strong>
					<?php echo $this->Xform->input( "{$this->modelClass}.structurereferente_id", array( 'domain' => 'apre', 'label' => false, 'type' => 'select', 'options' => $structs, 'selected' => $struct_id, 'empty' => true ) );?>
					<?php echo $this->Ajax->observeField( $this->modelClass.'StructurereferenteId', array( 'update' => 'StructurereferenteRef', 'url' => array( 'action' => 'ajaxstruct' ) ) ); ?>
				</td>
				<td class="noborder">
					<strong>Nom du référent</strong>
					<?php echo $this->Xform->input( "{$this->modelClass}.referent_id", array( 'domain' => 'apre', 'label' => false, 'type' => 'select', 'options' => $referents, 'selected' => $struct_id.'_'.$referent_id,'empty' => true ) );?>
					<?php echo $this->Ajax->observeField( $this->modelClass.'ReferentId', array( 'update' => 'ReferentRef', 'url' => array( 'action' => 'ajaxref' ) ) ); ?>
				</td>
			</tr>
			<tr>
				<td class="wide noborder"><div id="StructurereferenteRef"></div></td>

				<td class="wide noborder"><div id="ReferentRef"></div></td>
			</tr>
		</table>
	</fieldset>

<script type="text/javascript">
document.observe("dom:loaded", function() {

	// Javascript pour les aides liées à l'APRE
	['Formqualif', 'Formpermfimo', 'Actprof', 'Permisb', 'Amenaglogt', 'Acccreaentr', 'Acqmatprof', 'Locvehicinsert' ].each( function( formation ) {
		observeDisableFieldsetOnRadioValue(
			'<?php echo $this->modelClass;?>',
			'data[<?php echo $this->modelClass;?>][Natureaide]',
			$( formation ),
			formation,
			false,
			true
		);
	} );

	<?php
		echo $this->Ajax->remoteFunction(
			array(
				'update' => 'FormqualifCoordonnees',
				'url' => array( 'action' => 'ajaxtiersprestaformqualif', Set::extract( $this->request->data, 'Formqualif.tiersprestataireapre_id' ) )
			)
		).';';
		echo $this->Ajax->remoteFunction(
			array(
				'update' => 'FormpermfimoCoordonnees',
				'url' => array( 'action' => 'ajaxtiersprestaformpermfimo', Set::extract( $this->request->data, 'Formpermfimo.tiersprestataireapre_id' ) )
			)
		).';';
		echo $this->Ajax->remoteFunction(
			array(
				'update' => 'ActprofAdresseemployeur',
				'url' => array( 'action' => 'ajaxtiersprestaactprof', Set::extract( $this->request->data, 'Actprof.tiersprestataireapre_id' ) )
			)
		).';';
		echo $this->Ajax->remoteFunction(
			array(
				'update' => 'PermisbAdresseautoecole',
				'url' => array( 'action' => 'ajaxtiersprestapermisb', Set::extract( $this->request->data, 'Permisb.tiersprestataireapre_id' ) )
			)
		).';';
	?>

});
</script>

	<fieldset class="wide">
		<legend>Justificatif</legend>
		<?php
			echo $this->Xform->enum( "{$this->modelClass}.justificatif", array(  'legend' => false, 'div' => false,  'required' => true, 'type' => 'radio', 'separator' => '<br />', 'options' => $options['justificatif'] ) );
		?>
	</fieldset>
	<?php
		echo $this->Xform->input( 'Pieceapre.Pieceapre', array( 'options' => $piecesapre, 'multiple' => 'checkbox',  'label' => 'Pièces jointes' ) );
	?>

	<h2 class="center">Nature de la demande</h2>
	<br />
	<h3 class="center" style="font-style:italic">Liée à une Formation</h3>
	<fieldset>
		<?php
			/// Formation qualifiante
			$tmp = radioApre( $this, "{$this->modelClass}.Natureaide", 'Formqualif', 'Formations individuelles qualifiantes' );
			echo $this->Xhtml->tag( 'h3', $tmp );
		?>
		<fieldset id="Formqualif" class="invisible">
			<?php
				$FormqualifId = Set::classicExtract( $this->request->data, 'Formqualif.id' );
				if( $this->action == 'edit' && !empty( $FormqualifId ) ) {
					echo $this->Form->input( 'Formqualif.id', array( 'type' => 'hidden' ) );
				}
				echo $this->Xform->input( 'Formqualif.intituleform', array(  'required' => true, 'domain' => 'apre' ) );
				echo $this->Xform->enum( 'Formqualif.tiersprestataireapre_id', array( 'required' => true, 'domain' => 'apre', 'options' => $tiersFormqualif, 'empty' => true ) );
				echo $this->Ajax->observeField( 'FormqualifTiersprestataireapreId', array( 'update' => 'FormqualifCoordonnees', 'url' => array( 'action' => 'ajaxtiersprestaformqualif' ) ) );
				echo $this->Xhtml->tag(
					'div',
					$this->Xhtml->tag( 'div', ( isset( $FormqualifCoordonnees ) ? $FormqualifCoordonnees : ' ' ), array( 'id' => 'FormqualifCoordonnees' ) ).'<br />'
				);

				echo $this->Xform->input( 'Formqualif.ddform', array( 'required' => true, 'domain' => 'apre', 'type' => 'date', 'dateFormat' => 'DMY' ) );
				echo $this->Xform->input( 'Formqualif.dfform', array( 'required' => true, 'domain' => 'apre', 'type' => 'date', 'dateFormat' => 'DMY' ) );
				echo $this->Xform->input( 'Formqualif.dureeform', array( 'required' => true, 'domain' => 'apre' ) );
				echo $this->Xform->input( 'Formqualif.modevalidation', array( 'domain' => 'apre' ) );
				echo $this->Xform->input( 'Formqualif.coutform', array( 'required' => true, 'domain' => 'apre' ) );
				echo $this->Xform->input( 'Formqualif.cofinanceurs', array( 'domain' => 'apre' ) );
				echo $this->Xform->input( 'Formqualif.montantaide', array( 'required' => true, 'domain' => 'apre' ) );

				$selected = Set::extract( $this->request->data, '/Formqualif/Pieceformqualif/id' );
				if ( empty( $selected ) ) {
					$selected = Set::extract( $this->request->data, '/Pieceformqualif/Pieceformqualif' );
				}
				echo $this->Xform->input( 'Pieceformqualif.Pieceformqualif', array( 'options' => $piecesformqualif, 'multiple' => 'checkbox', 'label' => 'Pièces jointes','selected' => $selected ) );
			?>
		</fieldset>
	</fieldset>
	<fieldset>
		<?php
			/// Formation qualifiante Perm FIMO
			$tmp = radioApre( $this, "{$this->modelClass}.Natureaide", 'Formpermfimo', 'Formation permis de conduire Poids Lourd + FIMO' );
			echo $this->Xhtml->tag( 'h3', $tmp );

		?>
		<fieldset id="Formpermfimo" class="invisible">
			<?php
				$FormpermfimoId = Set::classicExtract( $this->request->data, 'Formpermfimo.id' );
				if( $this->action == 'edit' && !empty( $FormpermfimoId ) ) {
					echo $this->Form->input( 'Formpermfimo.id', array( 'type' => 'hidden' ) );
				}
				echo $this->Xform->input( 'Formpermfimo.intituleform', array( 'required' => true, 'domain' => 'apre' ) );
				echo $this->Xform->enum( 'Formpermfimo.tiersprestataireapre_id', array( 'required' => true, 'domain' => 'apre', 'options' => $tiersFormpermfimo, 'empty' => true ) );
				echo $this->Ajax->observeField( 'FormpermfimoTiersprestataireapreId', array( 'update' => 'FormpermfimoCoordonnees', 'url' => array( 'action' => 'ajaxtiersprestaformpermfimo' ) ) );
				echo $this->Xhtml->tag(
					'div',
					$this->Xhtml->tag( 'div', ( isset( $FormpermfimoCoordonnees ) ? $FormpermfimoCoordonnees : ' ' ), array( 'id' => 'FormpermfimoCoordonnees' ) ).'<br />'
				);

				echo $this->Xform->input( 'Formpermfimo.ddform', array( 'required' => true, 'domain' => 'apre', 'type' => 'date', 'dateFormat' => 'DMY' ) );
				echo $this->Xform->input( 'Formpermfimo.dfform', array( 'required' => true, 'domain' => 'apre', 'type' => 'date', 'dateFormat' => 'DMY' ) );
				echo $this->Xform->input( 'Formpermfimo.dureeform', array( 'required' => true, 'domain' => 'apre' ) );
				echo $this->Xform->input( 'Formpermfimo.modevalidation', array( 'domain' => 'apre' ) );
				echo $this->Xform->input( 'Formpermfimo.coutform', array( 'required' => true, 'domain' => 'apre' ) );
				echo $this->Xform->input( 'Formpermfimo.cofinanceurs', array( 'domain' => 'apre' ) );
				echo $this->Xform->input( 'Formpermfimo.montantaide', array( 'required' => true, 'domain' => 'apre' ) );;

				$selected = Set::extract( $this->request->data, '/Formpermfimo/Pieceformpermfimo/id' );
				if ( empty( $selected ) ) {
					$selected = Set::extract( $this->request->data, '/Pieceformpermfimo/Pieceformpermfimo' );
				}
				echo $this->Xform->input( 'Pieceformpermfimo.Pieceformpermfimo', array( 'options' => $piecesformpermfimo, 'multiple' => 'checkbox', 'label' => 'Pièces jointes', 'selected' => $selected ) );
			?>
		</fieldset>
	</fieldset>
	<fieldset>
		<?php
			/// Action de professionnalisation
			$tmp = radioApre( $this, "{$this->modelClass}.Natureaide", 'Actprof', 'Action de professionnalisation des contrats aides et salariés dans les SIAE' );
			echo $this->Xhtml->tag( 'h3', $tmp );

		?>
		<fieldset id="Actprof" class="invisible">
			<?php
				$ActprofId = Set::classicExtract( $this->request->data, 'Actprof.id' );
				if( $this->action == 'edit' && !empty( $ActprofId ) ) {
					echo $this->Form->input( 'Actprof.id', array( 'type' => 'hidden' ) );
				}
				echo $this->Xform->enum( 'Actprof.tiersprestataireapre_id', array( 'required' => true, 'domain' => 'apre', 'options' => $tiersActprof, 'empty' => true ) );
				echo $this->Ajax->observeField( 'ActprofTiersprestataireapreId', array( 'update' => 'ActprofAdresseemployeur', 'url' => array( 'action' => 'ajaxtiersprestaactprof' ) ) );
				echo $this->Xhtml->tag(
					'div',
					$this->Xhtml->tag( 'div', ( isset( $ActprofAdresseemployeur ) ? $ActprofAdresseemployeur : ' ' ), array( 'id' => 'ActprofAdresseemployeur' ) ).'<br />'
				);

				echo $this->Xform->enum( 'Actprof.typecontratact', array( 'required' => true, 'div' => false, 'legend' => 'Type de contrat', 'type' => 'radio', 'options' => $optionsacts['typecontratact'] ) );
				echo $this->Xform->input( 'Actprof.ddconvention', array( 'required' => true, 'domain' => 'apre', 'dateFormat' => 'DMY' ) );
				echo $this->Xform->input( 'Actprof.dfconvention', array( 'required' => true, 'domain' => 'apre', 'dateFormat' => 'DMY' ) );
				echo $this->Xform->input( 'Actprof.intituleformation', array( 'required' => true, 'domain' => 'apre' ) );
				echo $this->Xform->input( 'Actprof.ddform', array( 'required' => true, 'domain' => 'apre', 'dateFormat' => 'DMY' ) );
				echo $this->Xform->input( 'Actprof.dfform', array( 'required' => true, 'domain' => 'apre', 'dateFormat' => 'DMY' ) );
				echo $this->Xform->input( 'Actprof.dureeform', array( 'required' => true, 'domain' => 'apre' ) );
				echo $this->Xform->input( 'Actprof.modevalidation', array( 'domain' => 'apre' ) );;
				echo $this->Xform->input( 'Actprof.coutform', array('required' => true,  'domain' => 'apre' ) );
				echo $this->Xform->input( 'Actprof.cofinanceurs', array( 'domain' => 'apre' ) );
				echo $this->Xform->input( 'Actprof.montantaide', array( 'required' => true, 'domain' => 'apre' ) );

				$selected = Set::extract( $this->request->data, '/Actprof/Pieceactprof/id' );
				if ( empty( $selected ) ) {
					$selected = Set::extract( $this->request->data, '/Pieceactprof/Pieceactprof' );
				}
				echo $this->Xform->input( 'Pieceactprof.Pieceactprof', array( 'options' => $piecesactprof, 'multiple' => 'checkbox', 'label' => 'Pièces jointes', 'selected' => $selected ) );
			?>
		</fieldset>
	</fieldset>
	<fieldset>
		<?php
			/// Permis B
			$tmp = radioApre( $this, "{$this->modelClass}.Natureaide", 'Permisb', 'Permis de conduire B' );
			echo $this->Xhtml->tag( 'h3', $tmp );
		?>
		<fieldset id="Permisb" class="invisible">
			<?php
				$PermisbId = Set::classicExtract( $this->request->data, 'Permisb.id' );
				if( $this->action == 'edit' && !empty( $PermisbId ) ) {
					echo $this->Form->input( 'Permisb.id', array( 'type' => 'hidden' ) );
				}

				echo $this->Xform->enum( 'Permisb.tiersprestataireapre_id', array( 'required' => true, 'domain' => 'apre', 'options' => $tiersPermisb, 'empty' => true ) );
				echo $this->Ajax->observeField( 'PermisbTiersprestataireapreId', array( 'update' => 'PermisbAdresseautoecole', 'url' => array( 'action' => 'ajaxtiersprestapermisb' ) ) );
				echo $this->Xhtml->tag(
					'div',
					$this->Xhtml->tag( 'div', ( isset( $PermisbAdresseautoecole ) ? $PermisbAdresseautoecole : ' ' ), array( 'id' => 'PermisbAdresseautoecole' ) ).'<br />'
				);
				echo $this->Xform->input( 'Permisb.code',
					array( 'div' => false, 'label' => 'Code', 'type' => 'checkbox' )
				);
				echo $this->Xform->input( 'Permisb.conduite',
					array( 'div' => false, 'label' => 'Conduite', 'type' => 'checkbox' )
				);
				echo $this->Xform->input( 'Permisb.dureeform', array( 'required' => true, 'domain' => 'apre' ) );
				echo $this->Xform->input( 'Permisb.montantaide', array( 'required' => true, 'domain' => 'apre', 'maxlength' => 4 ) );

				$selected = Set::extract( $this->request->data, '/Permisb/Piecepermisb/id' );
				if ( empty( $selected ) ) {
					$selected = Set::extract( $this->request->data, '/Piecepermisb/Piecepermisb' );
				}
				echo $this->Xform->input( 'Piecepermisb.Piecepermisb', array( 'options' => $piecespermisb, 'multiple' => 'checkbox', 'label' => 'Pièces jointes', 'selected' => $selected ) );
			?>
		</fieldset>
	</fieldset>
	<br />
	<h3 class="center" style="font-style:italic">Hors Formation</h3>
	<fieldset>
		<?php
			/// Amenagement logement

			$tmp = radioApre( $this, "{$this->modelClass}.Natureaide", 'Amenaglogt', 'Aide à l\'installation' );
			echo $this->Xhtml->tag( 'h3', $tmp );
		?>
		<fieldset id="Amenaglogt" class="invisible">
			<?php
				$AmenaglogtId = Set::classicExtract( $this->request->data, 'Amenaglogt.id' );
				if( $this->action == 'edit' && !empty( $AmenaglogtId ) ) {
					echo $this->Form->input( 'Amenaglogt.id', array( 'type' => 'hidden' ) );
				}
			?>
			<div class="demi">
				<?php echo $this->Form->input( 'Amenaglogt.typeaidelogement', array( 'label' => 'Type d\'aide au logement : ' , 'type' => 'radio', 'div' => false, 'separator' => '</div><div class="demi">', 'options' => $optionslogts['typeaidelogement'], 'legend' => false ) );?>
			</div>

			<?php
				echo $this->Xform->address( 'Amenaglogt.besoins', array( 'domain' => 'apre' ) );
				echo $this->Xform->input( 'Amenaglogt.montantaide', array( 'required' => true, 'domain' => 'apre' ) );

				$selected = Set::extract( $this->request->data, '/Amenaglogt/Pieceamenaglogt/id' );
				if ( empty( $selected ) ) {
					$selected = Set::extract( $this->request->data, '/Pieceamenaglogt/Pieceamenaglogt' );
				}
				echo $this->Xform->input( 'Pieceamenaglogt.Pieceamenaglogt', array( 'options' => $piecesamenaglogt, 'multiple' => 'checkbox', 'label' => 'Pièces jointes', 'selected' => $selected ) );
			?>
		</fieldset>
	</fieldset>
	<fieldset>
		<?php
			/// Accompagnement à la création d'entreprise

			$tmp = radioApre( $this, "{$this->modelClass}.Natureaide", 'Acccreaentr', 'Accompagnement à la création d\'entreprise' );
			echo $this->Xhtml->tag( 'h3', $tmp );
		?>
		<fieldset id="Acccreaentr" class="invisible">
			<?php
				$AcccreaentrId = Set::classicExtract( $this->request->data, 'Acccreaentr.id' );
				if( $this->action == 'edit' && !empty( $AcccreaentrId ) ) {
					echo $this->Form->input( 'Acccreaentr.id', array( 'type' => 'hidden' ) );
				}
				echo $this->Xform->enum( 'Acccreaentr.nacre', array( 'required' => true, 'legend' => 'Dispositif Nacre', 'div' => false, 'type' => 'radio', 'options' => $optionscrea['nacre'] ) );
				echo $this->Xform->enum( 'Acccreaentr.microcredit', array( 'required' => true, 'legend' => 'Dispositif Micro-crédit', 'div' => false, 'type' => 'radio', 'options' => $optionscrea['microcredit'] ) );
				echo $this->Xform->address( 'Acccreaentr.projet', array( 'domain' => 'apre' ) );
				echo $this->Xform->input( 'Acccreaentr.montantaide', array( 'required' => true, 'domain' => 'apre' ) );

				$selected = Set::extract( $this->request->data, '/Acccreaentr/Pieceacccreaentr/id' );
				if ( empty( $selected ) ) {
					$selected = Set::extract( $this->request->data, '/Pieceacccreaentr/Pieceacccreaentr' );
				}
				echo $this->Xform->input( 'Pieceacccreaentr.Pieceacccreaentr', array( 'options' => $piecesacccreaentr, 'multiple' => 'checkbox', 'label' => 'Pièces jointes', 'selected' => $selected ) );
			?>
		</fieldset>
	</fieldset>
	<fieldset>
		<?php
			/// Acquisition de matériels professionnels

			$tmp = radioApre( $this, "{$this->modelClass}.Natureaide", 'Acqmatprof', 'Acquisition de matériels professionnels' );
			echo $this->Xhtml->tag( 'h3', $tmp );
		?>
		<fieldset id="Acqmatprof" class="invisible">
			<?php
				$AcqmatprofId = Set::classicExtract( $this->request->data, 'Acqmatprof.id' );
				if( $this->action == 'edit' && !empty( $AcqmatprofId ) ) {
					echo $this->Form->input( 'Acqmatprof.id', array( 'type' => 'hidden' ) );
				}
				echo $this->Xform->address( 'Acqmatprof.besoins', array( 'domain' => 'apre' ) );
				echo $this->Xform->input( 'Acqmatprof.montantaide', array( 'required' => true, 'domain' => 'apre' ) );

				$selected = Set::extract( $this->request->data, '/Acqmatprof/Pieceacqmatprof/id' );
				if ( empty( $selected ) ) {
					$selected = Set::extract( $this->request->data, '/Pieceacqmatprof/Pieceacqmatprof' );
				}
				echo $this->Xform->input( 'Pieceacqmatprof.Pieceacqmatprof', array( 'options' => $piecesacqmatprof, 'multiple' => 'checkbox', 'label' => 'Pièces jointes', 'selected' => $selected ) );
			?>
		</fieldset>
	</fieldset>
	<fieldset>
		<?php
			/// Aide à la location d'un véhicule d'insertion

			$tmp = radioApre( $this, "{$this->modelClass}.Natureaide", 'Locvehicinsert', 'Aide à la location d\'un véhicule d\'insertion' );
			echo $this->Xhtml->tag( 'h3', $tmp );
		?>
		<fieldset id="Locvehicinsert" class="invisible">
			<?php
				$LocvehicinsertId = Set::classicExtract( $this->request->data, 'Locvehicinsert.id' );
				if( $this->action == 'edit' && !empty( $LocvehicinsertId ) ) {
					echo $this->Form->input( 'Locvehicinsert.id', array( 'type' => 'hidden' ) );
				}
				echo $this->Xform->input( 'Locvehicinsert.societelocation', array('required' => true,  'domain' => 'apre' ) );
				echo $this->Xform->input( 'Locvehicinsert.dureelocation', array( 'required' => true, 'domain' => 'apre' ) );
				echo $this->Xform->input( 'Locvehicinsert.montantaide', array('required' => true,  'domain' => 'apre' ) );

				$selected = Set::extract( $this->request->data, '/Locvehicinsert/Piecelocvehicinsert/id' );
				if ( empty( $selected ) ) {
					$selected = Set::extract( $this->request->data, '/Piecelocvehicinsert/Piecelocvehicinsert' );
				}
				echo $this->Xform->input( 'Piecelocvehicinsert.Piecelocvehicinsert', array( 'options' => $pieceslocvehicinsert, 'multiple' => 'checkbox', 'label' => 'Pièces jointes', 'selected' => $selected ) );
			?>
		</fieldset>
	</fieldset>
	<fieldset class="aere">
			<legend>Avis technique et motivé du référent (Article 5.1 relatif au règlement de l'APRE): </legend>
		<?php
			echo $this->Xform->input(  "{$this->modelClass}.avistechreferent", array( 'domain' => 'apre', 'label' => false ) );?>
	</fieldset>

</div>

<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
	<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();?>

<script type="text/javascript">
	Event.observe( $( 'ApreStructurereferenteId' ), 'change', function( event ) {
		$( 'ReferentRef' ).update( '' );
	} );
</script>