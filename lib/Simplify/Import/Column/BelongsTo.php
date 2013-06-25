<?php

class Simplify_Import_Column_BelongsTo extends Simplify_Import_Column
{

  /**
   *
   * @var Simplify_Import_Model
   */
  protected $model;

  /**
   *
   * @var mixed[]
   */
  protected $data;

  /**
   *
   * @var string
   */
  protected $references;

  /**
   *
   * @param int $index
   * @param string $field
   * @param Simplify_Import_Model $model
   * @param string $references
   */
  public function __construct($index, $field, Simplify_Import_Model $model, $references = null)
  {
    parent::__construct($index, $field);

    $this->model = $model;
  }

  /**
   *
   * @return Simplify_Import_Model
   */
  public function getModel()
  {
    return $this->model;
  }

  /**
   *
   * @return string
   */
  public function getReferences()
  {
    if (empty($this->references)) {
      $this->references = $this->getModel()->getPrimaryKey();
    }
    return $this->references;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Import_Column::onAfterSave()
   */
  public function onAfterSave()
  {
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Import_Column::onBeforeSave()
   */
  public function onBeforeSave()
  {
    $this->data = $this->model->save();
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_Import_Column::onCollectTableData()
   */
  public function onCollectTableData(&$data, $row)
  {
    $key = $this->getKeyColumnValue($row);

    $id = $this->data[$key][$this->model->getPrimaryKey()];

    $data[$this->field] = $id;
  }

  /**
   *
   * @param unknown_type $row
   * @return string
   */
  protected function getKeyColumnValue($row)
  {
    $key = array();
    foreach ((array) $this->index as $col) {
      $key[] = $row[$col];
    }
    return implode('-', $key);
  }

}
