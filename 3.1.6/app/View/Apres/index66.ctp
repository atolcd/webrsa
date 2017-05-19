<?php
	$this->pageTitle = sprintf('APRE/ADREs liées à %s', $personne['Personne']['nom_complet']);
	$this->modelClass = Inflector::classify($this->request->params['controller']);
	
	$urlController = 'apres'.Configure::read('Apre.suffixe');
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);
?>
<h1><?php echo $this->pageTitle;?></h1>
<?php echo $this->element('ancien_dossier');?>

		<?php if(empty($apres)):?>
			<p class="notice">Cette personne ne possède pas encore d'APRE/ADRE.</p>
		<?php endif;?>
		<?php if($this->Permissions->checkDossier($urlController, 'add', $dossierMenu)):?>
			<ul class="actionMenu">
				<?php
					echo '<li>'.$this->Xhtml->addLink(
						'Ajouter APRE/ADRE',
						array('controller' => $urlController, 'action' => 'add', $personne_id),
						WebrsaAccess::addIsEnabled("/$urlController/add", $ajoutPossible)
					).' </li>';
				?>
			</ul>
		<?php endif;?>

<?php if(!empty($apres)):?>
<?php
	echo 'Montant accordé à ce jour : '.$apresPourCalculMontant.' €';
	if($alerteMontantAides) {
		echo $this->Xhtml->tag(
			'p',
			$this->Xhtml->image('icons/error.png', array('alt' => 'Remarque')).' '.sprintf('Cette personne risque de bénéficier de plus de %s € d\'aides sur l\'année en cours', Configure::read('Apre.montantMaxComplementaires')),
			array('class' => 'error')
		);
	}
?>

<table class="tooltips default2">
	<thead>
		<tr>
			<th>Date demande APRE/ADRE</th>
			<th>Etat du dossier</th>
			<th>Thème de l'aide</th>
			<th>Type d'aides</th>
			<th>Montant proposé</th>
			<th>Montant accordé</th>
			<th>Décision</th>
			<th colspan="7" class="action">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach($apres as $index => $apre) {
				$nbFichiersLies = Hash::get($apre, 'Fichiermodule.nombre');

				$statutApre = Hash::get($apre, "{$this->modelClass}.statutapre");

				$mtforfait = Hash::get($apre, 'Aideapre66.montantpropose');
				$mtattribue = Hash::get($apre, 'Aideapre66.montantaccorde');

				$etat = Set::enum(Hash::get($apre, "{$this->modelClass}.etatdossierapre"), $options['etatdossierapre']);

				$innerTable = '<table id="innerTable'.$index.'" class="innerTable">
					<tbody>
						<tr>
							<th>N° APRE/ADRE</th>
							<td>'.h(Hash::get($apre, "{$this->modelClass}.numeroapre")).'</td>
						</tr>
						<tr>
							<th>Nom/Prénom Allocataire</th>
							<td>'.h($apre['Personne']['nom'].' '.$apre['Personne']['prenom']).'</td>
						</tr>
						<tr>
							<th>Référent APRE/ADRE</th>
							<td>'.h(Set::enum(Hash::get($apre, "{$this->modelClass}.referent_id"), $referents)).'</td>
						</tr>
						<tr>
							<th>Natures de la demande</th>
							<td>'.(empty($aidesApre) ? null :'<ul><li>'.implode('</li><li>', $aidesApre).'</li></ul>').'</td>
						</tr>
						<tr>
							<th>Raison annulation</th>
							<td>'.h($apre['Apre66']['motifannulation']).'</td>
						</tr>
					</tbody>
				</table>';

				echo $this->Xhtml->tableCells(
					array(
						h(date_short(Hash::get($apre, 'Aideapre66.datedemande'))),
						h($etat),
						h(Set::enum(Hash::get($apre, 'Aideapre66.themeapre66_id'), $themes )),
						h(Set::enum(Hash::get($apre, 'Aideapre66.typeaideapre66_id'), $nomsTypeaide )),
						h($this->Locale->money($mtforfait)),
						h($this->Locale->money($mtattribue)),
						h(Set::enum(Hash::get($apre, 'Aideapre66.decisionapre'), $options['decisionapre'])),
						$this->Xhtml->viewLink(
							'Voir la demande APRE/ADRE',
							array(
								'controller' => $urlController,
								'action' => 'view'.Configure::read('Cg.departement'),
								$apre[$this->modelClass]['id']
							),
							WebrsaAccess::isEnabled($apre, "/$urlController/view66")
						),
						$this->Xhtml->editLink(
							'Editer la demande APRE/ADRE',
							array('controller' => $urlController, 'action' => 'edit', $apre[$this->modelClass]['id']),
							WebrsaAccess::isEnabled($apre, "/$urlController/edit")
						),
						$this->Xhtml->printLink(
							'Imprimer la demande APRE/ADRE',
							array('controller' => 'apres66', 'action' => 'impression', $apre[$this->modelClass]['id']),
							WebrsaAccess::isEnabled($apre, "/$urlController/impression")
						),
						$this->Default2->button(
							'email',
							array('controller' => $urlController, 'action' => 'maillink', $apre[$this->modelClass]['id']),
							array(
								'label' => 'Envoi mail référent',
								'title' => 'Envoi Mail',
								'enabled' => WebrsaAccess::isEnabled($apre, "/$urlController/maillink")
							)
						),
						$this->Default2->button(
							'filelink',
							array('controller' => $urlController, 'action' => 'filelink', $apre[$this->modelClass]['id']),
							array(
								'label' => 'Fichiers liés ('.$nbFichiersLies.')',
								'title' => 'Fichiers liés ('.$nbFichiersLies.')',
								'enabled' => WebrsaAccess::isEnabled($apre, "/$urlController/filelink")
							)
						),
						$this->Default2->button(
							'cancel',
							array('controller' => 'apres66', 'action' => 'cancel', $apre[$this->modelClass]['id']),
							array('enabled' => WebrsaAccess::isEnabled($apre, "/$urlController/cancel"))
						),
						array($innerTable, array('class' => 'innerTableCell'))
					),
					array('class' => 'odd', 'id' => 'innerTableTrigger'.$index),
					array('class' => 'even', 'id' => 'innerTableTrigger'.$index)
				);
			}
		?>
	</tbody>
</table>
<?php endif;?>
