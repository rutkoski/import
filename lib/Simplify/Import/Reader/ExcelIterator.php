<?php

class Simplify_Import_Reader_ExcelIterator extends Simplify_Import_ReaderIterator
{

  /**
   *
   * @var Simplify_Import_Reader_Excel
   */
  public $reader;

  public function __construct(Simplify_Import_Reader_Excel $reader)
  {
    parent::__construct($reader);
  }

  public function current()
  {
    return $this->valid() ? $this->reader->getRow($this->index) : false;
  }

  public function key()
  {
    return $this->index;
  }

  public function next()
  {
    if ($this->reader->numRows()) {
      do {
        $this->index++;
      }
      while (! empty($this->reader->skipRows) && in_array($this->index, $this->reader->skipRows));
    }
  }

  public function rewind()
  {
    $this->index = - 1;

    if ($this->reader->numRows()) {
      do {
        $this->index++;
      }
      while (! empty($this->reader->skipRows) && in_array($this->index, $this->reader->skipRows));
    }
  }

  public function valid()
  {
    return $this->index >= 0 && $this->index < $this->reader->numRows();
  }

}
