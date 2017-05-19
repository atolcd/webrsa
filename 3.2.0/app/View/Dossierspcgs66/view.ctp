<?php
$this->pageTitle = __d('dossierpcg66', "Dossierspcgs66::{$this->action}");
if (Configure::read('debug') > 0) {
    echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
}
?>
<?php
echo $this->Xhtml->tag('h1', $this->pageTitle);

echo $this->Default2->view(
	$dossierpcg66,
	array(
		'Dossierpcg66.datereceptionpdo',
		'Typepdo.libelle',
		'Originepdo.libelle',
		'Dossierpcg66.orgpayeur',
		'Serviceinstructeur.lib_service',
		'Dossierpcg66.iscomplet',
		'Poledossierpcg66.name',
		'User.nom_complet' => array('type' => 'text', 'label' => 'Gestionnaire du dossier (technicien en charge du dossier)'),
		'Dossierpcg66.etatdossierpcg',
		'Notificationdecisiondossierpcg66.name' => array('label' => 'Transmission à', 'value' => @$orgs),
		'Decisiondossierpcg66.0.datetransmissionop',
		'Dossierpcg66.motifannulation',
	),
	array(
		'class' => 'aere',
		'options' => $options
	)
);
//debug($dossierpcg66);
//	if( $dossierpcg66['Decisiondossierpcg66'][0]['etatop'] == 'transmis' && !empty( $dossierpcg66['Decisiondossierpcg66'][0]['datetransmissionop'] ) ) {
//		echo $this->Default2->view(
//			$dossierpcg66,
//			array(
//				'Decisiondossierpcg66.0.datetransmissionop'
//			),
//			array(
//				'class' => 'aere',
//				'options' => $options
//			)
//		);
//	}
?>
<h2>Décision du dossier</h2>
<?php if (!empty($dossierpcg66['Decisiondossierpcg66'])): ?>
    <?php if ($dossierpcg66['Decisiondossierpcg66'][0]['etatop'] == 'transmis'): ?>
        <table class="tooltips aere">
            <thead>
                <tr>
                    <th>Décision</th>
                    <th>Date de la décision</th>
                    <th>Validation</th>
                    <th>Date de validation</th>
                    <th>Commentaire du technicien</th>
                </tr>
            </thead>
            <tbody>
                <?php
                echo $this->Xhtml->tableCells(
                        array(
                            h(Set::enum(Hash::get($dossierpcg66['Decisiondossierpcg66'][0], 'decisionpdo_id'), $decisionpdo)),
                            h(date_short(Hash::get($dossierpcg66['Decisiondossierpcg66'][0], 'datepropositiontechnicien'))),
                            h(value($options['Decisiondossierpcg66']['validationproposition'], Hash::get($dossierpcg66['Decisiondossierpcg66'][0], 'validationproposition'))),
                            h(date_short(Hash::get($dossierpcg66['Decisiondossierpcg66'][0], 'datevalidation'))),
                            h(Hash::get($dossierpcg66['Decisiondossierpcg66'][0], 'commentairetechnicien'))
                        )
                );
                ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="notice">Aucune décision émise pour ce dossier</p>
    <?php endif; ?>
<?php else: ?>
    <p class="notice">Aucune décision émise pour ce dossier</p>
<?php endif; ?>

<?php if ($this->Permissions->checkDossier('decisionsdossierspcgs66', 'edit', $dossierMenu) || $this->Permissions->checkDossier('decisionsdossierspcgs66', 'avistechnique', $dossierMenu) || $this->Permissions->checkDossier('decisionsdossierspcgs66', 'validation', $dossierMenu)): ?>
    <?php if ( !empty( $dossierpcg66['Decisiondossierpcg66'] ) ):?>
    <h2>Décisions émises</h2>
    <table class="tooltips aere">
        <thead>
            <tr>
                <th>Commentaire de l'avis technique</th>
                <th>Date de l'avis technique</th>
                <th>Commentaire de la validation</th>
                <th>Date de la validation</th>
            </tr>
        </thead>
        <tbody>
            <?php
//            debug($dossierpcg66);
            foreach ($dossierpcg66['Decisiondossierpcg66'] as $decisiondossierpcg66) {
                echo $this->Xhtml->tableCells(
                    array(
                        h('"'.$decisiondossierpcg66['commentaireavistechnique'].'" émis par '.Hash::get( $decisiondossierpcg66, 'Useravistechnique.nom_complet' )),
                        h(date_short($decisiondossierpcg66['dateavistechnique'] ) ),
                        h('"'.$decisiondossierpcg66['commentairevalidation'].'" émis par '.Hash::get( $decisiondossierpcg66, 'Userproposition.nom_complet' )),
                        h(date_short($decisiondossierpcg66['datevalidation'] ) )
                    )
                );
            }
            ?>
        </tbody>
    </table>
    <?php endif;?>
<?php endif; ?>
<?php
echo "<h2>Pièces jointes</h2>";
echo $this->Fileuploader->results(Set::classicExtract($dossierpcg66, 'Fichiermodule'));
?>
<?php if (!empty($traitementsCourriersEnvoyes)): ?>
    <h2>Courriers envoyés</h2>
    <table class="tooltips aere">
        <thead>
            <tr>
                <th>Type de traitement</th>
                <th>Date d'envoi</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
    <?php
    foreach ($traitementsCourriersEnvoyes as $courrierEnvoye) {
        echo $this->Xhtml->tableCells(
                array(
                    h($courrierEnvoye['Situationpdo']['libelle']),
                    h(date_short($courrierEnvoye['Traitementpcg66']['dateenvoicourrier'])),
                    $this->Xhtml->printLink(
                            'Imprimer', array('controller' => 'traitementspcgs66', 'action' => 'printModeleCourrier', $courrierEnvoye['Traitementpcg66']['id']), $this->Permissions->checkDossier('traitementspcgs66', 'printModeleCourrier', $dossierMenu)
                    )
                )
        );
    }
    ?>
        </tbody>
    </table>
<?php endif; ?>

<?php
echo '<div class="aere">';
echo $this->Default->button(
        'back', array(
    'controller' => 'dossierspcgs66',
    'action' => 'index',
    $dossierpcg66['Dossierpcg66']['foyer_id']
        ), array(
    'id' => 'Back'
        )
);
echo '</div>';
?>
</div>