<?php  $this->pageTitle = 'Avis '.__d('default'.Configure::read('Cg.departement'), 'du Président du Conseil Départemental').' sur les droits RSA';?>

<h1><?php echo $this->pageTitle;?></h1>

<?php if( empty( $avispcgdroitrsa ) ):?>
	<p class="notice">Ce dossier ne possède pas encore d'avis.</p>

<?php else:?>
	<table class="aere">
		<thead>
			<tr>
				<th>Avis  </th>
				<th>Date avis  </th>
				<th>Nom agent </th>
				<th>Type personne</th>
			</tr>
		</thead>
		<tbody>
			<?php
				echo $this->Xhtml->tableCells(
					array(
						h( ( trim( Set::extract( $avispcgdroitrsa, 'Avispcgdroitrsa.avisdestpairsa' ) )  != '' ) ? $avisdestpairsa[$avispcgdroitrsa['Avispcgdroitrsa']['avisdestpairsa']] : null ),
						h( $this->Locale->date( 'Date::short', $avispcgdroitrsa['Avispcgdroitrsa']['dtavisdestpairsa'] ) ),
						h( Set::classicExtract( $avispcgdroitrsa, 'Avispcgdroitrsa.nomtie' ) ),
						h( Set::enum( Set::classicExtract( 'Avispcgdroitrsa.typeperstie', $avispcgdroitrsa ), $typeperstie ) )
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
			?>
		</tbody>
	</table>

	<h2>Condition administrative</h2>
	<table class="aere">
		<thead>
			<tr>
				<th>Avis  </th>
				<th>Avis cond admin  </th>
				<th>Motif </th>
				<th>Commentaire 1</th>
				<th>Commentaire 2</th>
				<th>Date</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $avispcgdroitrsa['Condadmin'] as $condadmin ):?>
				<?php
					echo $this->Xhtml->tableCells(
						array(
							h( ( trim( Set::extract( $avispcgdroitrsa, 'Avispcgdroitrsa.avisdestpairsa' ) ) != '' ) ?$avisdestpairsa[$avispcgdroitrsa['Avispcgdroitrsa']['avisdestpairsa']] : null ),
							h( $aviscondadmrsa[$condadmin['aviscondadmrsa']]),
							h( $condadmin['moticondadmrsa']),
							h( $condadmin['comm1condadmrsa']),
							h( ( Set::extract( $condadmin, 'Condadmin.comm2condadmrsa' ) != '' ) ? $condadmin['comm2condadmrsa'] : null ),
							h( date_short( $condadmin['dteffaviscondadmrsa'] ) ),
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				?>
			<?php endforeach;?>
		</tbody>
	</table>
	<h2>Réduction RSA</h2>
	<?php if ( empty( $avispcgdroitrsa['Reducrsa'] ) ):?>
		<p>Pas de réduction RSA.</p>
	<?php else:?>
		<table class="aere">
			<thead>
				<tr>
					<th>Montant de la réduction</th>
					<th>Date de début</th>
					<th>Date de fin</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $avispcgdroitrsa['Reducrsa'] as $reducrsa ):?>
					<?php
						echo $this->Xhtml->tableCells(
							array(
								h( $reducrsa['mtredrsa']),
								h( $reducrsa['ddredrsa']),
								h( $reducrsa['dfredrsa']),
							),
							array( 'class' => 'odd' ),
							array( 'class' => 'even' )
						);
					?>
				<?php endforeach;?>
			</tbody>
		</table>
	<?php endif;?>
<?php endif;?>