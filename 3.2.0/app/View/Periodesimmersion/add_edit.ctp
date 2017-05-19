<?php
	$domain = 'periodeimmersion';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'periodeimmersion', "Periodesimmersion::{$this->action}" )
	);

	echo $this->Xform->create( 'Periodeimmersion', array( 'id' => 'periodeimmersionform' ) );
	if( Set::check( $this->request->data, 'Periodeimmersion.id' ) ) {
		echo '<div>'.$this->Xform->input( 'Periodeimmersion.id', array( 'type' => 'hidden' ) ).'</div>';
	}
?>

<fieldset>
	<legend>LE CONTRAT CONCERNÉ</legend>
	<?php
		echo $this->Default->view(
			$cui,
			array(
				'Cui.secteur' => array( 'options' => $options['secteur'] ),
				'Cui.convention' => array( 'options' => $options['convention'] ),
			),
			array(
				'widget' => 'table',
				'id' => 'infosCui',
				'options' => $options
			)
		);
	?>
</fieldset>

<fieldset>
	<legend>L'EMPLOYEUR</legend>
	<?php
		echo $this->Default->view(
			$cui,
			array(
				'Cui.nomemployeur',
				'Cui.numvoieemployeur',
				'Cui.typevoieemployeur' => array( 'options' => $options['typevoie'] ),
				'Cui.nomvoieemployeur',
				'Cui.compladremployeur',
				'Cui.codepostalemployeur',
				'Cui.villeemployeur',
				'Cui.numtelemployeur',
				'Cui.emailemployeur',
				'Cui.siret',
				'Cui.atelierchantier',
				'Cui.numannexefinanciere'
			),
			array(
				'widget' => 'table',
				'id' => 'infosCui',
				'options' => $options
			)
		);
	?>
</fieldset>

<fieldset>
	<legend>LE SALARIÉ</legend>
	<table class="wide noborder">
		<tr>
			<td class="mediumSize noborder">
				<strong>Nom : </strong><?php echo Set::enum( Set::classicExtract( $personne, 'Personne.qual'), $qual ).' '.Set::classicExtract( $personne, 'Personne.nom' );?>
				<br />
				<?php if(  Set::classicExtract( $personne, 'Personne.qual') != 'MR' ):?>
					<strong>Pour les femmes, nom patronymique : </strong><?php echo Set::classicExtract( $personne, 'Personne.nomnai' );?>
				<?php endif;?>
				<br />
				<strong>Né(e) le : </strong>
					<?php
						echo date_short( Set::classicExtract( $personne, 'Personne.dtnai' ) ).' <strong>à</strong>  '.Set::classicExtract( $personne, 'Personne.nomcomnai' );
					?>
				<br />
				<strong>Adresse : </strong><br />
					<?php
						echo Set::extract( $personne, 'Adresse.numvoie' ).' '.Set::extract( $personne, 'Adresse.libtypevoie' ).' '.Set::extract( $personne, 'Adresse.nomvoie' ).'<br /> '.Set::extract( $personne, 'Adresse.compladr' ).'<br /> '.Set::extract( $personne, 'Adresse.codepos' ).' '.Set::extract( $personne, 'Adresse.nomcom' );
					?>
				<br />
				<!-- Si on n'autorise pas la diffusion de l'email, on n'affiche rien -->
				<?php if( Set::extract( $personne, 'Foyer.Modecontact.0.autorutiadrelec' ) == 'A' ):?>
					<strong>Adresse électronique : </strong><?php echo Set::extract( $personne, 'Foyer.Modecontact.0.adrelec' );?>
				<?php endif;?>
			</td>
			<td class="mediumSize noborder">
				<strong>Prénoms : </strong><?php echo Set::classicExtract( $personne, 'Personne.prenom' );?>
				<br />
				<strong>NIR : </strong><?php echo Set::classicExtract( $personne, 'Personne.nir');?>
				<br />
				<strong>Si bénéficiaire RSA, n° allocataire : </strong>
				<?php
					echo Set::classicExtract( $personne, 'Foyer.Dossier.matricule' ).'  <strong><br />relève de : </strong> '.Set::classicExtract( $personne, 'Foyer.Dossier.fonorg' );
				?>
				<br />
				<!-- Si on n'autorise aps la diffusion du téléphone, on n'affiche rien -->
				<?php if( Set::extract( $personne, 'Foyer.Modecontact.0.autorutitel' ) == 'A' ):?>
					<strong>Numéro de téléphone 1 : </strong><?php echo Set::extract( $personne, 'Foyer.Modecontact.0.numtel' );?>
					<br />
					<strong>Numéro de téléphone 2 : </strong><?php echo Set::extract( $personne, 'Foyer.Modecontact.1.numtel' );?>
				<?php endif;?>
			</td>
		</tr>
	</table>
</fieldset>

<fieldset id="periodeimmersion" class="invisible">
	<?php
		echo $this->Default->subform(
			array(
				'Periodeimmersion.cui_id' => array( 'value' => $cui_id, 'type' => 'hidden' )
			),
			array(
				'domain' => $domain,
				'options' => $options
			)
		);
	?>

	<fieldset>
		<legend>L'ENTREPRISE D'ACCUEIL</legend>
		<?php
			echo $this->Default->subform(
				array(
					'Periodeimmersion.nomentaccueil',
					'Periodeimmersion.numvoieentaccueil',
					'Periodeimmersion.typevoieentaccueil' => array( 'options' => $options['typevoie'] ),
					'Periodeimmersion.nomvoieentaccueil',
					'Periodeimmersion.compladrentaccueil',
					'Periodeimmersion.codepostalentaccueil',
					'Periodeimmersion.villeentaccueil',
					'Periodeimmersion.numtelentaccueil',
					'Periodeimmersion.emailentaccueil',
					'Periodeimmersion.activiteentaccueil',
					'Periodeimmersion.siretentaccueil'
				),
				array(
					'options' => $options
				)
			);
		?>
	</fieldset>
	<fieldset>
		<legend>PÉRIODE D'IMMERSION</legend>
		<?php
			echo $this->Default->subform(
				array(
					'Periodeimmersion.datedebperiode' => array( 'dateFormat' => 'DMY', 'minYear' => date('Y')-2, 'maxYear' => date('Y')+2, 'empty' => false ),
					'Periodeimmersion.datefinperiode' => array( 'dateFormat' => 'DMY', 'minYear' => date('Y')-2, 'maxYear' => date('Y')+2, 'empty' => false )
				),
				array(
					'options' => $options
				)
			);
		?>
		<table class="periodeimmersion wide aere noborder">
			<tr>
				<td class="noborder mediumSize">Soit un nombre de jours èquivalent à </td>
				<td class="noborder mediumSize" id="PeriodeimmersionNbjourperiode"></td>
			</tr>
		</table>
		<?php
			echo $this->Default->subform(
				array(
					'Periodeimmersion.codeposteaffectation',
					'Periodeimmersion.objectifimmersion' => array( 'type' => 'radio', 'separator' => '<br />', 'options' => $options['objectifimmersion'] ),
					'Periodeimmersion.datesignatureimmersion' => array( 'dateFormat' => 'DMY', 'minYear' => date('Y')-2, 'maxYear' => date('Y')+2, 'empty' => false )
				),
				array(
					'options' => $options
				)
			);
		?>
	</fieldset>
</fieldset>

<script type="text/javascript" >
function calculNbDays() {
	var Datedebperiode = $F( 'PeriodeimmersionDatedebperiodeDay' );
	var Datefinperiode = $F( 'PeriodeimmersionDatefinperiodeDay' );
	$( 'PeriodeimmersionNbjourperiode' ).update( ( Datefinperiode - Datedebperiode ) );
}

$( 'PeriodeimmersionDatefinperiodeDay' ).observe( 'blur', function( event ) { calculNbDays(); } );
</script>

<div class="submit">
	<?php
		echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
		echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
	?>
</div>
<?php echo $this->Xform->end();?>
