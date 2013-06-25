<?php

class Simplify_Import_Reader
{

  public $skipRows;

  protected $data;

  public function getRow($index)
  {
    return $this->data[$index];
  }

  public function numRows()
  {
    return count($this->data);
  }

  /**
   *
   * @return Simplify_Import_ReaderIterator
   */
  public function getIterator()
  {
    return new Simplify_Import_ReaderIterator($this);
  }

}
