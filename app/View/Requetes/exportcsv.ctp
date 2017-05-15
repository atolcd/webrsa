<?php
// REFERENTIEL : passage des paramètres
//
$this->Csv->preserveLeadingZerosInExcel = FALSE;
$this->Csv->delimiter = ';';
$entete=array_values($listefields);
// foreach($listefields as $cle => $libelle) {
//     $entete[]=$libelle ;
// }
$this->Csv->addRow( $entete );

$col = 0;
foreach( $extractions['data'] as $extraction ) {
//    $this->Csv->addRow($extraction);
    //fputcsv($this->buffer, $extraction, $this->Csv->delimiter, '');
    fwrite($this->Csv->buffer, $extraction."\n");
}
//fputcsv($this->Csv->buffer, $row, $this->delimiter, $this->enclosure);
Configure::write( 'debug', 0 );
//echo $this->Csv->render( $requete['Requete']['nom'].'-'.date( 'Ymd-Hhm' ).'.csv','ISO-8859-1','UTF-8' );
echo $this->Csv->render( $requete['Requete']['nom'].'-'.date( 'Ymd-Hhm' ).'.csv',NULL,'UTF-8' );

?>