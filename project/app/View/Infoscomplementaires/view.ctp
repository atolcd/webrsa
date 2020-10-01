<?php $this->pageTitle = 'Dossier RSA '.$details['Dossier']['numdemrsa'];?>

<h1>Informations complémentaires</h1> <!--FIXME: grugeage -->
<br />
<div id="tabbedWrapper" class="tabs">
	<div id="allocataires">
		<h2 class="title">Personnes</h2>
		<table class="noborder">
			<tbody>
				<tr>
					<td class="noborder">
						<?php
							echo $this->Theme->tableDemandeurConjoint(
								$details,
								array(
									'Personne.qual' => array( 'options' => $qual ),
									'Personne.nom',
									'Personne.prenom'
								),
								array(
									'id' => 'personnes'
								)
							);
						?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="activites">
	<h2 class="title">Activité</h2>
	<table class="noborder">
		<tbody>
			<tr>
				<td class="noborder">
					<?php
						echo $this->Theme->tableDemandeurConjoint(
							$details,
							array(
								'Activite.reg' => array( 'options' => $reg ),
								'Activite.act' => array( 'options' => $act, 'label' => __d( 'activite', 'Activite.categoriepro' ) ),
								'Activite.paysact' => array( 'options' => $paysact ),
								'Activite.ddact',
								'Activite.dfact',
							)
						);
					?>
				</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="titres">
	<h2 class="title">Titre de séjour</h2>
	<table class="noborder">
		<tbody>
			<tr>
				<td class="noborder">
					<?php
						echo $this->Theme->tableDemandeurConjoint(
							$details,
							array(
								'Titresejour.dtentfra',
								'Titresejour.nattitsej',
								'Titresejour.menttitsej',
								'Titresejour.ddtitsej',
								'Titresejour.dftitsej',
								'Titresejour.numtitsej',
								'Titresejour.numduptitsej'
							)
						);
					?>
				</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="dossiercaf">
	<h2 class="title">Dossier CAF</h2>
	<table class="noborder">
		<tbody>
			<tr>
				<td class="noborder">
					<?php
						echo $this->Theme->tableDemandeurConjoint(
							$details,
							array(
								'Dossiercaf.ddratdos',
								'Dossiercaf.dfratdos',
								'Dossiercaf.toprespdos',
								'Dossiercaf.numdemrsaprece',
							)
						);
					?>
				</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="allocation">
	<h2 class="title">Allocation soutien familial</h2>
	<table class="noborder">
		<tbody>
			<tr>
				<td class="noborder">
					<?php
						echo $this->Theme->tableDemandeurConjoint(
							$details,
							array(
								'Allocationsoutienfamilial.sitasf' => array( 'options' => $sitasf ),
								'Allocationsoutienfamilial.parassoasf' => array( 'options' => $parassoasf ),
								'Allocationsoutienfamilial.ddasf',
								'Allocationsoutienfamilial.dfasf'
							)
						);
					?>
				</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="creances">
	<h2 class="title">Créances alimentaires</h2>
	<table class="noborder">
		<tbody>
			<tr>
				<td class="noborder" colspan="2">
					<?php
						echo $this->Theme->tableDemandeurConjoint(
							$details,
							array(
								'Creancealimentaire.etatcrealim' => array( 'options' => $etatcrealim ),
								'Creancealimentaire.ddcrealim',
								'Creancealimentaire.dfcrealim',
								'Creancealimentaire.orioblalim' => array( 'options' => $orioblalim ),
								'Creancealimentaire.motidiscrealim' => array( 'options' => $motidiscrealim ),
								'Creancealimentaire.commcrealim',
								'Creancealimentaire.mtsancrealim',
								'Creancealimentaire.topdemdisproccrealim' => array( 'options' => $topdemdisproccrealim ),
								'Creancealimentaire.engproccrealim' => array( 'options' => $engproccrealim ),
								'Creancealimentaire.verspa' => array( 'options' => $verspa ),
								'Creancealimentaire.topjugpa'
							),
							array(
								'id' => 'creancesalimentaires'
							)
						);
					?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
</div>

<!-- *********************************************************************** -->

<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
	}
?>

<script type="text/javascript">
	makeTabbed( 'tabbedWrapper', 2 );
</script>