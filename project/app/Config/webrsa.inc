<?php
   /**
    * Paramétrage de la génération de documents PDF au moyen de Gedooo.
    *
    * Les clés de configuration possibles sont:
    *  - method: la méthode permettant de générer le document de sortie; valeurs
    *      possibles: classic, unoconv, cloudooo (ainsi que cloudooo_ancien et
    *      unoconv_ancien)
    *  - wsdl: l'URL du web-service, utilisé quelle que soit la méthode de
    *      génération; exemple: http://192.168.2.35:8880/ODFgedooo/OfficeService?wsdl
    *  - unoconv_bin: chemin vers le binaire unoconv, lorsque la méthode unoconv
    *      ou unoconv_ancien est paramétrée; exemple: /usr/bin/unoconv
    *  - cloudooo_host: le serveur cloudooo, lorsque la méthode cloudooo ou
    *      cloudooo_ancien est utilisée; exemple: 192.168.2.35
    *  - cloudooo_port: le port du serveur cloudooo, lorsque la méthode cloudooo
    *      ou cloudooo_ancien est utilisée; exemple: 8012
    *  - debug_export_data: permet d'exporter les données envoyées en plus des noms
    *      des variables dans un fichier du répertoire app/tmp/logs lorsque l'on
    *      n'est pas en mode production; false (par défaut) ou true
    *  - dont_force_newlines: pour que les retours à la ligne ne soient pas forcés
    *      (ancien comportement de unoconv et cloudooo); false (par défaut) ou true
    *  - filter_vars: permet de filtrer les variables envoyées à l'impression en
    *      analysant les variables utilisateur définies dans le modèle ; false
    *      (par défaut) ou true
    */
   /**
    * Nouvelle façon de paramétrer la génération de documents PDF via Gedooo.
    *
    * Ci-dessous, l'ensemble des clés de configuration possibles.
    *
    * array(
    *  'method' => 'classic', // classic, unoconv, cloudooo
    *  // Ancien serveur Gedooo
    *  // 'wsdl' => 'http://gedooo.test.adullact.org:8080/axis2/services/OfficeService?wsdl',
    *  // Nouveau serveur Gedooo
    *  'wsdl' => 'http://gedooo.test.adullact.org:8880/ODFgedooo/OfficeService?wsdl',
    *  // Pour Unoconv
    *  'unoconv_bin' => '/home/cbuffin/Bureau/1/usr/bin/unoconv',
    *  // Pour Cloudooo
    *  'cloudooo_host' => 'xx.xx.xx.xx',
    *  'cloudooo_port' => '8012'
    * )
    */
   Configure::write(
       'Gedooo',
       array(
           'method' => env('GEDOOO_METHOD'),
           'wsdl' => env('GEDOOO_WSDL'),
           'cloudooo_host' => env('GEDOOO_CLOUDOOO_HOST'),
           'cloudooo_port' => env('GEDOOO_CLOUDOOO_PORT'),
           'filter_vars' => (boolean)env('GEDOOO_FILTER_VARS'),
           'debug_export_data' => (boolean)env('GEDOOO_EXPORT_DATA'),
       )
   );

   /**
    * Le chemin complet vers la base de l'application (utilisé par certains shells)
    *
    * @default: null
    */
   Configure::write( 'Cg.departement', env('CD_DEPARTEMENT') );

   /**
    * Configuration pour le dialogue avec un CMS (système de gestion de contenu)
    * comme Alfresco, servant à stocker les PDF
    */
   Configure::write(
       'Cmis',
       array(
           'url' => env('CMIS_URL'),
           'username' => env('CMIS_USERNAME'),
           'password' => env('CMIS_PASSWORD'),
           'prefix' => env('CMIS_PREFIX'),
       )
   );

   Configure::write('Categorie.General', array('webrsa'));

