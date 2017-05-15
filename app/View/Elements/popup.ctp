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
						<a href="#" onclick="$('progressBarContainer').hide(); return false;"><?php echo $this->Xhtml->image('icon_close.png', array('class' => 'cntrl', 'alt' => 'close')); ?></a>
						<div id="popup-content1">Edition en cours ... <br /> Une fois terminée, veuillez cliquer sur la croix rouge afin de fermer cette fenêtre.</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>