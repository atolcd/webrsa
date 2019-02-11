<?php
	$this->pageTitle = 'Rendez-vous';

	$departement = Configure::read( 'Cg.departement' );
?>

<h1><?php echo 'Rendez-vous';?></h1>

<div id="ficheCI">
	<table>
		<tbody>
			<tr class="even">
				<th><?php echo $departement == 93 ? 'Structure proposant le RDV' : __d( 'structurereferente', 'Structurereferente.lib_struc' );?></th>
				<td><?php echo Set::classicExtract( $rendezvous, 'Structurereferente.lib_struc' );?></td>
			</tr>
			<tr class="odd">
				<th><?php echo $departement == 93 ? 'Personne proposant le RDV' : __( 'Référent' );?></th>
				<td><?php echo Set::classicExtract( $rendezvous, 'Referent.nom_complet' );?></td>
			</tr>
			<tr class="even">
				<th><?php echo __( 'Fonction du référent' );?></th>
				<td><?php echo Set::classicExtract( $rendezvous, 'Referent.fonction' );?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __( 'Permanence liée à la structure' );?></th>
				<td><?php echo Set::classicExtract( $rendezvous, 'Permanence.libpermanence' );?></td>
			</tr>
			<tr class="even">
				<th><?php echo __( 'Type de RDV' );?></th>
				<td><?php echo Set::classicExtract( $rendezvous, 'Typerdv.libelle' );?></td>
			</tr>
			<?php $thematiques = Hash::extract( $rendezvous, 'Thematiquerdv.{n}.name' );?>
			<?php if( !empty( $thematiques ) ) :?>
			<tr class="odd">
				<th><?php echo __d( 'rendezvous', 'Thematiquerdv.name' );?></th>
				<td><ul><?php
					foreach( $thematiques as $thematique ) {
						echo $this->Xhtml->tag( 'li', $thematique );
					}
				?></ul></td>
			</tr>
			<?php endif;?>
			<tr class="odd">
				<th><?php echo __d( 'rendezvous', 'Rendezvous.statutrdv' );?></th>
				<td><?php echo Set::classicExtract( $rendezvous, 'Statutrdv.libelle' );?></td>
			</tr>
			<tr class="even">
				<th><?php echo __d( 'rendezvous', 'Rendezvous.daterdv' );?></th>
				<td><?php echo date_short( Set::classicExtract( $rendezvous, 'Rendezvous.daterdv' ) );?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __d( 'rendezvous', 'Rendezvous.heurerdv' );?></th>
				<td><?php echo Set::classicExtract( $rendezvous, 'Rendezvous.heurerdv' );?></td>
			</tr>
			<tr class="even">
				<th><?php echo __d( 'rendezvous', 'Rendezvous.objetrdv' );?></th>
				<td><?php echo Set::classicExtract( $rendezvous, 'Rendezvous.objetrdv' );?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __d( 'rendezvous', 'Rendezvous.commentairerdv' );?></th>
				<td><?php echo Set::classicExtract( $rendezvous, 'Rendezvous.commentairerdv' );?></td>
			</tr>
		</tbody>
	</table>
</div>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'rendezvous',
			'action'     => 'index',
			$personne_id
		),
		array(
			'id' => 'Back'
		)
	);
?>