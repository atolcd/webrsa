<?php
// préparation des parametres de datatables colonnes visibles
// selection des colonnes à afficher
$defcol     = ",'columnDefs': [ " ;
$flagsearch = "false" ;
$libelles   = array_values($listefields);
$tableCol   = array() ; // libelles des colonnes selectionnees
$nbcolsel   = 0; // compteur du nb de colonnes selectionnees
$col        = 0; // pointeur sur la colonne en cours
$virgule    = "";
foreach ($entetes as $operation) {
   $toperation  = explode('-',$operation) ;
   if (array_intersect($toperation,array('SEARCH','SHOW','CACHE'))) {
            $tableCol[] = $libelles[$col] ;
            $defcol .= $virgule;
            $virgule = ",";
            if (in_array('CACHE',$toperation)) {
              $defcol .= "{'visible': false, ";
            } else {
              $defcol .= "{'visible': true, ";
            }
            if (in_array('SEARCH',$toperation)) {
               $defcol .= "'searchable': true, ";
               $flagsearch = "true" ;
            } else {
               $defcol .= "'searchable': false, ";
            }
   $defcol .= "'targets': [ ".$nbcolsel." ]}";
   $nbcolsel++ ;
   }
   $col++ ;
}
$defcol .= "]";
?>
<script type="text/javascript">
$(document).ready(function()  {

	oTable= $('#tableau').DataTable({
        'pageLength'    : 20,
        //'aLengthMenu'       : [[10, 20, 50], [10, 20, 50]],
        'jQueryUI'         : false,
        'pagingType'   : 'full_numbers',
        'autoWidth'        : false,
        'order'         : [],
        'scrollY'          : '475px',
        'processing'       : true,
        'serverSide'       : true,
        'ajax'             : "/requetes/view/<?php echo $requete['Requete']['id']; ?>",
        'dom'              : 'ifrtS',
        'deferRender'      : true,
        'searching' : <?php echo $flagsearch ; ?>,
        'language':
        {
            'processing'   : 'Traitement en cours...',
            'lengthMenu'   : 'Afficher _MENU_ résultats',
            'zeroRecords'  : 'Aucune requête à afficher',
            'info'         : 'Affichage de _START_ à _END_ résultats sur _TOTAL_',
            'infoEmpty'    : 'Affichage de l\'élement 0 à 0 sur 0 résultats',
            'infoFiltered' : '(filtré de _MAX_ résultats au total)',
            'infoPostFix'  : '',
            'search'       : 'Rechercher&nbsp;:',
            'url'          : ''
        }

// colonnes visibles

<?php echo $defcol ?>

            });
});

</script>
<div class="résultats index">
	<h2><?php echo __('Résultats'); ?></h2>

    <div class="leform">

    <?php echo $this->Form->create('Export',array('type'=>'post','url'=>'/requetes/view/'.$requete['Requete']['id']));?>
    <?php echo $this->Form->input('Requete.id', array( 'type' => 'hidden','value'=>$requete['Requete']['id']) );?>
    <?php echo $this->Form->input('Requete.export', array( 'type' => 'hidden','value'=>'csv') );?>
    <?php echo $this->Form->submit('Export CSV', array( 'div' => false ));?>
    <?php echo $this->Form->end();?>
    <?php //echo $this->html->link( 'Export : CSV', array( 'controller' => 'requetes', 'action' => 'view',$requete['Requete']['id'],'document' => 'csv' ) );?>
    <?php //echo $this->html->link( ' : Excel', array( 'controller' => 'requetes', 'action' => 'view',$requete['Requete']['id'],'document' => 'excel' ) );?>
    </div>
<table cellpadding="5" cellspacing="0"  id="tableau" class="stripe hover order-column">
    <thead>
    <tr>
    <?php foreach($tableCol as $libelle) {?>
        <th><?php echo $libelle?></th>
    <?php }?>
    </tr>
    </thead>
   <tbody>
        <tr>
            <td colspan="5" class="dataTables_empty">Processus en cours</td>
        </tr>
  </tbody>
</table>

</div>
