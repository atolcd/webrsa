<?php  $this->pageTitle = 'Informations ETI de la personne';?>

<h1><?php echo $this->pageTitle;?></h1>

<?php if( empty( $informationeti ) ):?>
	<p class="notice">Cette personne ne possède pas encore d'informations ETI.</p>
<?php else:?>
		<table class="tooltips">
			<thead>
				<tr>
					<th>Top créateur d'entreprise</th>
					<th>Bénéficiaire ACCRE</th>
					<th>Activité eti</th>
					<th colspan="3">Employés</th>
					<th>Date de début du chiffre d'affaire ETI</th>
					<th>Date de fin du chiffre d'affaire ETI</th>
					<th>Montant du chiffre d'affaire </th>
					<th>Régime fiscal ETI</th>
					<th>Top bénéfice ETI</th>
					<th>Régime fiscal ETI de l'année précédente</th>
					<th>Montant des bénéfices de l'année précédente</th>
					<th>Montant des amortissements</th>
					<th>Montant des plus values professionnelles</th>
					<th>Top évolutions des revenus</th>
					<th>Libellé évolution des revenus</th>
					<th>Top ressources à évaluer</th>
				</tr>
			</thead>
			<tbody>
				<?php
					echo $this->Xhtml->tableCells(
						array(
							h( $topcreaentre[$informationeti['Informationeti']['topcreaentre']]),
							h( $topaccre[$informationeti['Informationeti']['topaccre']]),
							h( $acteti[$informationeti['Informationeti']['acteti']]),
							h( $topempl1ax[$informationeti['Informationeti']['topempl1ax']] ) ,
							h( $topstag1ax[$informationeti['Informationeti']['topstag1ax']] ) ,
							h( $topsansempl[$informationeti['Informationeti']['topsansempl']] ) ,
							h( $informationeti['Informationeti']['ddchiaffaeti'] ) ,
							h( $informationeti['Informationeti']['dfchiaffaeti'] ) ,
							h( $informationeti['Informationeti']['mtchiaffaeti'] ) ,
							h( $regfiseti[$informationeti['Informationeti']['regfiseti']] ) ,
							h( $topbeneti[$informationeti['Informationeti']['topbeneti']] ) ,
							h( $regfisetia1[$informationeti['Informationeti']['regfisetia1']] ) ,
							h( $informationeti['Informationeti']['mtbenetia1'] ) ,
							h( $informationeti['Informationeti']['mtamoeti'] ) ,
							h( $informationeti['Informationeti']['mtplusvalueti'] ) ,
							h( $topevoreveti[$informationeti['Informationeti']['topevoreveti']] ) ,
							h( $informationeti['Informationeti']['libevoreveti'] ) ,
							h( $topressevaeti[$informationeti['Informationeti']['topressevaeti']] ),
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				?>
			</tbody>
		</table>
<?php endif;?>