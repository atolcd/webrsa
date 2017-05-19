<?php

$this->Csv->preserveLeadingZerosInExcel = true;

if ($searchFunction == 'searchDossier') {
    $this->Csv->addRow(
		array(
			__d('dossier', 'Dossier.numdemrsa'),
			'Nom de la personne concernée',
			'Origine de la PDO',
			'Type de dossier',
			'Date de création de la DO',
			'Pôle / Gestionnaire',
			'Décision',
			'Nb de propositions de décisions',
			'État du dossier',
			'Date de réception du document',
			'Motif(s) de la personne',
			'Statut(s) de la personne',
			'Nb de fichiers dans la corbeille',
			__d('search_plugin', 'Structurereferenteparcours.lib_struc'),
			__d('search_plugin', 'Referentparcours.nom_complet'),
			'Code famille de l\'emploi recherché',
			'Code domaine de l\'emploi recherché',
			'Code métier de l\'emploi recherché',
			'Appellation de l\'emploi recherché (rome V3)',
			'Métiers (détaillé) (rome v2)'
		)
    );
} else {
    $this->Csv->addRow(
            array(
                __d('dossier', 'Dossier.numdemrsa'),
                'Nom de la personne concernée',
                'Origine de la PDO',
                'Type de dossier',
                'Date de réception',
                'Pôle  / Gestionnaire',
                'Nb de propositions de décisions',
                'Nb de traitements PCGs',
                'Type de traitement',
                'État du dossier',
                'Nb de fichiers dans la corbeille',
                __d('search_plugin', 'Structurereferenteparcours.lib_struc'),
                __d('search_plugin', 'Referentparcours.nom_complet'),
            )
    );
}

foreach ($results as $i => $result) {

    // Liste des organismes auxquels on transmet le dossier
    $orgs = vfListeToArray($result['Orgtransmisdossierpcg66']['listorgs']);
    if (!empty($orgs)) {
        $orgs = implode(',', $orgs);
    } else {
        $orgs = '';
    }

    $datetransmission = '';
    if ($result['Dossierpcg66']['etatdossierpcg'] == 'transmisop') {
        $datetransmission = ' à ' . $orgs . ' le ' . date_short(Set::classicExtract($result, 'Decisiondossierpcg66.datetransmissionop'));
    } else if ($result['Dossierpcg66']['etatdossierpcg'] == 'atttransmisop') {
        $datetransmission = ' à ' . $orgs;
    }

    $etatdosrsaValue = Set::classicExtract($result, 'Situationdossierrsa.etatdosrsa');
    $etatDossierRSA = isset($etatdosrsa[$etatdosrsaValue]) ? $etatdosrsa[$etatdosrsaValue] : 'Non défini';

    //Liste des différents motifs de la personne
    $differentsMotifs = $result['Personnepcg66']['listemotifs'];
    //Liste des différents statuts de la personne
    $differentsStatuts = $result['Personnepcg66']['listestatuts'];

    //Liste des différents traitements de la personne
    $traitementspcgs66 = $result['Dossierpcg66']['listetraitements'];

    $pole = isset($result['Dossierpcg66']['poledossierpcg66_id']) ? ( Set::enum(Hash::get($result, 'Dossierpcg66.poledossierpcg66_id'), $polesdossierspcgs66) . ' / ' ) : null;

    $gestionnaires = Hash::get($result, 'User.nom_complet');

    $personnesConcernees = Hash::extract($result, 'Personneconcernee.{n}.Personne.nom_complet');

    if (!empty($personnesConcernees)) {
        $personnesConcernees = implode(" ,\n", $personnesConcernees);
    } else {
        $personnesConcernees = '';
    }

    if ($searchFunction == 'searchDossier') {
        $row = array(
            h(Hash::get($result, 'Dossier.numdemrsa')),
            $personnesConcernees,
            h(Set::enum(Hash::get($result, 'Dossierpcg66.originepdo_id'), $originepdo)),
            h(Set::enum(Hash::get($result, 'Dossierpcg66.typepdo_id'), $typepdo)),
            h($this->Locale->date('Locale->date', Hash::get($result, 'Dossierpcg66.datereceptionpdo'))),
            h($pole . $gestionnaires),
            h(Hash::get($result, 'Decisionpdo.libelle')),
            h($result['Dossierpcg66']['nbpropositions']),
            Set::enum(Hash::get($result, 'Dossierpcg66.etatdossierpcg'), $options['Dossierpcg66']['etatdossierpcg']) . $datetransmission,
			$this->Locale->date( 'Locale->date', Hash::get( $result, 'Traitementpcg66.datereception' ) ),
			$differentsMotifs,
            $differentsStatuts,
            h($result['Fichiermodule']['nb_fichiers_lies']),
            Hash::get($result, 'Structurereferenteparcours.lib_struc'),
            Hash::get($result, 'Referentparcours.nom_complet'),
            Hash::get($result, 'Familleromev3.name'),
            Hash::get($result, 'Domaineromev3.name'),
            Hash::get($result, 'Metierromev3.name'),
            Hash::get($result, 'Appellationromev3.name'),
            implode( ' - ', array( Hash::get( $result, 'Categoriemetierromev2.code' ), Hash::get( $result, 'Categoriemetierromev2.name' ) ) )
        );
    } else {
        $row = array(
            h(Hash::get($result, 'Dossier.numdemrsa')),
            $personnesConcernees,
            h(Set::enum(Hash::get($result, 'Dossierpcg66.originepdo_id'), $originepdo)),
            h(Set::enum(Hash::get($result, 'Dossierpcg66.typepdo_id'), $typepdo)),
            h($this->Locale->date('Locale->date', Hash::get($result, 'Dossierpcg66.datereceptionpdo'))),
            h(Hash::get($result, 'User.nom_complet')),
            h($result['Dossierpcg66']['nbpropositions']),
            h($result['Personnepcg66']['nbtraitements']),
            $traitementspcgs66,
            Set::enum(Hash::get($result, 'Dossierpcg66.etatdossierpcg'), $options['Dossierpcg66']['etatdossierpcg']) . $datetransmission,
            h($result['Fichiermodule']['nb_fichiers_lies']),
            Hash::get($result, 'Structurereferenteparcours.lib_struc'),
            Hash::get($result, 'Referentparcours.nom_complet'),
        );
    }
    $this->Csv->addRow($row);
}

Configure::write('debug', 0);
echo $this->Csv->render("{$this->request->params['controller']}_{$this->request->params['action']}_" . date('Ymd-His') . '.csv');
?>