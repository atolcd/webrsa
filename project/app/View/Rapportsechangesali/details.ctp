<?php
	echo "<h1>".$titre."</h1>";

	//Affichage des erreurs du flux
	if(!empty($erreursglobales)){
		echo "<br/><br/>";
		echo "<h2>";
		echo __m("erreurs.titre");
		echo "</h2>";

		echo "<ul>";
		foreach($erreursglobales as $erreur){
			echo "<li>";
			if($erreur[0]['code'] == 'personne_inconnue'){
				echo $nb_personnes_inconnues.' '.__m('personnes_inconnues');
			} else {
				echo __m($erreur[0]['code']);
			}
			echo "</li>";
		}
		echo "</ul>";
	}

	if(isset($personnes)){

		$colonnes = [
			'Personne.qual',
			'Personne.nom',
			'Personne.prenom',
			'Personne.dtnai'
		];

		if($type == 'import'){
			$colonnes = array_merge(
				$colonnes,
				[
					'Erreur.referent',
					'Erreur.rdv',
					'Erreur.dsp',
					'Erreur.cer',
					'Erreur.orient',
					'Erreur.d1',
					'Erreur.d2',
					'Erreur.b7'
				]
			);

		}

		$colonnes = array_merge(
			$colonnes,
			[
				'/personnes/view/#Personne.id#' => array(
					'title' => false, 'target' => '_blank'
				)
			]
		);


		echo "<br/><br/>";
		echo "<h2>";
		echo __m("personnes.titre");
		echo "</h2>";
		echo "<h3>";
		echo sprintf(__d('rapportsechangesali', 'personnes.total'), $nbPersonnes);
		echo "</h3>";
		$pagination = $this->Xpaginator->paginationBlock( 'PersonneEchangeALI', $this->passedArgs );
		echo $pagination;

		echo $this->Default3->index(
			$personnes,
			$this->Translator->normalize(
				$colonnes
			),
			array(
				'paginate' => false,
				'id' => 'TableRapportsechangesaliDetails'
			)
		);

		echo $pagination;

	} else {
		echo "<br/><br/>";
		echo "<h3>";
		echo __m("aucun_detail");
		echo "</h3>";
	}

?>
<script type="text/javascript">
	document.observe( "dom:loaded", function() {

		document.querySelector("#TableRapportsechangesaliDetails").style.width = '100%';

		var nameTdIndexReferent = document.querySelector('#TableRapportsechangesaliDetailsColumnErreurReferent').cellIndex + 1;
		var ref = '#TableRapportsechangesaliDetails td:nth-child(' + nameTdIndexReferent + ')';
		var nameTdIndexRdv = document.querySelector('#TableRapportsechangesaliDetailsColumnErreurRdv').cellIndex + 1;
		var rdv = '#TableRapportsechangesaliDetails td:nth-child(' + nameTdIndexRdv + ')';
		var nameTdIndexDsp = document.querySelector('#TableRapportsechangesaliDetailsColumnErreurDsp').cellIndex + 1;
		var dsp = '#TableRapportsechangesaliDetails td:nth-child(' + nameTdIndexDsp + ')';
		var nameTdIndexCer = document.querySelector('#TableRapportsechangesaliDetailsColumnErreurCer').cellIndex + 1;
		var cer = '#TableRapportsechangesaliDetails td:nth-child(' + nameTdIndexCer + ')';	
		var nameTdIndexOrient = document.querySelector('#TableRapportsechangesaliDetailsColumnErreurOrient').cellIndex + 1;
		var orient = '#TableRapportsechangesaliDetails td:nth-child(' + nameTdIndexOrient + ')';
		var nameTdIndexD1 = document.querySelector('#TableRapportsechangesaliDetailsColumnErreurD1').cellIndex + 1;
		var d1 = '#TableRapportsechangesaliDetails td:nth-child(' + nameTdIndexD1 + ')';
		var nameTdIndexD2 = document.querySelector('#TableRapportsechangesaliDetailsColumnErreurD2').cellIndex + 1;
		var d2 = '#TableRapportsechangesaliDetails td:nth-child(' + nameTdIndexD2 + ')';
		var nameTdIndexB7 = document.querySelector('#TableRapportsechangesaliDetailsColumnErreurB7').cellIndex + 1;
		var b7 = '#TableRapportsechangesaliDetails td:nth-child(' + nameTdIndexB7 + ')';
		var elements = document.querySelectorAll(
			""+ref+", "+rdv+", "+dsp+", "+cer+", "+orient+", "+d1+", "+d2+", "+b7+""
		);
		elements.forEach(function(element) {
			if(element.textContent != '-'){
				element.classList.add('boolean');
				if(element.textContent == 'Ok') {
					element.classList.add('true');
				} else {
					element.classList.add('false');
				}
			}
		});
	});
</script>