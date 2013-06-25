<?php

class Simplify_Import_Model
{

  protected $table;

  protected $primaryKey;

  /**
   *
   * @var string[]
   */
  protected $keyColumns;

  /**
   *
   * @var Simplify_Import_Column[]
   */
  protected $columns = array();

  /**
   *
   * @var Simplify_Import_Reader
   */
  public $reader;

  /**
   *
   * @var mixed[]
   */
  protected $data;

  /**
   *
   * @param string $table
   * @param string $primaryKey
   */
  public function __construct($table, $primaryKey, $keyColumns)
  {
    $this->table = $table;
    $this->primaryKey = $primaryKey;
    $this->keyColumns = $keyColumns;
  }

  /**
   *
   * @param Simplify_Import_Column $column
   * @return Simplify_Import_Column
   */
  public function addColumn(Simplify_Import_Column $column)
  {
    $this->columns[] = $column;
    return $column;
  }

  /**
   *
   * @return string
   */
  public function getTable()
  {
    return $this->table;
  }

  /**
   *
   * @return string
   */
  public function getPrimaryKey()
  {
    return $this->primaryKey;
  }

  /**
   *
   * @return string[]
   */
  public function getKeyColumns()
  {
    return (array) $this->keyColumns;
  }

  /**
   *
   * @return Simplify_Import_Column[]
   */
  public function getColumns()
  {
    return $this->columns;
  }

  /**
   *
   * @return mixed[]
   */
  public function save()
  {
    $this->data = array();

    foreach ($this->columns as $column) {
      $column->onBeforeSave();
    }

    $it = $this->reader->getIterator();

    $it->rewind();
    while ($it->valid()) {
      $row = $it->current();

      $_row = array();

      foreach ($this->columns as $column) {
        $column->onCollectTableData($_row, $row);
      }

      if ($this->exists($_row)) {
        $this->update($_row);
      } else {
        $this->insert($_row);
      }

      $this->data[$this->getKeyColumnValue($_row)] = $_row;

      $it->next();
    }

    foreach ($this->columns as $column) {
      $column->onAfterSave();
    }

    return $this->data;
  }

  /**
   *
   * @param unknown_type $row
   * @return string
   */
  protected function getKeyColumnValue($row)
  {
    $key = array();
    foreach ($this->getKeyColumns() as $col) {
      $key[] = $row[$col];
    }
    return implode('-', $key);
  }

  /**
   *
   * @param unknown_type $row
   * @return boolean
   */
  protected function exists(&$row)
  {
    $data = array();

    $q = s::db()->query()->from($this->table)->select($this->primaryKey);

    foreach ($this->getKeyColumns() as $column) {
      $q->where("{$column} = :{$column}");

      $data[$column] = $row[$column];
    }

    $id = $q->execute($data)->fetchOne();

    if (empty($id)) return false;

    $row[$this->primaryKey] = $id;

    return true;
  }

  /**
   *
   * @param unknown_type $row
   */
  protected function insert(&$row)
  {
    s::db()->insert($this->table, $row)->execute($row);
    $row[$this->primaryKey] = s::db()->lastInsertId();
  }

  /**
   *
   * @param unknown_type $row
   */
  protected function update($row)
  {
    s::db()->update($this->table, $row, "{$this->primaryKey} = :{$this->primaryKey}")->execute($row);
  }

}
