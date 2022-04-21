<?php
echo $this->Html->tag( 'h1', __m('Problemeadresses.titre') );
echo $this->Default3->configuredindex(
    $pbAdresses,
    array(
        'options' => $options,
        'paginate' => false
    )
);

echo '<ul class="actionMenu">'
		.'<li>'
			.$this->Xhtml->printLinkJs(
				'Imprimer le tableau',
				array( 'onclick' => 'printit(); return false;', 'class' => 'noprint' )
			)
		.'</li>'
		.'<li>'
		. $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'controller' => 'algorithmeorientation', 'action' => 'exportcsv_adresses' )
		)
		.'</li>'
	.'</ul>';

echo '<ul class="actionMenu center">'
		.'<li>'
        .  $this->Xhtml->link(
            __m('IgnorerAdresses.bouton'),
            [
                'controller' => 'algorithmeorientation',
                'action' => 'affichageOrientables',
                true
            ],
            [],
            __m("IgnorerAdresses.confirm")
        )
		.'</li>'
        .'<li>'
        .  $this->Xform->button( __m('rafraichir.bouton'), ['type' => 'submit', 'form' =>  'AlgorithmeorientationOrientationForm', 'id' => 'submitAlgorithmeorientationOrientationForm']
        )
		.'</li>'
	.'</ul>';

    ?>