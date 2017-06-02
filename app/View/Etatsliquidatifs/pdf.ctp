<h1><?php echo $this->pageTitle = 'Etat de liquidation';?></h1>

<?php
	$etatliquidatif['Etatliquidatif']['tranche'] = ( ( $etatliquidatif['Etatliquidatif']['typeapre'] == 'forfaitaire' ) ? 'T01' : 'T02' );
	$etatliquidatif['Etatliquidatif']['objet'] = $etatliquidatif['Etatliquidatif']['lib_programme'].' '.date( 'm/Y', strtotime( $etatliquidatif['Etatliquidatif']['datecloture'] ) );
	$etatliquidatif['Etatliquidatif']['montanttotalapre'] = $this->Locale->money( $etatliquidatif['Etatliquidatif']['montanttotalapre'] );
?>

<table class="etatliquidatif header">
	<tr>
		<th>Entité financière:</th><td><?php echo $etatliquidatif['Etatliquidatif']['entitefi']?></td>
		<th>Opération:</th><td><?php echo $etatliquidatif['Etatliquidatif']['operation']?></td>
	</tr>
	<tr>
		<th>Exercice budgétaire:</th><td><?php echo $etatliquidatif['Budgetapre']['exercicebudgetai']?></td>
		<th>Nature analytique:</th><td><?php echo $etatliquidatif['Etatliquidatif']['lib_natureanalytique']?></td>
	</tr>
	<tr>
		<th>Cdr:</th><td><?php echo $etatliquidatif['Etatliquidatif']['libellecdr']?></td>
		<th>Objet:</th><td><?php echo $etatliquidatif['Etatliquidatif']['objet']?></td>
	</tr>
</table>

<?php
	function paiementfoyerComplet( $paiementfoyer ) {
		$keys = array_keys( Hash::filter( (array)$paiementfoyer ) );
		$requiredKeys = array( 'titurib', 'nomprenomtiturib', 'etaban', 'guiban', 'numcomptban', 'clerib' );
		return ( count( $keys ) == count( $requiredKeys ) );
	}

	function paiementorganismeComplet( $paiementorganisme ) {
		$filtered = Hash::filter( (array)$paiementorganisme );
		$requiredKeys = array( 'guiban', 'etaban', 'numcomptban', 'clerib' );
		foreach( $requiredKeys as $requiredKey ) {
			if( !isset( $filtered[$requiredKey] ) ) {
				return false;
			}
		}
		return true;
	}

	/// Vérification de données manquantes FIXME: déléguer dans le modèle ?
	$nbrAttentdu = count( Set::extract( $elements, '/Apre' ) );
	$nbrLibellesDomicialiation = count( Hash::filter( (array)Set::extract( $elements, '/Domiciliationbancaire/libelledomiciliation' ) ) );

	$nbrPaiementsFoyer = 0;
	foreach( Set::extract( $elements, '{n}.Paiementfoyer' ) as $paiementfoyer ) {
		$nbrPaiementsFoyer += ( paiementfoyerComplet( $paiementfoyer ) ? 1 : 0 );
	}

	$problems = array();
	if( $nbrLibellesDomicialiation != $nbrAttentdu ) {
		$nbrProblems = ( $nbrAttentdu - $nbrLibellesDomicialiation );
		$problems[] = sprintf( __n( '%s entrée sans libellé de domiciliation', '%s entrées sans libellé de domiciliation', $nbrProblems ), $nbrProblems );
	}
	if( $nbrPaiementsFoyer != $nbrAttentdu ) {
		$nbrProblems = ( $nbrAttentdu - $nbrPaiementsFoyer );
		$problems[] = sprintf( __n( '%s entrée dont les informations de paiement pour le foyer ne sont pas complètes', '%s entrées dont les informations de paiement pour le foyer ne sont pas complètes', $nbrProblems ), $nbrProblems );
	}
	if( !empty( $problems ) ) {
		echo '<ul><li>'.implode( '</li><li>', $problems ).'</li></ul>';
	}
?>

<table class="etatliquidatif apres">
	<thead>
		<tr>
			<th>Titre</th>
			<th>Nom Prénom</th>
			<th>Adresse</th>
			<th>C.P.</th>
			<th>Ville</th>
			<th>Versé à</th>
			<th>Banque</th>
			<th>Guichet</th>
			<th>Compte</th>
			<th>RIB</th>
			<th>Allocation</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach( $elements as $element ):?>
			<?php
				$adresse = null;
				$trClass = null;
				// Le destinataire est-il l'allocataire (true) ou l'organisme de formation (false)
				$destinataireAllocataire = !( $etatliquidatif['Etatliquidatif']['typeapre'] == 'complementaire' && !empty( $element['Tiersprestataireapre']['guiban'] ) );

				// Formattage élément
				$adresse = implode(
					' ',
					array(
						Set::classicExtract( $element, 'Adresse.numvoie' ),
						mb_convert_case( Set::classicExtract( $element, 'Adresse.libtypevoie' ), MB_CASE_UPPER, Configure::read( 'App.encoding' ) ),
						Set::classicExtract( $element, 'Adresse.nomvoie' ),
						Set::classicExtract( $element, 'Adresse.complideadr' ),
						Set::classicExtract( $element, 'Adresse.compladr' ),
					)
				);


				if( $destinataireAllocataire ) {
					/// Vérification de données manquantes FIXME: déléguer dans le modèle ?
					$libelledomiciliation = Set::classicExtract( $element, 'Domiciliationbancaire.libelledomiciliation' );
					if( empty( $libelledomiciliation ) || !paiementfoyerComplet( Set::extract( $element, 'Paiementfoyer' ) ) ) {
						$trClass = 'error';
					}
				}
				else {
					/// Vérification de données manquantes FIXME: déléguer dans le modèle ?
					if( !paiementorganismeComplet( Set::extract( $element, 'Tiersprestataireapre' ) ) ) {
						$trClass = 'error';
					}
				}
			?>
			<tr<?php if( !empty( $trClass ) ) echo ' class="'.$trClass.'" style="color: red;"';?>>
				<td><?php echo $element['Paiementfoyer']['titurib'];?></td>
				<td><?php echo $element['Paiementfoyer']['nomprenomtiturib'];?></td>
				<td><?php echo $adresse;?></td>
				<td><?php echo $element['Adresse']['codepos'];?></td>
				<td><?php echo $element['Adresse']['nomcom'];?></td>
				<?php if( $destinataireAllocataire ):?>
					<td>Allocataire</td>
					<td><?php echo $element['Paiementfoyer']['etaban'];?></td>
					<td><?php echo $element['Paiementfoyer']['guiban'];?></td>
					<td><?php echo $element['Paiementfoyer']['numcomptban'];?></td>
					<td><?php echo str_pad( $element['Paiementfoyer']['clerib'], 2, '0', STR_PAD_LEFT );?></td>
				<?php else:?>
					<td>Organisme: <?php echo $element['Tiersprestataireapre']['nomtiers'];?></td>
					<td><?php echo $element['Tiersprestataireapre']['etaban'];?></td>
					<td><?php echo $element['Tiersprestataireapre']['guiban'];?></td>
					<td><?php echo $element['Tiersprestataireapre']['numcomptban'];?></td>
					<td><?php echo str_pad( $element['Tiersprestataireapre']['clerib'], 2, '0', STR_PAD_LEFT );?></td>
				<?php endif;?>
				<td class="number"><?php echo str_replace( ' ', '&nbsp;', $this->Locale->money( $element['Apre']['allocation'] ) );?></td>
			</tr>
		<?php endforeach;?>
		<tr>
			<th colspan="8">Total</th>
			<td class="number" colspan="2"><?php echo str_replace( ' ', '&nbsp;', $this->Locale->money( array_sum( Set::extract( $elements, '/Apre/allocation' ) ) ) );?></td>
		</tr>
	</tbody>
</table>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'etatsliquidatifs',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>