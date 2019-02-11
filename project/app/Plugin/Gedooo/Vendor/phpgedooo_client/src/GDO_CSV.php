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
 * Classe GDO_CSV
 * ---------------
 * Un objet de type GDO_CSV gère les fichiers au format CSV
 * et les convertit en iteration pour la fusion.
 *
 * Version 1.0
 */

class GDO_CSV
{

    public $iterationName;
    public $column2Field;
    public $column2Type;
    public $CSVFileName;
    public $fieldSeparator;
    public $stringDelimiter;
    public $encoding;
    public $constantValues;
    public $constantTypes;

    public function __construct($iterationName)
    {
          $this->iterationName = $iterationName;
          $this->column2Field = array();
          $this->constantValues = array();
          $this->constantTypes = array();
    }

    public function setCSVFile($filename, $fieldSeparator, $stringDelimiter, $encoding)
    {
          $this->CSVFileName = $filename;
          $this->fieldSeparator = $fieldSeparator;
          $this->stringDelimiter = $stringDelimiter;
          $this->encoding = strtolower($encoding);
    }

    public function mapField($columnName, $fieldName, $type = "default")
    {
        if (trim($type) == "") {
            $type = "vide";
        }
          $this->column2Field[strtolower(trim($columnName))] = trim($fieldName);
          $this->column2Type[strtolower(trim($columnName))] = strtolower(trim($type));
    }


// Si les noms de colonnes ne correspondent pas au noms de champs
// un fichier de mapping peut etre utilisé...

    public function setMapFile($filename)
    {

        if ($filename == "") {
            return;
        }

          $handle = fopen($filename, 'r');
        if ($handle == null) {
        // Couldn't open/read from CSV file.
            return;
        }

        while (($data = fgetcsv($handle, 1000, ";", "\"")) !== false) {
            $this->mapField($data[0], $data[1], $data[2]);
        }
    }

// .. ou bien un tableau.

    public function setMapArray($mapArray)
    {
        $this->column2Field = $mapArray;
    }

// Ajout d'une constante à répéter à chaque itération
//

    public function addConstant($name, $value, $type = "string")
    {
        if ($type ==  "") {
            $type = "string";
        }
        $this->constantValues[$name] = $value;
        $this->constantTypes[$name] = $type;
    }

    public function getIteration()
    {
          $handle = fopen($this->CSVFileName, 'r');

        if ($handle == null || ($data = fgetcsv(
            $handle,
            5000,
            $this->fieldSeparator,
            $this->stringDelimiter
        )) === false) {
        // Couldn't open/read from CSV file.
            return null;
        }
        //
        // Getting columnNames
        //
           $names = array();
        foreach ($data as $field) {
            $names[] = strtolower(trim($field));
        }

            // Setting columns indexes
            $index = array();
            $iCol = 0;
        foreach ($names as $name) {
            $index[$name] = $iCol;
            $iCol++;
        }

            // Setting default map, if necessary.
        if (count($this->column2Field) ==0) {
            foreach ($names as $name) {
                $this->mapField($name, $name);
            }
        }

            // Creating iteration

            $oIteration = new GDO_IterationType($this->iterationName);
        while (($data = fgetcsv(
            $handle,
            5000,
            $this->fieldSeparator,
            $this->stringDelimiter
        )) !== false) {
            $oPart = new GDO_PartType();
            foreach ($this->constantValues as $name => $value) {
                if ($this->encoding != "utf8") {
                    $value = utf8_encode($value);
                }
                  $oPart->addElement(new GDO_FieldType($name, $value, $this->constantTypes[$name]));
            }
            foreach ($this->column2Field as $column => $field) {
                if (!isset($this->constantValue[$field])) {
                    $value = $data[$index[$column]];
                    if ($this->encoding != "utf8") {
                        $value = utf8_encode($value);
                    }

                    $type = $this->column2Type[$column];
                    $oPart->addElement(new GDO_FieldType($field, $value, $type));
                }
            }
            $oIteration->addPart($oPart);
        }

            return $oIteration;
    }
}
