<?php

/**
 * CI3-compatible database wrapper over a CI4 BaseConnection.
 * Query-builder calls accumulate on a shared BaseBuilder which is reset
 * after every terminal operation, mirroring CI3's $this->db chaining.
 */
#[AllowDynamicProperties]
class CI_DB
{
    /** @var \CodeIgniter\Database\BaseConnection */
    protected $connection;

    /** @var \CodeIgniter\Database\BaseBuilder|null */
    protected $builder;

    /** @var CI_DB_result|null */
    public $query;

    public $db_debug = true;

    /** @var array */
    protected $last_error = ['code' => 0, 'message' => ''];

    public function __construct()
    {
        $this->connection = db_connect();
    }

    /**
     * Expose the underlying CI4 connection.
     */
    public function connection()
    {
        return $this->connection;
    }

    protected function _builder()
    {
        if ($this->builder === null) {
            $this->builder = $this->connection->newQuery();
        }

        return $this->builder;
    }

    protected function _resetBuilder(): void
    {
        if ($this->builder !== null) {
            $this->builder->resetQuery();
            $this->builder->from([], true);
        }
    }

    /**
     * Guard a CI4 database operation against exceptions when db_debug is off.
     * The shared builder is always reset after the call.
     */
    protected function _guard(callable $fn)
    {
        try {
            return $fn();
        } catch (\Throwable $e) {
            $this->last_error = $this->connection->error();
            if ($this->db_debug) {
                throw $e;
            }

            return false;
        } finally {
            $this->_resetBuilder();
        }
    }

    // --------------------------------------------------------------------
    // Query Builder (accumulated)
    // --------------------------------------------------------------------

    public function select($select = '*', $escape = null)
    {
        $this->_builder()->select($select);

        return $this;
    }

    public function select_sum($select = '', $alias = '')
    {
        $this->_builder()->selectSum($select, $alias);

        return $this;
    }

    public function from($from, $escape = null)
    {
        $this->_builder()->from($from);

        return $this;
    }

    public function join($table, $cond, $type = '')
    {
        $this->_builder()->join($table, $cond, $type);

        return $this;
    }

    public function where($key, $value = null, $escape = null)
    {
        if (is_array($key) && $value === null) {
            $this->_builder()->where($key);
        } else {
            $this->_builder()->where($key, $value);
        }

        return $this;
    }

    public function or_where($key, $value = null, $escape = null)
    {
        if (is_array($key) && $value === null) {
            $this->_builder()->orWhere($key);
        } else {
            $this->_builder()->orWhere($key, $value);
        }

        return $this;
    }

    public function where_in($key, $values = null, $escape = null)
    {
        $this->_builder()->whereIn($key, $values);

        return $this;
    }

    public function or_where_in($key, $values = null, $escape = null)
    {
        $this->_builder()->orWhereIn($key, $values);

        return $this;
    }

    public function like($field, $match = '', $side = 'both', $escape = null)
    {
        $this->_builder()->like($field, $match, $side);

        return $this;
    }

    public function or_like($field, $match = '', $side = 'both', $escape = null)
    {
        $this->_builder()->orLike($field, $match, $side);

        return $this;
    }

    public function group_start()
    {
        $this->_builder()->groupStart();

        return $this;
    }

    public function or_group_start()
    {
        $this->_builder()->orGroupStart();

        return $this;
    }

    public function group_end()
    {
        $this->_builder()->groupEnd();

        return $this;
    }

    public function order_by($orderby, $direction = '')
    {
        $this->_builder()->orderBy($orderby, $direction);

        return $this;
    }

    public function limit($value, $offset = 0)
    {
        $this->_builder()->limit($value, $offset);

        return $this;
    }

    public function group_by($by)
    {
        $this->_builder()->groupBy($by);

        return $this;
    }

    public function having($key, $value = null, $escape = null)
    {
        if ($value !== null) {
            $this->_builder()->having($key, $value);
        } else {
            $this->_builder()->having($key);
        }

        return $this;
    }

    public function distinct($val = true)
    {
        $this->_builder()->distinct((bool) $val);

        return $this;
    }

    public function set($key, $value = '')
    {
        if (is_array($key)) {
            $this->_builder()->set($key);
        } else {
            $this->_builder()->set($key, $value);
        }

        return $this;
    }

    // --------------------------------------------------------------------
    // Terminal Query Builder Operations
    // --------------------------------------------------------------------

    public function get($table = '', $limit = null, $offset = null)
    {
        return $this->_guard(function () use ($table, $limit, $offset) {
            $b = $this->_builder();
            if ($table !== '' && $table !== null) {
                $b->from($table);
            }
            if ($limit !== null) {
                $b->limit($limit, $offset);
            }
            $result = $b->get();
            $this->query = new CI_DB_result($result);

            return $this->query;
        });
    }

    public function get_where($table = '', $where = null, $limit = null, $offset = null)
    {
        if (is_array($table) && $where === null) {
            $where = $table;
            $table = '';
        }

        return $this->_guard(function () use ($table, $where, $limit, $offset) {
            $b = $this->_builder();
            if ($table !== '' && $table !== null) {
                $b->from($table);
            }
            if (! empty($where)) {
                $b->where($where);
            }
            if ($limit !== null) {
                $b->limit($limit, $offset);
            }
            $result = $b->get();
            $this->query = new CI_DB_result($result);

            return $this->query;
        });
    }

    public function count_all_results($table = '')
    {
        return $this->_guard(function () use ($table) {
            $b = $this->_builder();
            if ($table !== '' && $table !== null) {
                $b->from($table);
            }
            $count = $b->countAllResults();

            return $count;
        });
    }

    public function count_all($table = '')
    {
        if ($table !== '') {
            $this->from($table);
        }

        return $this->count_all_results();
    }

    public function insert($table = '', $data = null)
    {
        return $this->_guard(function () use ($table, $data) {
            $b = $this->_builder();
            if ($table !== '' && $table !== null) {
                $b->from($table);
            }

            return $b->insert($data);
        });
    }

    public function insert_batch($table = '', $data = null)
    {
        return $this->_guard(function () use ($table, $data) {
            $b = $this->_builder();
            if ($table !== '' && $table !== null) {
                $b->from($table);
            }

            return $b->insertBatch($data);
        });
    }

    public function update($table = '', $data = null, $where = null)
    {
        return $this->_guard(function () use ($table, $data, $where) {
            $b = $this->_builder();
            if ($table !== '' && $table !== null) {
                $b->from($table);
            }
            if ($where !== null) {
                $b->where($where);
            }

            return $b->update($data);
        });
    }

    public function delete($table = '', $where = null)
    {
        return $this->_guard(function () use ($table, $where) {
            $b = $this->_builder();
            if ($table !== '' && $table !== null) {
                $b->from($table);
            }
            if ($where !== null) {
                $b->where($where);
            }

            return $b->delete();
        });
    }

    public function empty_table($table = '')
    {
        return $this->_guard(function () use ($table) {
            return $this->connection->table($table)->emptyTable();
        });
    }

    public function truncate($table = '')
    {
        return $this->_guard(function () use ($table) {
            return $this->connection->table($table)->truncate();
        });
    }

    // --------------------------------------------------------------------
    // Raw query + connection-level methods
    // --------------------------------------------------------------------

    public function query($sql, $binds = null)
    {
        return $this->_guard(function () use ($sql, $binds) {
            $result = $this->connection->query($sql, $binds);

            if ($result instanceof \CodeIgniter\Database\ResultInterface) {
                $this->query = new CI_DB_result($result);

                return $this->query;
            }

            return $result;
        });
    }

    public function table_exists($table, $cache = true)
    {
        return $this->connection->tableExists($table, $cache);
    }

    public function field_exists($field, $table)
    {
        return $this->connection->fieldExists($field, $table);
    }

    public function list_fields($table = '')
    {
        return $this->connection->getFieldNames($table);
    }

    public function insert_id()
    {
        return $this->connection->insertID();
    }

    public function affected_rows()
    {
        return $this->connection->affectedRows();
    }

    public function error()
    {
        $error = $this->connection->error();

        return [
            'code'    => $error['code'] ?? 0,
            'message' => $error['message'] ?? '',
        ];
    }

    public function escape($str)
    {
        return $this->connection->escape($str);
    }

    public function escape_str($str, $like = false)
    {
        return $this->connection->escapeString($str, $like);
    }

    public function escape_like_str($str)
    {
        return $this->connection->escapeLikeString($str);
    }

    public function last_query()
    {
        $query = $this->connection->getLastQuery();

        return $query === null ? '' : (string) $query;
    }

    public function platform()
    {
        return $this->connection->getPlatform();
    }

    public function database()
    {
        return $this->connection->getDatabase();
    }

    public function trans_begin($test_mode = false)
    {
        return $this->connection->transBegin($test_mode);
    }

    public function trans_commit()
    {
        return $this->connection->transCommit();
    }

    public function trans_rollback()
    {
        return $this->connection->transRollback();
    }

    public function trans_status()
    {
        return $this->connection->transStatus();
    }

    public function trans_start($test_mode = false)
    {
        $this->connection->transBegin($test_mode);
        $this->connection->transException(true);
    }

    public function trans_complete()
    {
        if ($this->connection->transStatus()) {
            $this->connection->transCommit();
        } else {
            $this->connection->transRollback();
        }
    }
}

/**
 * CI3-compatible query result wrapper.
 */
#[AllowDynamicProperties]
class CI_DB_result
{
    /** @var \CodeIgniter\Database\BaseResult */
    protected $result;

    public function __construct($result)
    {
        $this->result = $result;
    }

    public function result($type = 'object')
    {
        return $this->result->getResult();
    }

    public function result_array()
    {
        return $this->result->getResultArray();
    }

    public function row($n = 0, $type = 'object')
    {
        return $this->result->getRow($n);
    }

    public function row_array($n = 0)
    {
        return $this->result->getRowArray($n);
    }

    public function num_rows()
    {
        return $this->result->getNumRows();
    }

    public function num_fields()
    {
        return $this->result->getFieldCount();
    }

    public function list_fields()
    {
        return $this->result->getFieldNames();
    }

    public function field_data()
    {
        return $this->result->getFieldData();
    }

    public function free_result()
    {
    }
}
