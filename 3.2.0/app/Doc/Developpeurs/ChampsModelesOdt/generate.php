<?php
	/**
	 * Permet de générer le contenu du fichier content.xml se trovant dans un
	 * modèle odt avec l'affichage d'une liste de champs.
	 *
	 * A utiliser pour les tests.
	 */
	$champs = array(
		'dossierep_id',
		'dossierep_personne_id',
		'dossierep_themeep',
		'dossierep_created_time',
		'dossierep_created',
		'dossierep_modified_time',
		'dossierep_modified',
		'dossierep_anonymiser',
		'personne_id',
		'personne_foyer_id',
		'personne_qual',
		'personne_nom',
		'personne_prenom',
		'personne_nomnai',
		'personne_prenom2',
		'personne_prenom3',
		'personne_nomcomnai',
		'personne_dtnai',
		'personne_rgnai',
		'personne_typedtnai',
		'personne_nir',
		'personne_topvalec',
		'personne_sexe',
		'personne_nati',
		'personne_dtnati',
		'personne_pieecpres',
		'personne_idassedic',
		'personne_numagenpoleemploi',
		'personne_dtinscpoleemploi',
		'personne_numfixe',
		'personne_numport',
		'personne_age',
		'dossier_matricule',
		'dossier_dtdemrsa',
		'dossier_dtdemrsaactuelle',
		'foyer_sitfam',
		'foyer_nbenfants',
		'adresse_numvoie',
		'adresse_libtypevoie',
		'adresse_nomvoie',
		'adresse_compladr',
		'adresse_nomcom',
		'adresse_numcom',
		'adresse_codepos',
		'serviceinstructeur_lib_service',
		'orientstruct_date_valid',
		'historiqueetatpe_inscritpe',
		'dossiercaf_ddratdos',
		'commissionep_dateseance_time',
		'commissionep_dateseance',
		'commissionep_salle',
		'commissionep_lieuseance',
		'commissionep_adresseseance',
		'commissionep_codepostalseance',
		'commissionep_villeseance',
		'nonrespectsanctionep93_origine',
		'nonrespectsanctionep93_rgpassage',
		'relance1_dateimpression',
		'relance2_dateimpression',
		'passage1_impressionconvocation',
		'decision1ep_decision',
		'decision1ep_commentaire',
		'decision1cg_decision',
		'decision1cg_commentaire',
		'passage2_impressionconvocation',
		'decision2ep_decision',
		'decision2ep_commentaire',
		'decision2cg_decision',
		'decision2cg_commentaire',
		'passage3_impressionconvocation',
		'decision3ep_decision',
		'decision3ep_commentaire',
		'decision3cg_decision',
		'decision3cg_commentaire',
		'structurereferente_lib_struc',
		'dsp_natlog',
		'dsp_nivetu',
		'radiationpe_date',
		'radiationpe_etat',
		'radiationpe_code',
		'radiationpe_motif',
		'modeleodt_path'
	);

	$template = '<?xml version="1.0" encoding="UTF-8"?>
<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:rpt="http://openoffice.org/2005/report" xmlns:of="urn:oasis:names:tc:opendocument:xmlns:of:1.2" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:grddl="http://www.w3.org/2003/g/data-view#" xmlns:officeooo="http://openoffice.org/2009/office" xmlns:tableooo="http://openoffice.org/2009/table" xmlns:drawooo="http://openoffice.org/2010/draw" xmlns:calcext="urn:org:documentfoundation:names:experimental:calc:xmlns:calcext:1.0" xmlns:loext="urn:org:documentfoundation:names:experimental:office:xmlns:loext:1.0" xmlns:field="urn:openoffice:names:experimental:ooo-ms-interop:xmlns:field:1.0" xmlns:formx="urn:openoffice:names:experimental:ooxml-odf-interop:xmlns:form:1.0" xmlns:css3t="http://www.w3.org/TR/css3-text/" office:version="1.2">
	<office:scripts/>
	<office:font-face-decls>
		<style:font-face style:name="Lohit Hindi1" svg:font-family="&apos;Lohit Hindi&apos;"/>
		<style:font-face style:name="Times New Roman" svg:font-family="&apos;Times New Roman&apos;" style:font-family-generic="roman" style:font-pitch="variable"/>
		<style:font-face style:name="Arial" svg:font-family="Arial" style:font-family-generic="swiss" style:font-pitch="variable"/>
		<style:font-face style:name="AR PL UMing HK" svg:font-family="&apos;AR PL UMing HK&apos;" style:font-family-generic="system" style:font-pitch="variable"/>
		<style:font-face style:name="Lohit Hindi" svg:font-family="&apos;Lohit Hindi&apos;" style:font-family-generic="system" style:font-pitch="variable"/>
	</office:font-face-decls>
	<office:automatic-styles>
		<style:style style:name="T1" style:family="text">
			<style:text-properties officeooo:rsid="000c0e9a"/>
		</style:style>
		<number:number-style style:name="N0">
			<number:number number:min-integer-digits="1"/>
		</number:number-style>
	</office:automatic-styles>
	<office:body>
		<office:text>
			<text:sequence-decls>
				<text:sequence-decl text:display-outline-level="0" text:name="Illustration"/>
				<text:sequence-decl text:display-outline-level="0" text:name="Table"/>
				<text:sequence-decl text:display-outline-level="0" text:name="Text"/>
				<text:sequence-decl text:display-outline-level="0" text:name="Drawing"/>
			</text:sequence-decls>
			<text:user-field-decls>
				#DEFINITIONS#
			</text:user-field-decls>
			#EXAMPLES#
		</office:text>
	</office:body>
</office:document-content>';

	$definitions = array();
	$examples = array();
	foreach( $champs as $champ ) {
		$definitions[] = '<text:user-field-decl office:value-type="float" office:value="0" text:name="'.$champ.'"/>';
		$examples[] = '<text:p text:style-name="Standard">
				<text:span text:style-name="T1">'.$champ.': </text:span>
				<text:user-field-get style:data-style-name="N0" text:name="'.$champ.'">0</text:user-field-get>
			</text:p>';
	}

	$out = str_replace( '#DEFINITIONS#', implode( "\n", $definitions ), $template );
	$out = str_replace( '#EXAMPLES#', implode( "\n", $examples ), $out );
	echo $out;
?>