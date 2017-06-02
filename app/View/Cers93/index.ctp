<?php
	$title_for_layout = 'Contrats d\'engagement réciproque';
	$this->set( 'title_for_layout', $title_for_layout );

	App::uses( 'WebrsaAccess', 'Utility' );
	WebrsaAccess::init( $dossierMenu );

	echo $this->Html->tag( 'h1', $title_for_layout );
	echo $this->element( 'ancien_dossier' );
?>
<?php if( !empty( $signalementseps93 ) ):?>
	<h2>Signalements pour non respect du contrat</h2>
	<table class="tooltips">
		<thead>
			<tr>
				<th>Date début contrat</th>
				<th>Date fin contrat</th>
				<th>Date signalement</th>
				<th>Rang signalement</th>
				<th>État dossier EP</th>
				<th colspan="2" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach( $signalementseps93 as $signalementep93 ):?>
			<?php
				$etatdossierep = Set::enum( $signalementep93['Passagecommissionep']['etatdossierep'], $optionsdossierseps['Passagecommissionep']['etatdossierep'] );
				if( empty( $etatdossierep ) ) {
					$etatdossierep = 'En attente';
				}
			?>
			<tr>
				<td><?php echo $this->Locale->date( 'Locale->date', $signalementep93['Contratinsertion']['dd_ci'] );?></td>
				<td><?php echo $this->Locale->date( 'Locale->date', $signalementep93['Contratinsertion']['df_ci'] );?></td>
				<td><?php echo $this->Locale->date( 'Locale->date', $signalementep93['Signalementep93']['date'] );?></td>
				<td><?php echo h( $signalementep93['Signalementep93']['rang'] );?></td>
				<td><?php echo h( $etatdossierep );?></td>
				<td class="action"><?php echo $this->Default->button( 'edit', array( 'controller' => 'signalementseps', 'action' => 'edit', $signalementep93['Signalementep93']['id'] ), array( 'enabled' => ( empty( $signalementep93['Passagecommissionep']['etatdossierep'] ) ) ) );?></td>
				<td class="action"><?php echo $this->Default->button( 'delete', array( 'controller' => 'signalementseps', 'action' => 'delete', $signalementep93['Signalementep93']['id'] ), array( 'enabled' => ( empty( $signalementep93['Passagecommissionep']['etatdossierep'] ) ), 'confirm' => 'Confirmer la suppression du signalement ?' ) );?></td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
<?php endif;?>

<?php if( !empty( $contratscomplexeseps93 ) ):?>
	<h2>Passages en EP pour contrats complexes</h2>
	<table class="tooltips">
		<thead>
			<tr>
				<th>Date début contrat</th>
				<th>Date fin contrat</th>
				<th>Date de création du dossier d'EP</th>
				<th>État dossier EP</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach( $contratscomplexeseps93 as $contratcomplexeep93 ):?>
			<?php
				$etatdossierep = Set::enum( $contratcomplexeep93['Passagecommissionep']['etatdossierep'], $optionsdossierseps['Passagecommissionep']['etatdossierep'] );
				if( empty( $etatdossierep ) ) {
					$etatdossierep = 'En attente';
				}
			?>
			<tr>
				<td><?php echo $this->Locale->date( 'Locale->date', $contratcomplexeep93['Contratinsertion']['dd_ci'] );?></td>
				<td><?php echo $this->Locale->date( 'Locale->date', $contratcomplexeep93['Contratinsertion']['df_ci'] );?></td>
				<td><?php echo $this->Locale->date( 'Locale->date', $contratcomplexeep93['Contratcomplexeep93']['created'] );?></td>
				<td><?php echo h( $etatdossierep );?></td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
<?php endif;?>

<?php
	echo $this->Default3->actions(
		array(
			"/Cers93/add/{$personne_id}" => array(
				'disabled' => false === WebrsaAccess::addIsEnabled( "/Cers93/add/{$personne_id}", $ajoutPossible )
			)
		)
	);
?>

<?php if( !empty( $cers93 ) && !empty( $erreursCandidatePassage ) ):?>
	<h2>Raisons pour lesquelles le contrat ne peut pas être signalé</h2>
	<div class="error_message">
		<?php if( count( $erreursCandidatePassage ) > 1 ):?>
		<ul>
			<?php foreach( $erreursCandidatePassage as $erreur ):?>
				<li><?php echo __d( 'relancenonrespectsanctionep93', "Erreur.{$erreur}" );?></li>
			<?php endforeach;?>
		</ul>
		<?php else:?>
			<p><?php echo __d( 'relancenonrespectsanctionep93', "Erreur.{$erreursCandidatePassage[0]}" );?></p>
		<?php endif;?>
	</div>
<?php endif;?>

<?php
	echo $this->Default3->index(
		$cers93,
		array(
			'Cer93.positioncer' => array(
				'label' => __d( 'cer93', 'Cer93.positioncer' )
			),
			'Cer93.formeci' => array(
				'label' => __d( 'cer93', 'Cer93.formeci' )
			),
			'Contratinsertion.dd_ci' => array(
				'label' => __d( 'contratinsertion', 'Contratinsertion.dd_ci' )
			),
			'Contratinsertion.df_ci' => array(
				'label' => __d( 'contratinsertion', 'Contratinsertion.df_ci' )
			),
			'Contratinsertion.rg_ci' => array(
				'label' => __d( 'contratinsertion', 'Contratinsertion.rg_ci' )
			),
			'Contratinsertion.decision_ci' => array(
				'label' => __d( 'contratinsertion', 'Contratinsertion.decision_ci' )
			),
			'Contratinsertion.datedecision' => array(
				'label' => __d( 'contratinsertion', 'Contratinsertion.datedecision' )
			),
			'Fichiermodule.nb_fichiers_lies' => array(
				'label' => 'Nb fichiers liés', 'type' => 'text'
			)
		)
		+ WebrsaAccess::links(
			array(
				'/Cers93/view/#Contratinsertion.id#' => array(
					'class' => 'button'
				),
				'/Cers93/edit/#Contratinsertion.id#' => array(
					'condition' => '"#Cer93.positioncer#" == "00enregistre"',
					'conditionGroup' => 'edit',
					'class' => 'button'
				),
				'/Cers93/edit_apres_signature/#Contratinsertion.id#' => array(
					'condition' => '"#Cer93.positioncer#" != "00enregistre"',
					'conditionGroup' => 'edit',
					'class' => 'button edit'
				),
				'/Cers93/signature/#Contratinsertion.id#' => array(
					'class' => 'button signature'
				),
				'/Histoschoixcers93/attdecisioncpdv/#Contratinsertion.id#' => array(
					'class' => 'button attdecisioncpdv'
				),
				'/Histoschoixcers93/attdecisioncg/#Contratinsertion.id#' => array(
					'class' => 'button attdecisioncg'
				),
				'/Histoschoixcers93/premierelecture/#Contratinsertion.id#' => array(
					'class' => 'button premierelecture'
				),
				'/Histoschoixcers93/secondelecture/#Contratinsertion.id#' => array(
					'class' => 'button secondelecture'
				),
				'/Histoschoixcers93/aviscadre/#Contratinsertion.id#' => array(
					'class' => 'button aviscadre'
				),
				'/Cers93/impression/#Contratinsertion.id#' => array(
					'class' => 'button'
				),
				'/Cers93/impressionDecision/#Contratinsertion.id#' => array(
					'class' => 'button impression'
				),
				'/Cers93/delete/#Contratinsertion.id#' => array(
					'class' => 'button'
				),
				'/Cers93/cancel/#Contratinsertion.id#' => array(
					'class' => 'button'
				),
				'/Signalementseps/add/#Contratinsertion.id#' => array(
					'class' => 'button signalementseps add'
				),
				'/Contratsinsertion/filelink/#Contratinsertion.id#' => array(
					'class' => 'button filelink'
				),
			)
		),
		array(
			'paginate' => false,
			'options' => $options,
			'id' => 'Cers93Index'
		)
	);
?>