<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'popup' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<script type="text/javascript">
	function impressionCohorte( link ) {
		$( 'progressBarContainer' ).show();
	}
</script>

<!-- Partie nécessaire pour l'affichage du popup lors du lancement des impressions en cohorte -->
<div id="progressBarContainer" style="display: none;">
	<div id="popups2" style="z-index: 1000;">
		<div id="popup_1">
			<div class="hideshow">
				<div class="fade" style="z-index: 31"></div>
				<div class="popup_block">
					<div class="popup">
						<div id="popup-content1">Fichier en cours de génération... <br /> Une fois terminée, veuillez cliquer sur le bouton "Recharger la page".<div style="text-align:center;"><input type="button" value="Recharger la page"></div></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$$('#popup-content1 input[type="button"]').first().observe('click', function(event) {
		event.stopPropagation();
		
		if (confirm('ATTENTION si vous cliquer OUI, le fichier en cours de génération sera perdu')) {
			location.reload();
		}
	});
</script>