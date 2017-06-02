<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

    $this->modelClass = Inflector::classify( $this->request->params['controller'] );

    $this->pageTitle = 'APRE/ADRE';

    if( $this->action == 'add' ) {
        $this->pageTitle = 'Ajout APRE/ADRE';
    }
    else {
        $this->pageTitle = 'Édition APRE/ADRE';
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
            dependantSelect(
                'Aideapre66Typeaideapre66Id',
                'Aideapre66Themeapre66Id'
            );


            observeDisableFieldsetOnCheckbox( '<?php echo $this->modelClass;?>Hasfrais', $( 'Fraisdeplacement66Destination' ).up( 'fieldset' ), false );

            observeDisableFieldsetOnRadioValue(
                'Apre',
                'data[Aideapre66][versement]',
                $( 'Soussigne' ),
                'TIE',
                false,
                true
            );
        });

	</script>

<!--/************************************************************************/ -->

<!--/************************************************************************/ -->

<script type="text/javascript">
    document.observe("dom:loaded", function() {

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


			echo $this->Ajax->remoteFunction(
				array(
					'update' => 'Piece66',
					'url' => array( 'action' => 'ajaxpiece', ( $this->action == 'add' ? null : $this->request->data['Apre66']['id'] ) ),
					'with' => 'Form.serialize( $( \'Apre\' ) )'
				)
			).';';
        ?>

    });
</script>

    <h1>Formulaire de demande de l'APRE/ADRE</h1>
	<br />
    <?php
        echo $this->Form->create( 'Apre', array( 'type' => 'post', 'id' => 'Apre', 'novalidate' => true ) );
        $ApreId = Set::classicExtract( $this->request->data, "{$this->modelClass}.id" );
        if( $this->action == 'edit' ) {
            echo '<div>';
            echo $this->Form->input( "{$this->modelClass}.id", array( 'type' => 'hidden' ) );
            echo '</div>';
        }
        echo '<div>';
        echo $this->Form->input( 'Personne.id', array( 'type' => 'hidden', 'value' => $personne_id ) );
        echo $this->Form->input( "{$this->modelClass}.personne_id", array( 'type' => 'hidden', 'value' => $personne_id ) );
        echo '</div>';
    ?>

    <div class="aere">
<?php
	echo '<fieldset><legend>Formulaire</legend>'
		. $this->Default3->subform(
			array(
				$this->modelClass.'.isapre'
			),
			array('options' => array($this->modelClass => $options))
		) . '</fieldset>'
	;
?>
        <fieldset>
            <legend>Demandeur</legend>
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
                            $isPoleemploi = Set::classicExtract( $personne, 'Historiqueetatpe.etat' );
                            if( $isPoleemploi == 'inscription' )
                                echo 'Oui';
                            else
                                echo 'Non';
                        ?>
                        <br />
                        <strong>N° identifiant : </strong><?php echo Set::classicExtract( $personne, 'Personne.idassedic' );?>
                        <!-- <br />
                        <strong>Nbre d'enfants : </strong><?php /*echo $nbEnfants;*/?> -->
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="mediumSize noborder">
                        <strong>Adresse : </strong><br /><?php echo Set::classicExtract( $personne, 'Adresse.numvoie' ).' '.Set::classicExtract( $personne, 'Adresse.libtypevoie' ).' '.Set::classicExtract( $personne, 'Adresse.nomvoie' ).'<br /> '.Set::classicExtract( $personne, 'Adresse.codepos' ).' '.Set::classicExtract( $personne, 'Adresse.nomcom' );?>
                    </td>
                </tr>
				<tr>
					<td class="mediumSize noborder">
						<strong>Tél. fixe : </strong>
						<?php
							$numtelfixe = Set::classicExtract( $personne, 'Personne.numfixe' );
							if( !empty( $numtelfixe ) ) {
								echo Set::extract( $personne, 'Personne.numfixe' );
							}
							else{
								echo $this->Xform->input( 'Personne.numfixe', array( 'label' => false, 'type' => 'text' ) );

							}
						?>
					</td>
					<td class="mediumSize noborder">
						<strong>Tél. portable : </strong>
						<?php
							$numtelport = Set::extract( $personne, 'Personne.numport' );
							if( !empty( $numtelport ) ) {
								echo Set::extract( $personne, 'Personne.numport' );
							}
							else{
								echo $this->Xform->input( 'Personne.numport', array( 'label' => false, 'type' => 'text' ) );
							}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="mediumSize noborder">
						<strong>Adresse mail : </strong>
						<?php
							$email = Set::extract( $personne, 'Personne.email' );
							if( !empty( $email ) ) {
								echo Set::extract( $personne, 'Personne.email' );
							}
							else{
								echo $this->Xform->input( 'Personne.email', array( 'label' => false, 'type' => 'text' ) );
							}
						?>
					</td>
				</tr>
            </table>
        </fieldset>

        <fieldset>
            <legend>Référent ou prescripteur habilité</legend>
            <table class="wide noborder">
                <tr>
                    <td class="noborder">
                        <strong>Nom de l'organisme</strong>
                        <?php echo $this->Xform->input( "{$this->modelClass}.structurereferente_id", array( 'domain' => 'apre', 'label' => false, 'type' => 'select', 'options' => $structuresreferentes, 'empty' => true ) );?>
                        <?php echo $this->Ajax->observeField( $this->modelClass.'StructurereferenteId', array( 'update' => 'StructurereferenteRef', 'url' => array( 'action' => 'ajaxstruct' ) ) ); ?>
                    </td>
                    <td class="noborder">
                        <strong>Nom du référent</strong>
                        <?php echo $this->Xform->input( "{$this->modelClass}.referent_id", array( 'domain' => 'apre', 'label' => false, 'type' => 'select', 'options' => $referents, 'empty' => true ) );?>
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
    Event.observe( $( 'Apre66StructurereferenteId' ), 'change', function( event ) {
        $( 'ReferentRef' ).update( '' );
    } );
</script>

         <fieldset>
            <legend>Activité (Emploi, formation, Création d'entreprise)</legend>
            <table class="wide noborder">
                <tr>
                    <td class="mediumsize noborder"><strong>Type d'activité </strong></td>
                    <td class="mediumsize noborder"><?php echo $this->Xform->enum( "{$this->modelClass}.activitebeneficiaire", array( 'legend' => __d( 'apre', 'Apre.activitebeneficiaire' ), 'type' => 'radio', 'separator' => '<br />', 'options' => array( 'P' => 'Recherche d\'Emploi', 'E' => 'Emploi' , 'F' => 'Formation', 'C' => 'Création d\'Entreprise' ) ) );?></td>
                </tr>
            </table>
        </fieldset>

		<fieldset>
			<?php
				//Ajout des 3 checkbox pour les APREs 66 concernant le droit ou non à une APRE
				echo $this->Xform->input( "{$this->modelClass}.isbeneficiaire", array( 'label' => __d( 'apre', 'Apre66.isbeneficiaire' ), 'type' => 'checkbox' ) );
				echo $this->Xform->input( "{$this->modelClass}.hascer", array( 'label' => __d( 'apre', 'Apre66.hascer' ), 'type' => 'checkbox' ) );
				echo $this->Xform->input( "{$this->modelClass}.respectdelais", array( 'label' => __d( 'apre', 'Apre66.respectdelais' ), 'type' => 'checkbox' ) );
			?>
		</fieldset>
<fieldset>
    <legend><strong>Aide demandée</strong></legend>
    <?php

        $Aideapre66Id = Set::classicExtract( $this->request->data, 'Aideapre66.id' );
        $Fraisdeplacement66Id = Set::classicExtract( $this->request->data, 'Fraisdeplacement66.id' );
        $ApreId = Set::classicExtract( $this->request->data, "{$this->modelClass}.id" );


        if( $this->action == 'edit' && !empty( $Aideapre66Id ) ) {
            echo $this->Form->input( 'Aideapre66.id', array( 'type' => 'hidden' ) );
            echo $this->Form->input( 'Fraisdeplacement66.id', array( 'type' => 'hidden' ) );
            echo $this->Form->input( 'Aideapre66.apre_id', array( 'type' => 'hidden', 'value' => $ApreId ) );
            echo $this->Form->input( 'Fraisdeplacement66.aideapre66_id', array( 'type' => 'hidden', 'value' => $Aideapre66Id ) );

        }

        echo $this->Default->subform(
            array(
                'Aideapre66.themeapre66_id' => array( 'options' => $themes ),
                'Aideapre66.typeaideapre66_id' => array( 'options' => $typesaides )
            ),
            array(
                'options' => $options
            )
        );

        $ajaxOptions = array(
			'url' => array( 'action' => 'ajaxpiece', ( $this->action == 'add' ? null : $this->request->data['Apre66']['id'] ) ),
			'update' => 'Piece66',
			'with' => 'Form.serialize( $( \'Apre\' ) )'
        );

        echo $this->Ajax->observeField( 'Aideapre66Typeaideapre66Id', $ajaxOptions );
        echo $this->Ajax->observeField( 'Apre66Isapre', $ajaxOptions );



        echo $this->Xhtml->tag( 'div', null, array( 'id' => 'Piece66' ) );
        echo $this->Xhtml->tag( '/div' );


        echo $this->Default->subform(
            array(
                'Aideapre66.virement' => array( 'domain' => 'aideapre66', 'type' => 'radio', 'options' => $options['virement'], 'separator' => '<br />' ),
                'Aideapre66.versement' => array( 'domain' => 'aideapre66', 'type' => 'radio', 'options' => $options['versement'], 'separator' => '<br />' )
            ),
            array(
                'options' => $options
            )
        );

        echo $this->Xhtml->tag(
            'fieldset',
            'Je soussigné '. '<strong>'.Set::enum( Set::classicExtract( $personne, 'Personne.qual') , $qual ).' '.$personne['Personne']['nom'].' '.$personne['Personne']['prenom'].'</strong>'.' souhaite que mon aide ( si elle est acceptée ) soit versée sur le compte du '.$this->Default->subform( 'Aideapre66.creancier', array( 'rows' => 2 ) ),
            array( 'id' => 'Soussigne' )
        );

        echo $this->Default->subform(
            array(
                'Aideapre66.datedemande' => array( 'empty' => false, 'dateFormat' => 'DMY' )
            )
        );
    ?>
</fieldset>
<?php
    if( !empty( $listApres ) ) {
        $Aideapre66Id = Set::extract( $listesAidesSelonApre, '/Aideapre66/apre_id' );
    }
?>
<fieldset>
    <legend>Attributions antérieures de l'APRE/ADRE (le cas échéant)</legend>
    <?php if( !empty( $listesAidesSelonApre ) ):?>
        <table>
            <thead>
                <tr>
                    <th>Date de demande de l'APRE/ADRE</th>
                    <th>Thème de l'aide</th>
                    <th>Type d'aide</th>
                    <th>Montant accordé</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach( $listesAidesSelonApre as $i => $liste ){

                        echo $this->Xhtml->tableCells(
                            array(
                                h( date_short( Set::classicExtract( $liste, 'Aideapre66.datedemande' ) ) ),
                                h( Set::enum( Set::classicExtract( $liste, 'Aideapre66.themeapre66_id' ), $themes ) ),
                                h( Set::enum( Set::classicExtract( $liste, 'Aideapre66.typeaideapre66_id' ), $nomsTypeaide ) ),
                                h( $this->Locale->money( Set::classicExtract( $liste, 'Aideapre66.montantaccorde' ) ) ),
                            )
                        );
                    }
                ?>
            </tbody>
        </table>
    <?php else:?>
        <p class="notice">Aucune APRE/ADRE antérieure présente pour cette personne</p>
    <?php endif;?>
</fieldset>
<?php
    echo $this->Xform->input( "{$this->modelClass}.hasfrais", array( 'label' => 'Présence de frais', 'type' => 'checkbox' ) );
?>
<fieldset id="Hasfrais">
    <legend><strong>Calcul des frais de déplacements, d'hébergement et de restauration</strong></legend>
    <?php
        $tmp = array(
            'Fraisdeplacement66.lieuresidence' => Set::extract( $personne, 'Adresse.numvoie' ).' '.Set::extract( $personne, 'Adresse.libtypevoie' ).' '.Set::extract( $personne, 'Adresse.nomvoie' ).' '.Set::extract( $personne, 'Adresse.codepos' ).' '.Set::extract( $personne, 'Adresse.nomcom' )
        );
        echo $this->Default->view(
            Hash::expand( $tmp ),
            array(
                'Fraisdeplacement66.lieuresidence'
            ),
            array(
                'class' => 'inform'
            )
        );

        echo $this->Xform->input( 'Fraisdeplacement66.destination', array( 'label' => __d( 'fraisdeplacement66', "Fraisdeplacement66.destination" ), 'type' => 'text', 'required' => true ) );
    ?>

	<div class="fraisdepct">
		<table class="fraisdepct">
			<caption>Véhicule personnel</caption>
			<tbody>
				<tr>
					<th>Nb km par trajet</th>
					<td colspan="2"  class="fraisdepct"><?php echo $this->Xform->input( 'Fraisdeplacement66.nbkmvoiture', array( 'label' => false, 'div' => false, 'type' => 'text' ) );?></td>
				</tr>
				<tr>
					<th>Nb trajet </th>
					<td colspan="2"  class="fraisdepct"><?php echo $this->Xform->input( 'Fraisdeplacement66.nbtrajetvoiture', array( 'label' => false, 'div' => false, 'type' => 'text' ) );?></td>
				</tr>
                <tr>
                    <th>Nb total km</th>
                    <td colspan="2"  class="fraisdepct"><span></span><?php echo $this->Xform->input( 'Fraisdeplacement66.nbtotalkm', array( 'label' => false, 'div' => false, 'type' => 'hidden' ) );?> </td>
                </tr>
				<tr>
					<th>Forfait "Km"</th>
					<td colspan="2"  class="fraisdepct"><?php echo $this->Locale->money( Configure::read( 'Fraisdeplacement66.forfaitvehicule' ) );?></td>
				</tr>
				<tr>
					<th>Total</th>
					<td colspan="2"  class="fraisdepct noborder"><span></span><?php echo $this->Xform->input( 'Fraisdeplacement66.totalvehicule', array( 'label' => false, 'div' => false, 'type' => 'hidden' ) );?> &euro;</td>
<!--                     <td  class="fraisdepct noborder">&euro;</td> -->
				</tr>
			</tbody>
		</table>
	</div>

	<div class="fraisdepct">
		<table class="fraisdepct">
			<caption>Transport public</caption>
			<tbody>
				<tr>
					<th>Nb trajet</th>
					<td colspan="2"  class="fraisdepct"><?php echo $this->Xform->input( 'Fraisdeplacement66.nbtrajettranspub', array( 'label' => false, 'div' => false, 'type' => 'text' ) );?></td>
				</tr>
				<tr>
					<th>Prix billet </th>
					<td colspan="2"  class="fraisdepct"><?php echo $this->Xform->input( 'Fraisdeplacement66.prixbillettranspub', array( 'label' => false, 'div' => false, 'type' => 'text' ) );?></td>
				</tr>
				<tr>
					<th>Total</th>
					<td colspan="2" class="fraisdepct noborder"><span></span><?php echo $this->Xform->input( 'Fraisdeplacement66.totaltranspub', array( 'label' => false, 'div' => false, 'type' => 'hidden' ) );?> &euro;</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="fraisdepct">
		<table class="fraisdepct">
			<caption>Hébergement</caption>
			<tbody>
				<tr>
					<th>Nb nuitées</th>
					<td colspan="2"  class="fraisdepct"><?php echo $this->Xform->input( 'Fraisdeplacement66.nbnuithebergt', array( 'label' => false, 'div' => false, 'type' => 'text' ) );?></td>
				</tr>
				<tr>
					<th>Forfait "nuitées"</th>
					<td colspan="2"  class="fraisdepct"><?php echo $this->Locale->money( Configure::read( 'Fraisdeplacement66.forfaithebergt' ) );?></td>
				</tr>
				<tr>
					<th>Total</th>
					<td class="fraisdepct noborder"><span></span><?php echo $this->Xform->input( 'Fraisdeplacement66.totalhebergt', array( 'label' => false, 'div' => false, 'type' => 'hidden' ) );?> &euro;</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="fraisdepct">
		<table class="fraisdepct">
			<caption>Repas</caption>
			<tbody>
				<tr>
					<th>Nb repas</th>
					<td colspan="2"  class="fraisdepct"><?php echo $this->Xform->input( 'Fraisdeplacement66.nbrepas', array( 'label' => false, 'div' => false, 'type' => 'text' ) );?></td>
				</tr>
				<tr>
					<th>Forfait "Repas"</th>
					<td colspan="2" class="fraisdepct"><?php echo $this->Locale->money( Configure::read( 'Fraisdeplacement66.forfaitrepas' ) );?></td>
				</tr>
				<tr>
					<th>Total</th>
					<td class="fraisdepct noborder"><span></span><?php echo $this->Xform->input( 'Fraisdeplacement66.totalrepas', array( 'label' => false, 'div' => false, 'type' => 'hidden' ) );?> &euro;</td>
				</tr>
			</tbody>
		</table>
	</div>

</fieldset>

<fieldset class="aere">
    <legend><strong>Observations du référent</strong></legend>
    <?php
        echo $this->Xform->input(  "{$this->modelClass}.avistechreferent", array( 'domain' => 'apre', 'label' => false, 'type' => 'textarea', 'required' => true ) );
    ?>
</fieldset>

<fieldset>
    <legend><strong>Administration</strong></legend>
        <?php
            echo $this->Default->subform(
                array(
                    'Aideapre66.motifrejet',
                    'Aideapre66.montantpropose' => array( 'type' => 'text' ),
                    'Aideapre66.datemontantpropose' => array( 'empty' => false, 'dateFormat' => 'DMY' )
                )
            );
        ?>
</fieldset>

<?php if( $this->action == 'edit' ):?>
	<?php
// 		$error = Set::classicExtract( $this->validationErrors, "{$this->modelClass}.isdecision" );
// 		$class = 'radio'.( !empty( $error ) ? ' error' : '' );
// 		$thisDataIsDecision = Set::classicExtract( $this->request->data, "{$this->modelClass}.isdecision" );
// 		if( !empty( $thisDataIsDecision ) ) {
// 			$valueIsDecision = $thisDataIsDecision;
// 		}
// 		$input = $this->Form->input( "{$this->modelClass}.isdecision", array( 'type' => 'radio' , 'options' => $options['isdecision'], 'legend' => required( __d( 'apre', "{$this->modelClass}.isdecision" )  ), 'value' => $valueIsDecision ) );
// 		echo $this->Xhtml->tag( 'div', $input, array( 'class' => $class ) );
	?>

	<fieldset id="DecisionApre">
		<legend><strong>Décision et engagement financier de l'équipe de direction</strong></legend>
			<?php
				$avis = Set::classicExtract( $this->request->data, 'Aideapre66.decisionapre' );
				if( !empty( $avis ) ){
					$tmp = array(
						'Aideapre66.decisionapre' => Set::enum( Set::classicExtract( $this->request->data, 'Aideapre66.decisionapre' ), $options['decisionapre'] ),
						'Aideapre66.montantaccorde' => Set::classicExtract( $this->request->data, 'Aideapre66.montantaccorde' ),
						'Aideapre66.motifrejetequipe' => Set::classicExtract( $this->request->data, 'Aideapre66.motifrejetequipe' ),
						'Aideapre66.datemontantaccorde' => Set::classicExtract( $this->request->data, 'Aideapre66.datemontantaccorde' )
					);
					echo $this->Default->view(
						Hash::expand( $tmp ),
						array(
							'Aideapre66.decisionapre',
							'Aideapre66.montantaccorde' => array( 'type' => 'money' ),
							'Aideapre66.motifrejetequipe' => array( 'type' => 'text' ),
							'Aideapre66.datemontantaccorde' => array( 'type' => 'date' )
						),
						array(
							'class' => 'inform'
						)
					);
				}
				else{
					echo $this->Xhtml->tag(
						'p',
						'Aucune décision n\'a encore été prise pour cette demande d\'APRE/ADRE',
						array( 'class' => 'notice' )
					);
				}



// 				echo $this->Default->subform(
// 					array(
// 						'Aideapre66.decisionapre' => array( 'legend' => false, 'type' => 'radio', 'options' => $options['decisionapre'], 'separator' => '<br />' ),
// 						'Aideapre66.montantaccorde' => array( 'type' => 'text' ),
// 						'Aideapre66.motifrejetequipe' => array( 'type' => 'textarea' ),
// 						'Aideapre66.datemontantaccorde' => array( 'empty' => false )
// 					),
// 					array(
// 						'class' => 'fraisdepct'
// 					)
// 				);
			?>

	</fieldset>

<?php endif;?>

<fieldset class="loici">
    <p>
        Un formulaire de demande par type d'aide demandée. Il doit être établi par un référent, pour Pôle Emploi en son absence par un prescripteur habilité.
    </p>
</fieldset>
        <?php
        ///FIXME: Voir si on peut faire mieux
            $etat = Set::enum( Set::classicExtract( $this->request->data, "{$this->modelClass}.etatdossierapre" ), $options['etatdossierapre'] );
//             debug($etat);
            if( empty( $etat ) ) {
                echo 'Etat du dossier : <strong>'.$etat.'</strong>';
            }
            else{
                echo 'Etat du dossier : <strong>'.$etat.'</strong>';
            }
        ?>
    </div>

    <div class="submit">
        <?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
        <?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
    </div>
    <?php echo $this->Form->end();?>

<script type="text/javascript">
    /**
    *   Javascript gérant les frais de déplacements
    * FIXME: vérifier les types
    */

    function frenchToJsFloatValue( id ) {
        return $F( id ).replace( ',', '.' );
    }

    function jsToFrenchFloatValue( jsValue ) {
        return jsValue.toString().replace( '.', ',' );
    }

    function calculTotalVoiture() {
        // Frais de déplacement pour un véhicule individuel
        var Nbkmvoiture = frenchToJsFloatValue( 'Fraisdeplacement66Nbkmvoiture' );
        var Nbtrajetvoiture = frenchToJsFloatValue( 'Fraisdeplacement66Nbtrajetvoiture' );

		var Nbtotalkm = jsToFrenchFloatValue( Nbkmvoiture * Nbtrajetvoiture );
        $( 'Fraisdeplacement66Nbtotalkm' ).setValue( Nbtotalkm );
		$( 'Fraisdeplacement66Nbtotalkm' ).up().down( 'span' ).update( Nbtotalkm );

		var TotalVehicule = jsToFrenchFloatValue( ( Nbkmvoiture * Nbtrajetvoiture * <?php echo str_replace( ',', '.', Configure::read( 'Fraisdeplacement66.forfaitvehicule' ) );?> ).toFixed( 2 ) );
        $( 'Fraisdeplacement66Totalvehicule' ).setValue( TotalVehicule );
		$( 'Fraisdeplacement66Totalvehicule' ).up().down( 'span' ).update( TotalVehicule );
    }
    // Frais de déplacement pour un véhicule individuel
    $( 'Fraisdeplacement66Nbtotalkm' ).observe( 'blur', function( event ) { calculTotalVoiture(); } );
    $( 'Fraisdeplacement66Nbtrajetvoiture' ).observe( 'blur', function( event ) { calculTotalVoiture(); } );


    function calculTotalTranspub() {
        // Frais de déplacement pour un transport public
        var Nbtrajettranspub = frenchToJsFloatValue( 'Fraisdeplacement66Nbtrajettranspub' );
        var Prixbillettranspub = frenchToJsFloatValue( 'Fraisdeplacement66Prixbillettranspub' );
		var TotalTransportpub = jsToFrenchFloatValue( Nbtrajettranspub * Prixbillettranspub );
        $( 'Fraisdeplacement66Totaltranspub' ).setValue( TotalTransportpub );
		$( 'Fraisdeplacement66Totaltranspub' ).up().down( 'span' ).update( TotalTransportpub );

    }
    // Frais de déplacement pour un transport public
    $( 'Fraisdeplacement66Nbtrajettranspub' ).observe( 'blur', function( event ) { calculTotalTranspub(); } );
    $( 'Fraisdeplacement66Prixbillettranspub' ).observe( 'blur', function( event ) { calculTotalTranspub(); } );

    function calcultotalHebergt() {
        // Frais de déplacement pour un hébergement
        var Nbnuithebergt = frenchToJsFloatValue( 'Fraisdeplacement66Nbnuithebergt' );
		var Totalhebergt = jsToFrenchFloatValue( Nbnuithebergt * <?php echo str_replace( ',', '.', Configure::read( 'Fraisdeplacement66.forfaithebergt' ) ); ?> );
        $( 'Fraisdeplacement66Totalhebergt' ).setValue( Totalhebergt );
		$( 'Fraisdeplacement66Totalhebergt' ).up().down( 'span' ).update( Totalhebergt );
    }
    // Frais de déplacement pour un hébergement
    $( 'Fraisdeplacement66Nbnuithebergt' ).observe( 'blur', function( event ) { calcultotalHebergt(); } );



    function calculTotalRepas() {
        // Frais de déplacement pour un repas
        var Nbrepas = frenchToJsFloatValue( 'Fraisdeplacement66Nbrepas' );
		var Totalrepas = jsToFrenchFloatValue( Nbrepas * <?php echo str_replace( ',', '.', Configure::read( 'Fraisdeplacement66.forfaitrepas' ) );?> );
        $( 'Fraisdeplacement66Totalrepas' ).setValue( Totalrepas );
		$( 'Fraisdeplacement66Totalrepas' ).up().down( 'span' ).update( Totalrepas );
    }
    // Frais de déplacement pour un repas
    $( 'Fraisdeplacement66Nbrepas' ).observe( 'blur', function( event ) { calculTotalRepas(); } );

</script>

<script type="text/javascript">
	calculTotalVoiture();
	calculTotalTranspub();
	calcultotalHebergt();
	calculTotalRepas();

	var options = {
		APRE: <?php echo json_encode($typeaideOptions['APRE']);?>,
		ADRE: <?php echo json_encode($typeaideOptions['ADRE']);?>
	};

	function limiteOptionsTypeaideapre() {
		setTimeout(function(){
			var optionsAvailables = $('Apre66Isapre').getValue() === '1' ? options['APRE'] : options['ADRE'],
				selectedValue = $('Aideapre66Typeaideapre66Id').getValue()
			;
			$$('#Aideapre66Typeaideapre66Id option').each(function(option) {
				var value = option.getAttribute('value');
				if (value !== '' && !inArray(value, optionsAvailables)) {
					option.setAttribute('style', 'display:none;');
					if (value === selectedValue) {
						option.up('select').setValue('');
					}
				} else {
					option.removeAttribute('style');
				}
			});

			$$('#Aideapre66VirementCHE, label[for="Aideapre66VirementCHE"]').each(function(element){
				if ($('Apre66Isapre').getValue() === '1') {
					element.removeAttribute('style');
				} else {
					element.setAttribute('style', 'display:none;');
					if (element.checked) {
						element.checked = false;
					}
				}
			});
		},200);
	}

	$('Apre66Isapre').observe('change', limiteOptionsTypeaideapre);
	$('Aideapre66Themeapre66Id').observe('change', limiteOptionsTypeaideapre);
	limiteOptionsTypeaideapre();
</script>