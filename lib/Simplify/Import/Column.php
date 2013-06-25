<?php

class Simplify_Import_Column
{

  protected $index;

  protected $field;

  public function __construct($index, $field)
  {
    $this->index = $index;
    $this->field = $field;
  }

  public function getIndex()
  {
    return $this->index;
  }

  public function getField()
  {
    return $this->field;
  }

  public function onAfterSave()
  {
  }

  public function onBeforeSave()
  {
  }

  public function onCollectTableData(&$data, $row)
  {
    $data[$this->field] = $row[$this->index];
  }

}
