//-----------------------------------------------------------------

function make_treemenus( absoluteBaseUrl ) {
    var dir = absoluteBaseUrl + 'img/icons';
    $( '.treemenu li:has(ul)' ).each( function ( i ) {
        var img = $.create( 'img', { 'src': dir + '/bullet_toggle_plus.png', 'alt' : '+' }, '' );
        var link = $.create( 'a', { 'href': '#', 'class' : 'toggler' }, img );
        var sign = '+';
        $( link ).click( function () {
            $( this ).closest( 'li' ).children( 'ul' ).each( function ( j ) {
                if( $( this ).css( 'display' ) == 'none' ) {
                    sign = '-';
                }
                else {
                    sign = '+';
                }
            } );
            $( this ).children( 'img' ).each( function ( k ) {
                if( sign == '+' ) {
                    $( this ).attr( 'src', dir + '/bullet_toggle_plus.png' );
                    $( this ).attr( 'alt', '+' );
                }
                else {
                    $( this ).attr( 'src', dir + '/bullet_toggle_minus.png' );
                    $( this ).attr( 'alt', '-' );
                }
            } );
            $( this ).closest( 'li' ).children( 'ul' ).toggle();
            return false;
        } );
        $( this ).prepend( link );
        $( this ).children( 'ul' ).hide();
    } );

    var currentUrl = location.href.replace( new RegExp( '(#.*)$' ), '' ).replace( absoluteBaseUrl, '/' );
    var relBaseUrl = absoluteBaseUrl.replace( new RegExp( '^(http://[^/]+/)' ), '/' );
    $( '.treemenu a' ).each( function ( i ) {
        if( $( this ).attr( 'href' ).replace( relBaseUrl, '/' ) == currentUrl || $( this ).attr( 'href' ).replace( relBaseUrl, '/' ) == currentUrl.replace( '/edit/', '/view/' ) ) {
            $( this ).parents().show();
            $( this ).parents( 'li' ).children( 'a.toggler' ).children( 'img' ).each( function ( k ) {
                $( this ).attr( 'src', dir + '/bullet_toggle_minus.png' );
                $( this ).attr( 'alt', '-' );
            } );
            $( this ).parents( 'li' ).children( 'ul' ).show();
        }
    } );
}


//-----------------------------------------------------------------

function make_folded_forms() {
    $( 'form.folded' ).each( function( i ) {
        var link = $.create( 'a', { 'href': '#', 'class' : 'toggler', 'onclick' : '$( "#' + $( this ).attr( 'id' ) + '" ).toggle();' }, 'Visibilité formulaire' );
        var p = $.create( 'p', {}, link );
        $( p ).insertBefore( $( this ) );
        $( this ).hide();
    } );
}

//-----------------------------------------------------------------

// TODO: mettre avant les actions
function make_table_tooltips() {
    $( 'table.tooltips' ).each( function() {
        // FIXME: colspans dans le thead -> alert( $( this ).attr( 'colspan' ) );
        var tooltipPositions = new Array();
        var tooltipHeaders = new Array();
        var actionPositions = new Array();

        $( this ).children( 'thead' ).children( 'tr' ).children( 'th' ).each( function( i ) {
            var colspan = ( $( this ).attr( 'colspan' ) != undefined ) ? $( this ).attr( 'colspan' ) : 1;
            if( $( this ).hasClass( 'tooltip' ) ) {
                $( this ).remove();
                for( k = 0 ; k < colspan ; k++ )
                    tooltipPositions.push( i + k );
                tooltipHeaders.push( $( this ).html() );
            }
            if( $( this ).hasClass( 'action' ) ) {
                for( k = 0 ; k < colspan ; k++ )
                    actionPositions.push( i + k );
            }
        } );

        // FIXME
        var th = $.create( 'th', { 'class': 'tooltip_table' }, 'Informations complémentaires' )
        $( this ).children( 'thead' ).children( 'tr' ).append( th );

        $( this ).children( 'tbody' ).children( 'tr' ).each( function( i ) {
            var tooltip_table = document.createElement( 'table' );
            tooltip_table.className = 'tooltip';

            $( this ).children( 'td' ).each( function( j ) {
                if( ( thI = jQuery.inArray( j, tooltipPositions ) ) != -1 ) {
                    var tooltip_tr = document.createElement( 'tr' );
                    var tooltip_th = document.createElement( 'th' );
                    $( tooltip_th ).append( tooltipHeaders[thI] );
                    $( tooltip_tr ).append( $( tooltip_th ) );
                    $( tooltip_tr ).append( $( this ).html() );
                    $( tooltip_table ).append( $( tooltip_tr ) );

                    $( this ).remove();
                }
                else if( jQuery.inArray( j, actionPositions ) != -1 ) {
                    $( this ).addClass( 'action' );
                }
            } );

            var tooltip_td = document.createElement( 'td' );
            tooltip_td.className = 'tooltip_table';
            $( tooltip_td ).append( $( tooltip_table ) );
            $( this ).append( $( tooltip_td ) );
        } );
    } );

    //
    $( 'table.tooltips tr td' ).mouseover( function( e ) {
        if( !$( this ).hasClass( 'action' ) ) {
            $( this ).parents( 'tr' ).addClass( 'hover' ); // INFO: IE6
            $( this ).parents( 'tr' ).children( 'td.tooltip_table' ).each( function() {
                $( this ).css( 'left', e.pageX + 5 );
                $( this ).css( 'top', e.pageY + 5 ); // INFO: IE6
                $( this ).css( 'display', 'block' ); // INFO: IE6
            } );
        }
    } );

    //
    $( 'table.tooltips tr td' ).mousemove( function( e ) {
        if( !$( this ).hasClass( 'action' ) ) {
            $( this ).parents( 'tr' ).children( 'td.tooltip_table' ).each( function() {
                $( this ).css( 'left', e.pageX + 5 );
                $( this ).css( 'top', e.pageY + 5 ); // INFO: IE6
            } );
        }
    } );

    //
    $( 'table.tooltips tr td' ).mouseout( function( e ) {
        if( !$( this ).hasClass( 'action' ) ) {
            $( this ).parents( 'tr' ).removeClass( 'hover' ); // INFO: IE6
            $( this ).parents( 'tr' ).children( 'td.tooltip_table' ).each( function() {
                $( this ).css( 'display', 'none' ); // INFO: IE6
            } );
        }
    } );
}

//-----------------------------------------------------------------

// $( document ).ready( function() {
//     make_treemenus( 'http://localhost/webrsa/' ); // FIXME
//     make_table_tooltips();
//     make_folded_forms();
// } );