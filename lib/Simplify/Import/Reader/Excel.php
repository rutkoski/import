<?php

class Simplify_Import_Reader_Excel extends Simplify_Import_Reader
{

  /**
   *
   * @var PHPExcel
   */
  protected $xls;

  protected $sheetIndex;

  public function __construct($file, $sheetIndex = 0)
  {
    $this->sheetIndex = $sheetIndex;
    $this->load($file);
  }

  public function load($file)
  {
    $this->xls = PHPExcel_IOFactory::load($file);
  }

  public function getSheet()
  {
    return $this->xls->getSheet($this->sheetIndex);
  }

  public function getRow($index)
  {
    $index++;

    $rowIterator = $this->getSheet()->getRowIterator($index);

    $row = $rowIterator->current();

    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);

    $_row = array();

    foreach ($cellIterator as $cell) {
      if (! is_null($cell)) {
        $value = $cell->getCalculatedValue();

        $_row[] = $value;
      }
    }

    unset($rowIterator, $cellIterator);

    return $_row;
  }

  public function numRows()
  {
    return $this->getSheet()->getHighestRow();
  }

  /**
   *
   * @return Simplify_Import_ReaderIterator
   */
  public function getIterator()
  {
    return new Simplify_Import_Reader_ExcelIterator($this);
  }

}
