<script type="text/javascript">
$(document).ready(function()  {

    // basic usage see app/Controllers/CitiesController::index
    $('#tableau').dataTable({
        'pageLength'    : 20,
        //'aLengthMenu'       : [[10, 20, 50], [10, 20, 50]],
        'jQueryUI'         : false,
        'pagingType'   : 'full_numbers',
        'autoWidth'        : false,
        'order'         : [],
        'scrollY'          : '475px',
       // 'scrollCollapse'   : true,
        "processing"       : true,
        "serverSide"       : true,
        "ajax"             : "/requetes/index",
        //"sDom"              : "frtip",
        "dom"              : 'ifrtS',
        "deferRender"      : true,
        'language':
        {
            'processing'   : 'Traitement en cours...',
            'lengthMenu'   : 'Afficher _MENU_ requêtes',
            'zeroRecords'  : 'Aucune requête à afficher',
            'info'         : 'Affichage de _START_ à _END_ requêtes sur _TOTAL_',
            'infoEmpty'    : 'Affichage de l\'élement 0 à 0 sur 0 requêtes',
            'infoFiltered' : '(filtré de _MAX_ requêtes au total)',
            'infoPostFix'  : '',
            'search'       : 'Rechercher&nbsp;:',
            'url'          : ''
        },
        'columnDefs':
            //colonne cachée : ici 0
            [
                {'visible': false,    'targets': [ 0 ]},
                {'visible': true,     'targets': [ 1 ]},
                {'visible': true,     'targets': [ 2 ]},
                {'visible': true,     'targets': [ 3 ]},
				{"targets": -1,
				"data": null,
				"defaultContent": "<button>Export CSV</button>"	}
            ],
        'searchCols' : // init filtre, toutes les colonnes doivent être présentes
                [
                 null,
                 null,
                 null,
                 null
               ],

	   'rowCallback': function(row, data)
            {
            $('td:not(:eq(3))',row).click(function ()
                    {
                        location.href="/requetes/view/"+data[0];
                    });
            $('td:eq(3)',row).click(function ()
                {
                    location.href="/requetes/view/"+data[0]+"/export:csv";
                });

                var sTitle;
                var sStyle;

                sTitle =  "Description : "+data[3];
                $('td', row).parent().attr(
                         {title:sTitle,
                          style:sStyle }
                         );
            }
            });
});
</script>
<div class="requetes index">
	<h2><?php echo __('Requetes'); ?></h2>

<table cellpadding="5" cellspacing="0"  id="tableau" class="stripe hover order-column">
    <thead>
    <tr>
        <th>id</th>
        <th>Nom</th>
        <th>Type</th>
        <th>Description</th>
		<th>Action</th>
    </tr>
    </thead>
   <tbody>
        <tr>
            <td colspan="5" class="dataTables_empty">Processus en cours</td>
        </tr>
  </tbody>
</table>


</div>
