<?php
	require_once  dirname( __FILE__ ).DS.'search.ctp' ;
	$index = 0;
	$annee = Hash::get( $this->request->data, 'Search.annee' );
?>
<?php if( isset( $results ) ): ?>
    <table class="tableaud1">
        <thead>
            <tr class="main">
                <th rowspan="2" colspan="2"></th>
                <th>Nombre de participants prévisionnel</th>
                <th colspan="3">Report des participants de l'année précédente, le cas échéant</th>
                <th colspan="6" id="entrees">Entrées enregistrées, au titre de la période d'exécution considérée</th>
                <th colspan="3">Sorties enregistrées, au titre de la période d'exécution considérée</th>
                <th colspan="3">Nombre de participants à l'action au 31/12/<?php echo $annee;?></th>
            </tr>
            <tr class="main">
                <th>Total</th>
                <?php for( $i = 0 ; $i < 4 ; $i++ ):?>
					<th>Total</th>
					<?php if($i == 1) echo "<th>% Total</th>"; ?>
					<th>Hommes</th>
					<?php if($i == 1) echo "<th>% Hommes</th>"; ?>
					<th>Femmes</th>
					<?php if($i == 1) echo "<th>% Femmes</th>"; ?>
                <?php endfor;?>
            </tr>
        </thead>
        <tbody>
			<?php foreach( $results as $categorie1 => $data1 ):?>
				<tr class="category">
					<th colspan="2"><?php echo __d( 'tableauxsuivispdvs93', "/Tableauxsuivispdvs93/tableaud1/{$categorie1}" );?></th>
					<?php foreach( $columns as $column ):?>
						<td class="number">
							<?php
								$number = $data1[$column];
								if( is_null( $number ) ) {
									echo 'N/C';
								}
								else {
									echo $this->Locale->number( $number );
								}
							?>
						</td>
					<?php endforeach;?>
				</tr>
				<?php if( isset( $data1['dont'] ) ):?>
					<?php $i = 0;?>
					<?php foreach( $data1['dont'] as $categorie2 => $data2 ):?>
						<tr>
							<?php if( $i === 0 ):?>
								<th class="colonne_dont" rowspan="<?php echo count( $data1['dont'] );?>">dont</th>
							<?php endif;?>
							<th><?php echo $categories[$categorie1][$categorie2];?></th>
							<?php foreach( $columns as $column ):?>
								<td class="number">
									<?php
										$number = $data2[$column];
										if( is_null( $number ) ) {
											echo 'N/C';
										}
										else {
											echo $this->Locale->number( $number );
										}
									?>
								</td>
							<?php endforeach;?>
						</tr>
						<?php $i++;?>
					<?php endforeach;?>
				<?php endif;?>
			<?php endforeach;?>
		</tbody>
	</table>
	<?php include_once  dirname( __FILE__ ).DS.'footer.ctp' ;?>
<?php endif;?>