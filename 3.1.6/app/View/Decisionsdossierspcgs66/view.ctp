<?php

$this->pageTitle = __d('decisiondossierpcg66', "Decisionsdossierspcgs66::{$this->action}");
?>
<?php

echo $this->Xhtml->tag('h1', $this->pageTitle);
?>

<?php
	$orgsValues = is_array($orgs) ? array_values($orgs) : null;
	$orgs = isset($orgsValues[1]) ? $orgsValues[1] : '';

    echo $this->Default2->view(
        $decisiondossierpcg66,
        array(
            'Decisionpdo.libelle',
            'Decisiondossierpcg66.commentairetechnicien' => array( 'type' => 'text'),
            'Decisiondossierpcg66.datepropositiontechnicien',
            'Decisiondossierpcg66.avistechnique',
            'Decisiondossierpcg66.commentaireavistechnique' => array( 'type' => 'text'),
            'Decisiondossierpcg66.useravistechnique_id' => array('type' => 'text', 'value' => Hash::get($gestionnaire, (string)Hash::get($decisiondossierpcg66, 'Decisiondossierpcg66.useravistechnique_id'))),
            'Decisiondossierpcg66.dateavistechnique',
            'Decisiondossierpcg66.validationproposition',
            'Decisiondossierpcg66.commentairevalidation' => array( 'type' => 'text'),
            'Decisiondossierpcg66.userproposition_id' => array('type' => 'text', 'value' => Hash::get($gestionnaire, (string)Hash::get($decisiondossierpcg66, 'Decisiondossierpcg66.userproposition_id'))),
            'Decisiondossierpcg66.datevalidation',
            'Dossierpcg66.etatdossierpcg',
            'Notificationdecisiondossierpcg66.name' => array('label' => 'Transmission à', 'value' => $orgs),
            'Decisiondossierpcg66.datetransmissionop',
			'Decisiondossierpcg66.motifannulation'
        ),
        array(
            'class' => 'aere',
            'options' => $options
        )
    );


if ($this->Permissions->checkDossier('decisionsdossierspcgs66', 'avistechnique', $dossierMenu) || $this->Permissions->checkDossier('decisionsdossierspcgs66', 'validation', $dossierMenu)) {
    echo $this->Default2->view(
        $decisiondossierpcg66,
        array(
            'Decisiondossierpcg66.commentaire' => array('label' => 'Commentaire global')
        ),
        array(
            'class' => 'aere'
        )
    );
}
?>

<?php

echo "<h2>Pièces liées à la décision du dossier</h2>";
echo $this->Fileuploader->results(Set::classicExtract($decisiondossierpcg66, 'Fichiermodule'));
?>
<?php

echo '<div class="aere">';
echo $this->Default->button(
    'back',
    array(
        'controller' => 'dossierspcgs66',
        'action' => 'edit',
        $decisiondossierpcg66['Dossierpcg66']['id']
    ),
    array(
        'id' => 'Back'
    )
);
echo '</div>';
?>