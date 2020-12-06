<?php
defined('BASEPATH') or exit('No direct script access allowed');
use Stringy\StaticStringy as S;

class JSON_Model extends CI_Model
{
    public $modelPrefix = 'api/';
    public $name = '';
    public $sqlTable = null;
    public $attrs = [];
    public $belongsTo = []; // column_name => model_name
    public $hasMany = []; // model => column in model
    
    public $itemsPerPage = 30;
    private $currentPage = 1;

    private $modelMap = null;
    private $joinArray = null;
    private $includes = null;
    private $orderArr = null;

    public function __construct()
    {
        parent::__construct();
        $this->joins = [];
        $this->orderArr = [];
        $this->modelMap = null;

        $this->load->helper('inflector');
        $this->load->database();
    }

    public function getSQLTable()
    {
        if (!$this->sqlTable) {
            return $this->name;
        }
        return $this->sqlTable;
    }

    public function getIncludes($data = [])
    {
        $array = [];
        if ($this->includes && $data) {
            foreach ($this->includes as $include) {
                $includes = explode('.', $include);
                $field = $includes[0];
                if (isset($this->belongsTo[$field])) {
                    $modelName = $this->belongsTo[$field]['targetModel'];
                    $ids = array_map(function ($item) use ($field) {
                        return $item->$field;
                    }, $data);
                    $this->load->model('api/' . $modelName, $modelName);
                    array_shift($includes);

                    $result = $this->$modelName->getMany(['id' => implode(',', $ids), 'itemsPerPage' => 0, 'include' => $includes]);
                    $result = $this->$modelName->getRelationships($result['data']);
                    $currentIncludes = [];
                    if ($includes) {
                        $currentIncludes = $this->$modelName->getIncludes($result);
                    }

                    foreach ($result as &$value) {
                        $value = $this->$modelName->serialize($value);
                    }
                    
                    $array = array_merge($array, $result, $currentIncludes);
                }
            }
        }
        return $array;
    }

    public function getModelMap($alias = null): array
    {
        if ($this->modelMap) {
            return $this->modelMap;
        }

        $modelMap = [];
        $modelMap['table'] = $this->getSQLTable();
        $modelMap['attrs'] = $this->attrs;
        $modelMap['isJoin'] = false;
        if ($alias) {
            $modelMap['alias'] = $alias;
        } else {
            $modelMap['alias'] = $modelMap['table'];
        }
        $belongsTo = [];
        foreach ($this->belongsTo as $key => $value) {
            $targetModel = $value['targetModel'];
            $this->load->model('api/' . $targetModel, $targetModel);
            $belongsTo[$key] = $value;
            $belongsTo[$key]['model'] = $this->$targetModel->getModelMap($key);
        }
        $hasMany = [];
        foreach ($this->hasMany as $key => $value) {
            $targetModel = $value['targetModel'];
            $this->load->model('api/' . $targetModel, $targetModel);
            $hasMany[$key] = $value;
            $hasMany[$key]['model'] = $this->$targetModel->getModelMap(singular($key));
        }

        $modelMap['belongsTo'] = $belongsTo;
        $modelMap['hasMany'] = $hasMany;

        $this->modelMap = $modelMap;
        return $modelMap;
    }

    private function getJoinArray(): array
    {
        if ($this->joinArray) {
            return $this->joinArray;
        }
        $models = $this->getModelMap();
        $this->joinArray = $this->getNestedModels($models);
        return $this->joinArray;
    }

    private function getNestedModels($model): array
    {
        $array = [];
        foreach ($model['belongsTo'] as $key => $value) {
            $array[$key] = [
                'alias'  => $value['model']['alias'],
                'isJoin' => false,
                'join'   => [
                    "{$value['model']['table']} {$value['model']['alias']}",
                    "{$value['model']['alias']}.id = {$model['alias']}.{$key}",
                ],
            ];
            $array = array_merge($array, $this->getNestedModels($value['model']));
        }

        foreach ($model['hasMany'] as $key => $value) {
            $array[$value['model']['alias']] = [
                'alias'  => $value['model']['alias'],
                'isJoin' => false,
                'join'   => [
                    "{$value['model']['table']} {$value['model']['alias']}",
                    "{$value['model']['alias']}.{$value['joinColumn']} = {$model['alias']}.id",
                ],
            ];
            $array = array_merge($array, $this->getNestedModels($value['model']));
        }
        return $array;
    }

    private function setJoin($parts = [])
    {
        array_pop($parts);
        $joins = $this->joinArray;

        for ($i = 0; $i < count($parts); $i++) {
            if (isset($this->joinArray[$parts[$i]])) {
                $this->joinArray[$parts[$i]]['isJoin'] = true;
            }
        }
    }

    private function getField($field): string
    {
        $field = str_replace('_', '.', $field);
        $parts = explode('.', $field);
        if (count($parts) > 1) {
            $joinArray = $this->getJoinArray();
            $tableName = $joinArray[$parts[count($parts) - 2]]['alias'];
            $field = $parts[count($parts) - 1];
            $this->setJoin($parts);
        } else {
            $tableName = $this->getSQLTable();
            $field = $parts[0];
        }

        return "{$tableName}.{$field}";
    }

    private function getSelectFields(): array
    {
        $models = $this->getModelMap();
        $table = $this->getSQLTable();

        $belongsToFields = array_map(function ($field) use ($table) {
            return $table . '.' . $field;
        }, array_keys($models['belongsTo']));

        $attrs = array_map(function ($field) use ($table) {
            return $table . '.' . $field;
        }, $models['attrs']);

        $fields = array_merge([$table . '.id'], $belongsToFields, $attrs);

        return $fields;
    }

    private function parseParams($params)
    {
        if (count($params) > 0) {
            foreach ($params as $param => $value) {
                if ($param == 'query') {
                    $this->db->where($this->getQuery($value));
                } else if ($param == 'order') {
                    foreach ($value as $order => $direction) {
                        $this->orderArr[] = [$this->getField($order), $direction];
                    }
                } else if ($param == 'itemsPerPage') {
                    $this->itemsPerPage = $value;
                } else if ($param == 'page') {
                    $this->currentPage = $value;
                } else if ($param == 'include') {
                    $this->includes = $value;
                } else if ($param == 'between') {
                    foreach ($value as $nameCol => $val) {
                        $values = explode(',', $val);
                        $this->db->where($this->getField($nameCol) . ' >= ', $values[0]);
                        $this->db->where($this->getField($nameCol) . ' <= ', $values[1]);
                    }
                } else if (count(explode(',', $value)) > 1) {
                    $this->db->where_in($this->getField($param), explode(',', $value));
                } else if (strtolower($value) == 'isnull') {
                    $this->db->where($this->getField($param) . ' IS NULL');
                } else if (strtolower($value) == 'isnotnull') {
                    $this->db->where($this->getField($param) . ' IS NOT NULL');
                } else {
                    $field = $this->getField($param);
                    if (count(explode('|', $value)) >= 2) {
                        $values = explode('|', $value);
                        if ($values[0] == 'NOT IN') {
                            $this->db->where($field . ' NOT IN (SELECT ' . $values[1] . '.' . $values[2] . ' FROM ' . $values[1] . ')');
                        } else if (strtolower($values[0]) == 'like') {
                            $this->db->where("{$field} LIKE '%{$values[1]}%'");
                        } else {
                            $this->db->where($field . ' ' . $values[0], $values[1]);
                        }
                    } else {
                        $this->db->where($field, $value);
                    }
                }
            }
        }
    }

    public function getMany($params = [])
    {
        $table = $this->getSQLTable();
        $this->db->start_cache();

        $this->db->from($table);
        $this->db->select($this->getSelectFields());

        $this->parseParams($params);

        $joins = $this->getJoinArray();
        foreach ($joins as $key => $value) {
            if ($value['isJoin']) {
                $this->db->join($value['join'][0], $value['join'][1], 'left');
            }
        }

        $this->db->stop_cache();
        $totalItems = $this->db->count_all_results();

        foreach ($this->orderArr as $order) {
            $this->db->order_by($order[0], $order[1]);
        }

        $itemsPerPage = (int) $this->itemsPerPage;
        $currentPage = (int) $this->currentPage;

        if ($itemsPerPage == 0) {
            $itemsPerPage = $totalItems;
        }

        if ($totalItems > 0) {
            if ($itemsPerPage > $totalItems) {
                $currentPage = 1;
            }

            $offset = ($currentPage - 1) * $itemsPerPage;
            $this->db->limit($itemsPerPage, $offset);
        }

        $result_array = $this->db->get()->result();
        $this->db->flush_cache();

        $data = [
            'data' => $result_array,
            'meta' => [
                'currentPage'  => $currentPage,
                'totalItems'   => $totalItems,
                'itemsPerPage' => $itemsPerPage,
            ],
        ];

        return $data;
    }

    public function getOne($id, $params = [])
    {
        $this->db->select($this->getSelectFields());
        $this->parseParams($params);
        $joins = $this->getJoinArray();
        foreach ($joins as $key => $value) {
            if ($value['isJoin']) {
                $this->db->join($value['join'][0], $value['join'][1], 'left');
            }
        }

        $this->db->where(['id' => $id]);
        $row = $this->db->get($this->getSQLTable())->row();

        return [
            'data' => $row,
            'meta' => [],
        ];
    }

    public function getRelationships($data = [])
    {
        $models = $this->getModelMap();
        if (count($models['hasMany']) > 0 && count($data) > 0) {
            $ids = array_map(function ($item) {
                return $item->id;
            }, $data);

            $relationships = [];
            foreach ($models['hasMany'] as $key => $value) {
                $this->db->select(['id', "{$value['joinColumn']} joinColumn"]);
                $this->db->where_in("{$value['joinColumn']}", $ids);
                $values = $this->db->get($value['model']['table'])->result();

                if (count($values) > 0) {
                    foreach ($data as &$row) {
                        foreach ($values as $relation) {
                            if ($row->id == $relation->joinColumn) {
                                $row->$key[] = $relation->id;
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }

    public function create($data)
    {
        $data = $this->beforeSaveInDB($data, 0);

        $this->db->insert($this->getSQLTable(), $data);
        $id = $this->db->insert_id();

        $data = $this->afterSaveInDB($data, $id);
        return $id;
    }

    public function update($data, $id)
    {
        $data = $this->beforeSaveInDB($data, $id);

        $this->db->set($data);
        $this->db->where('id', $id);
        $this->db->update($this->getSQLTable());

        $this->afterSaveInDB($data, $id);
    }

    // public function prepaireParams($params)
    // {
    //     return $params;
    // }

    public function beforeSaveInDB($data, $id)
    {
        return $data;
    }

    public function afterSaveInDB($data, $id)
    {
        return $data;
    }

    private function serializeBelongsTo($model, $value)
    {
        return [
            'data' => [
                'id'   => $value,
                'type' => (string) S::dasherize($model['targetModel']),
            ],
        ];
    }

    private function serializeHasMany($model, $values)
    {
        $type = (string) S::dasherize(plural($model['targetModel']));

        return [
            'data' => array_map(function ($item) use ($type) {
                return [
                    'id'   => $item,
                    'type' => $type,
                ];
            }, $values),
        ];
    }

    public function serialize($item)
    {
        $model = $this->getModelMap();
        $arr = [];

        $arr['id'] = $item->id;
        $arr['type'] = (string) S::dasherize($this->name);
        foreach ($item as $key => $value) {
            $dasherizedKey = (string) S::dasherize($key);
            if (in_array($key, $model['attrs'])) {
                $arr['attributes'][$dasherizedKey] = $value;
            } else if (array_key_exists($key, $model['belongsTo']) && (int) $value > 0) {
                $arr['relationships'][$dasherizedKey] = $this->serializeBelongsTo($model['belongsTo'][$key], $value);
            } else if (array_key_exists($key, $model['hasMany'])) {
                $arr['relationships'][plural($dasherizedKey)] = $this->serializeHasMany($model['hasMany'][$key], $value);
            }
        }

        return $arr;
    }

    public function deserialize($item)
    {
        $object = [];
        if (isset($item->attributes) && count($this->attrs) > 0) {
            $attributes = (array) $item->attributes;
            foreach ($this->attrs as $column) {
                $attribute = (string) S::dasherize($column);
                if (isset($attributes[$attribute])) {
                    //echo $column . "  -  " . $attributes[$attribute] . " | ";
                    $object[$column] = $attributes[$attribute];
                }
            }
        }

        if (isset($item->relationships) && count($this->belongsTo) > 0) {
            $relationships = (array) $item->relationships;

            foreach ($this->belongsTo as $column => $value) {

                $relationship = (string) S::dasherize($column);

                if (isset($relationships[$relationship])) {
                    if ($relationships[$relationship]->data == null) {
                        $object[$column] = 0;
                    } else {
                        $object[$column] = $relationships[$relationship]->data->id;
                    }
                }
            }

        }

        return $object;
    }

    // query

    private function getQuery($filter)
    {
        $array = [];
        foreach ($filter as $item) {
            if (is_array($item)) {
                $array[] = $this->parseArray($item);
            } else {
                $array[] = $this->parseString($item);
            }
        }

        $queryString = '';
        if (count($array) > 0) {
            $queryString = implode(' AND ', $array);
        }

        return $queryString;
    }

    private function parseString($string)
    {
        if (count(explode(':', $string)) > 1) {

            list($field, $value) = explode(':', $string);

            $field = $this->getField($field);
            if (count(explode('|', $value)) > 1) {
                list($operator, $operand) = explode('|', $value);
            } else {
                $operator = $value;
                $operand = null;
            }

            if ($operand !== null) {
                $str = '';
                if (strtolower($operator) == 'in') {
                    $str = "IN({$operand})";
                } else if (strtolower($operator) == 'like') {
                    $str = "LIKE '%{$operand}%'";
                } else {
                    $str = "{$operator} '{$operand}'";
                }
                return "{$field} {$str}";
            } else {
                if (strtolower($operator) == 'isnull') {
                    return "{$field} IS NULL";
                } else if (strtolower($operator) == 'isnotnull') {
                    return "{$field} IS NOT NULL";
                }
                return "{$field} = '{$operator}'";
            }
        }

        return;
    }

    private function parseArray($array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array = [];
                foreach ($value as $item) {
                    if (is_array($item)) {
                        $array[] = $this->parseArray($item);
                    } else {
                        $str = $this->parseString($item);
                        if ($str) {
                            $array[] = $str;
                        }
                    }
                }

                if (strtolower($key) == 'or') {
                    if (count($array) > 1) {
                        $values = implode(' OR ', $array);
                        return "({$values})";
                    } else {
                        return $array[0];
                    }
                } else {
                    if (count($array) > 1) {
                        $values = implode(' AND ', $array);
                        return "({$values})";
                    } else {
                        return $array[0];
                    }
                }
            }
        }
    }

}
