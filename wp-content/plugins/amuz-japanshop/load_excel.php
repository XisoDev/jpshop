<?php

/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2015 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

if (PHP_SAPI == 'cli')
    die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/./Classes/PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("xiso")
    ->setLastModifiedBy("xiso")
    ->setTitle("amuz excel converter")
    ->setSubject("amuz excel converter")
    ->setDescription("xiso@amuz.co.kr")
    ->setKeywords("data converter")
    ->setCategory("amuz excel converter");


// Sheet Active
$objPHPExcel->setActiveSheetIndex(0);


function cellColor($cells,$color){
    global $objPHPExcel;

    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
            'rgb' => $color
        )
    ));
}

function cellAlign($cells, $hor = "center", $ver = "middle"){
    global $objPHPExcel;

    $horizontal = array(
        "center" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        "left" => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
        "right" => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
    );
    $vertical = array(
        "top" => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        "bottom" => PHPExcel_Style_Alignment::VERTICAL_BOTTOM,
        "middle" => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        "justify" => PHPExcel_Style_Alignment::VERTICAL_JUSTIFY,
    );

    $objPHPExcel->getActiveSheet()->getStyle($cells)->getAlignment()->applyFromArray(
        array(
            'horizontal' => $horizontal[$hor],
            'vertical' => $vertical[$ver],
        )
    );
}

function cellFont($cells,$size,$bold = false,$color = "333333",$font = "돋움"){
    global $objPHPExcel;

    $styleArray = array(
        'font'  => array(
            'bold'  => $bold,
            'color' => array('rgb' => $color),
            'size'  => $size,
            'name'  => $font
        ));
    $objPHPExcel->getActiveSheet()->getStyle($cells)->applyFromArray($styleArray);
}

function cellBorder($cells,$color="333333",$type="solid",$position = "allborders"){
    global $objPHPExcel;
    $type_list = array(
        "solid" => PHPExcel_Style_Border::BORDER_THIN,
        "dotted" => PHPExcel_Style_Border::BORDER_DOTTED,
        "dashed" => PHPExcel_Style_Border::BORDER_DASHED,

    );
    if($position == "all") $position = "allborders";
    if($position == "row") $position = "horizontal";
    if($position == "col") $position = "vertical";
    if($position == "cancel") $position = "diagonal";

    $objPHPExcel->getActiveSheet()->getStyle($cells)->applyFromArray(
        array(
            'borders' => array(
                $position => array(
                    'style' => $type_list[$type],
                    'color' => array('rgb' => $color)
                )
            )
        )
    );
}

function cellWidth($column_id,$width = "auto"){
    global $objPHPExcel;
    if(strpos($column_id,":")){
        $column_id = explode(":",$column_id);
        foreach (range($column_id[0], $column_id[1]) as $id) {
            if($width == "auto"){
                $objPHPExcel->getActiveSheet()->getColumnDimension($id)->setAutoSize(true);
            }else{
                $objPHPExcel->getActiveSheet()->getColumnDimension($id)->setWidth($width);
            }

        }
    }else{
        if($width == "auto"){
            $objPHPExcel->getActiveSheet()->getColumnDimension($column_id)->setAutoSize(true);
        }else{
            $objPHPExcel->getActiveSheet()->getColumnDimension($column_id)->setWidth($width);
        }
    }
}
function cellHeight($row_num,$height = -1){
    global $objPHPExcel;

    if(strpos($row_num,":")) {
        $row_num = explode(":", $row_num);
        foreach (range($row_num[0], $row_num[1]) as $num) {
            $objPHPExcel->getActiveSheet()->getRowDimension($num)->setRowHeight($height);
        }
    }else{
        $objPHPExcel->getActiveSheet()->getRowDimension($row_num)->setRowHeight($height);
    }
}


?>