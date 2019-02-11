<?php  $this->pageTitle = 'Suivis d\'instruction du dossier';?>

<h1><?php echo $this->pageTitle;?></h1>

<?php if( empty( $suivisinstruction ) ):?>
	<p class="notice">Ce dossier ne possède pas encore de suivis d'instruction.</p>
<?php endif;?>

<?php if( !empty( $suivisinstruction ) ):?>
	<table class="tooltips">
		<thead>
			<tr>
				<th>Etat du dossier </th>
				<th>Date</th>
				<th>Nom agent</th>
				<th>Numéro département</th>
				<th>Type de service</th>
				<th colspan="2" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $suivisinstruction as $suiviinstruction ):?>
				<?php
					$title = implode( ' ', array(
						$suiviinstruction['Suiviinstruction']['suiirsa'] ,
						$suiviinstruction['Suiviinstruction']['date_etat_instruction'],
						$suiviinstruction['Suiviinstruction']['nomins'] ,
						$suiviinstruction['Suiviinstruction']['prenomins'] ,
						$suiviinstruction['Suiviinstruction']['numdepins'] ,
						$suiviinstruction['Suiviinstruction']['typeserins'] ,
						$suiviinstruction['Suiviinstruction']['numcomins'] ,
						$suiviinstruction['Suiviinstruction']['numagrins']
					));

					echo $this->Xhtml->tableCells(
						array(
							h( $suiirsa[$suiviinstruction['Suiviinstruction']['suiirsa']]),
							h( date_short($suiviinstruction['Suiviinstruction']['date_etat_instruction'] ) ) ,
							h( $suiviinstruction['Suiviinstruction']['nomins']),
							h( $suiviinstruction['Suiviinstruction']['numdepins']),
							h( isset( $typeserins[$suiviinstruction['Suiviinstruction']['typeserins']] ) ? $typeserins[$suiviinstruction['Suiviinstruction']['typeserins']] : null ),
							$this->Xhtml->viewLink(
								'Voir les informations financières',
								array( 'controller' => 'suivisinstruction', 'action' => 'view', $suiviinstruction['Suiviinstruction']['id']),
								$this->Permissions->checkDossier( 'suivisinstruction', 'view', $dossierMenu )
							)
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				?>
			<?php endforeach;?>
		</tbody>
	</table>
<?php  endif;?>