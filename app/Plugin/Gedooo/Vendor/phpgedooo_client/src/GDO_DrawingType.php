<?php


// +----------------------------------------------------------------------+
// | PHP Version 5.3                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2004 Lille Metropole Communaute Urbaine (LMCU)         |
// +----------------------------------------------------------------------+
// | This file is part of GED'OOo.                                        |
// |                                                                      |
// | GED'OOo is free software; you can redistribute it and/or modify      |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation; either version 2 of the License, or    |
// | (at your option) any later version.                                  |
// |                                                                      |
// | Tiny is distributed in the hope that it will be useful,              |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with GED'OOo; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA        |
// +----------------------------------------------------------------------+
// | Authors:Philippe Allart                                              |
// +----------------------------------------------------------------------+
//

/*
 * Classe GDO_DrawingType
 * ---------------
 * Un objet de type Drawing permet de spécifier
 * le nom d'un dessin Draw inséré dans le documents
 * et la liste des figures graphique dont il
 * faut changer le style
 *
 * Version 1.0
 */


class GDO_DrawingType
{

    public $name;
    public $shapes = array();

    // }}}
    // {{{ GDO_DrawingType ()

    /**
     * Constructeur
     *
     * @param    string      $name Non du dessin
     * @since    1.0
     * @access   public
     */

    public function __construct($name)
    {
        $this->name = $name;
    }

    // }}}
    // {{{ addShape ()

    /**
     * Ajoute un objet de type GDO_ShapeType
     *
     * @param    object     l'objet GDO_ShapeType à ajouter à l'objet
     * @since    1.0
     * @access   public
     */

    public function addShape($aShape)
    {

        $this->shapes[] = $aShape;
    }
}
