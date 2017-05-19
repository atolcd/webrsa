<?php
	require_once  dirname( __FILE__ ).DS.'search.ctp' ;
	$annee = Hash::get( $this->request->data, 'Search.annee' );
?>
<?php if( isset( $results ) ): ?>
	<table class="tableaud2">
		<thead>
			<tr>
				<th colspan="3"></th>
				<th>Nombre de personnes</th>
				<th>En %</th>
				<th>Dont hommes</th>
				<th>En %</th>
				<th>Dont femmes</th>
				<th>En %</th>
				<th>Dont couvert par un CER = Objectif "SORTIE"</th>
				<th>En %</th>
			</tr>
		</thead>
		<tbody>
			<?php
				echo $this->Tableaud2->line1Categorie( 'totaux', $results );
				echo $this->Tableaud2->line1Categorie( 'maintien', $results );
				echo $this->Tableaud2->line3Categorie( 'sortie_obligation', $results, $categories );
				echo $this->Tableaud2->line1Categorie( 'abandon', $results );
				echo $this->Tableaud2->line1Categorie( 'reorientation', $results );
				echo $this->Tableaud2->line2Categorie( 'changement_situation', $results, $categories );
			?>
		</tbody>
	</table>

	<?php include_once  dirname( __FILE__ ).DS.'footer.ctp' ;?>
<?php endif;?>