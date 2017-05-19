<?php $this->pageTitle = 'Informations financières';?>

<h1><?php echo $this->pageTitle;?></h1>

<?php if( empty( $infosfinancieres ) ):?>
	<p class="notice">Cette personne ne possède pas encore d'informations financières.</p>
<?php else:?>
<fieldset>
<table>
	<tbody>
		<tr>
			<th>Nom / Prénom</th>
			<td> <?php echo $personne['Personne']['qual'].' '.$personne['Personne']['nom'].' '.$personne['Personne']['prenom'];?> </td>
		</tr>
		<tr>
			<th>NIR</th>
			<td> <?php echo $personne['Personne']['nir'];?> </td>
		</tr>
		<tr>
			<th>Date de naissance</th>
			<td> <?php echo  date_short( $personne['Personne']['dtnai'] );?> </td>
		</tr>
		<tr>
			<th>N° CAF</th>
			<td> <?php echo  $infosfinancieres[0]['Dossier']['matricule'];?> </td> <!-- FIXME: Voir si possibilité changer ces 0 -->
		</tr>
	</tbody>
</table>
</fieldset>
    <?php
        $pagination = $this->Xpaginator2->paginationBlock( 'Infofinanciere', $this->passedArgs );
        echo $pagination;
    ?>
	<table id="searchResults" class="tooltips">
		<thead>
			<tr>
				<th>Mois des mouvements</th>
				<th>Type d'allocation</th>
				<th>Nature de la prestation pour la créance</th>
				<th>Montant</th>
				<th class="action">Action</th>
				<th class="innerTableHeader">Informations complémentaires</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $infosfinancieres as $index => $indu ):?>
				<?php
					echo $this->Xhtml->tableCells(
						array(
							h( $this->Locale->date( 'Date::miniLettre', $indu['Infofinanciere']['moismoucompta'] ) ),
							h( $type_allocation[$indu['Infofinanciere']['type_allocation']] ),
							h( $natpfcre[$indu['Infofinanciere']['natpfcre']] ),
							h(  $this->Locale->money( $indu['Infofinanciere']['mtmoucompta'] ) ),
							$this->Xhtml->viewLink(
								'Voir l\'indu',
								array( 'controller' => 'infosfinancieres', 'action' => 'view', $indu['Infofinanciere']['id'] )
							),
						),
						array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
						array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
					);
				?>
			<?php endforeach;?>
		</tbody>
	</table>
    <?php echo $pagination;?>
<?php endif?>