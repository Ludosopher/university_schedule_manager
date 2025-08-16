<?php

namespace App\DocExporters\OneTable;

use App\ClassPeriod;

class DocExporter
{
    protected function getWriter($data) {
        
        $class_periods = ClassPeriod::get();
        $class_periods = array_combine(range(1, count($class_periods)), array_values($class_periods->toArray()));
        //------------------------------------------------------
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $headerFontStyle = array('size' => 8);
        $headerParagraphStyle = array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0);
       
        $section = $phpWord->addSection(array(
            'orientation' => 'landscape',
            'marginLeft'   => 600,
            'marginRight'  => 600,
            'marginTop'    => 600,
            'marginBottom' => 600,
        ));

        $section->addTextBreak(1);

        foreach ($data['header_rows'] as $row) {
            $section->addText($row['text'], ($row['font_style'] ?? null), ($row['paragraph_style'] ?? null));
        }

        $styleTable = array('borderSize' => 6, 'borderColor' => '999999');
        $headerCellStyle = array('valign' => 'center');
        
        $phpWord->addTableStyle('Schedule', $styleTable);
        
        $table = $section->addTable('Schedule');
        $table->addRow(null, array('tblHeader' => true));
        //$table->addCell(1300, $headerCellStyle)->addText(__('header.period'), $headerFontStyle, $headerParagraphStyle);
                
        foreach ($data['table_header_cells'] as $cell) {
            $table->addCell($cell['width'] ?? 2000, $headerCellStyle)->addText($cell['text'], $headerFontStyle, $headerParagraphStyle);
        }
        foreach ($data['table_content'] as $row) {
            $table->addRow($row['height']);
            foreach ($row['data'] as $cell) {
                $new_cell = $table->addCell($cell['add_cell']['value'], $cell['add_cell']['cell_style']);
                if (isset($cell['add_text'])) {
                    foreach ($cell['add_text'] as $cell_data) {
                        $new_cell->addText($cell_data['text'], ($cell_data['font_style'] ?? null), ($cell_data['paragraph_style'] ?? null));
                    }
                }
            }
        }
           
        return \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    }

    
}