<?php
$this->pageTitle = __m('Tutoriel.View.Titre');

echo '<h1>' . $this->pageTitle . '</h1>';

$sousPartie = false;
echo '<div id="tutoriel">';
foreach($tutoriels as $tuto) {
	if(empty($tuto['Tutoriel']['parentid'])) {
		if($sousPartie == true) {
			echo '</ul></div>';
			$sousPartie = false;
		}
		echo '<div>';
		$typeTitreDebut = '<h2>';
		$typeTitreFin = '</h2>';
	} else {
		if($sousPartie == false) {
			echo '<ul>';
			$sousPartie = true;
		}
		$typeTitreDebut = '<li>';
		$typeTitreFin = '</li>';
	}

	if(empty($tuto['Tutoriel']['fichiermodule_id'])) {
		$content = $tuto['Tutoriel']['titre'];
	} else {
		$content = $this->Xhtml->link($tuto['Tutoriel']['titre'], array('action' => 'download', $tuto['Tutoriel']['fichiermodule_id']) );
	}
	echo $typeTitreDebut . $content . $typeTitreFin;
}
echo '</div>';
