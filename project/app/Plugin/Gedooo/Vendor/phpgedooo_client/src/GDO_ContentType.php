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
 * Classe GDO_ContentType
 * ---------------
 * Un objet de type GDO_ContentType contient les références à un document
 * Ce document a un type MIME et peut se trouver
 *  - a une url donnée
 *  - dans l'objet lui-même sous forme binaire
 *  - dans l'objet lui-même sous forme d'un texte en html.
 *
 * Version 1.0
 */

class GDO_ContentType
{
    public $name;
    public $target;
    public $mimeType;
    public $url;
    public $binary;
    public $text;

    private $mode;

    // }}}
    // {{{ GDO_ContentType ()

    /**
     * Constructeur
     *
     * @param    string      $name      Non du document
     * @param    string      $mimeType  type MIME du document
     * @param    string      $mode      mode d'accés au contenu
     * @param    string      $value     url, valeur binaire ou texte (html), selon le mode
     * @since    1.0
     * @access   public
     */
    public function __construct($target, $name, $mimeType, $mode, $value)
    {
        $this->mode = $mode;
        if ($target != "") {
            $this->target = $target;
        }
        if ($name != "") {
            $this->name = $name;
        }
        $this->mimeType = $mimeType;

        switch ($mode) {
            case "url":
                $this->url = $value;
                break;
            case "binary":
                $this->binary = $value;
                break;
            case "text":
                $this->text = $value;
                break;
        }
    }

    // }}}
    // {{{ getName ()

    /**
     * Renvoi le nom du document
     *
     * @return   string     Le nom du document
     * @since    1.0
     * @access   public
     */
    public function getName()
    {
        return($this->name);
    }


    // }}}
    // {{{ getMimeType ()

    /**
     * Renvoi le type MIME du contenu
     *
     * @return   string     Le type MIME
     * @since    1.0
     * @access   public
     */
    public function getMimeType()
    {
        return($this->mimeType);
    }

    // }}}
    // {{{ getMimeType ()

    /**
     * Renvoi le type MIME du contenu
     *
     * @return   string     Le type MIME
     * @since    1.0
     * @access   public
     */
    public function getContent()
    {

        if (isset($this->url)) {
            return(file_get_contents($this->url));
        }
        if (isset($this->text)) {
            return($this->text);
        }
        if (isset($this->binary)) {
            return($this->binary);
        }

        throw new Exception("Content not available.");
    }

    // }}}
    // {{{ sendToClient ()

    /**
     * Renvoi le contenu vers le client.
     * Si le contenu est spécifié par une URL, il est d'abord récupéré.
     *
     * @since    1.0
     * @access   public
     */
    public function sendToClient()
    {

        //
        //  Accéder à toutes les données avant de lancer le premier header
        //
        $sMimeType = $this->getMimeType();
        $sFileName = $this->getName();
        $bContent  = $this->getContent();

        header("Content-type: $sMimeType");
        header("Content-disposition: attachment; filename=".$sFileName);
        header("Content-length: " . strlen($bContent));

        echo $bContent;
    }


    // }}}
    // {{{ sendToFile ()

   /**
     * Renvoie le contenu vers le fichier spécifié.
     *
     * @since    1.0
     * @access   public
     * @param    string     $sFile      Chemin du fichier où stocker le résultat
     */
    public function sendToFile($sFile)
    {
        file_put_contents($sFile, $this->getContent());
    }
}
