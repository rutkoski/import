<?php

class Simplify_Import_ReaderIterator implements Iterator
{

  public $reader;

  protected $index = - 1;

  public function __construct(Simplify_Import_Reader $reader)
  {
    $this->reader = $reader;
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
