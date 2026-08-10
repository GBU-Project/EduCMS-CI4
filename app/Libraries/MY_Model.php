<?php

/**
 * EduCMS legacy model base class (port from application/core/MY_Model.php).
 * Global namespace; concrete models in app/Models extend it and inherit the
 * CI3-style query API backed by the compat CI_DB wrapper.
 */
#[AllowDynamicProperties]
class MY_Model extends CI_Model {

    public $table = ''; // Must be public: accessed externally by Admin_CRUD_Controller and crud_helper (e.g. generate_unique_slug)
    protected $primary_key = 'id';
    protected $use_soft_delete = TRUE;

    public function __construct() {
        parent::__construct();
        if (!isset($this->db)) {
            $this->load->database();
        }
    }

    /**
     * Get all active records (not soft-deleted)
     */
    public function get_all($where = array(), $limit = NULL, $offset = NULL) {
        if ($this->use_soft_delete) {
            $this->db->where($this->table . '.deleted_at', NULL);
        }
        if (!empty($where)) {
            $this->db->where($where);
        }
        if ($limit !== NULL) {
            $this->db->limit($limit, $offset);
        }
        return $this->db->get($this->table)->result();
    }

    /**
     * Get single record by columns
     */
    public function get_by($column, $value) {
        if ($this->use_soft_delete) {
            $this->db->where($this->table . '.deleted_at', NULL);
        }
        $this->db->where($column, $value);
        return $this->db->get($this->table)->row();
    }

    /**
     * Get all records including soft-deleted ones
     */
    public function get_with_trashed($where = array(), $limit = NULL, $offset = NULL) {
        if (!empty($where)) {
            $this->db->where($where);
        }
        if ($limit !== NULL) {
            $this->db->limit($limit, $offset);
        }
        return $this->db->get($this->table)->result();
    }

    /**
     * Insert a new record
     */
    public function insert($data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update an existing record
     */
    public function update($id, $data) {
        $this->db->where($this->primary_key, $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete a record (Soft delete if enabled, otherwise hard delete)
     */
    public function delete($id, $deleted_by = NULL) {
        $this->db->where($this->primary_key, $id);

        if ($this->use_soft_delete) {
            return $this->db->update($this->table, array(
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => $deleted_by
            ));
        } else {
            return $this->db->delete($this->table);
        }
    }

    /**
     * Restore a soft-deleted record
     */
    public function restore($id) {
        if ($this->use_soft_delete) {
            $this->db->where($this->primary_key, $id);
            return $this->db->update($this->table, array(
                'deleted_at' => NULL,
                'deleted_by' => NULL
            ));
        }
        return FALSE;
    }

    /**
     * Permanently remove a record from the database
     */
    public function force_delete($id) {
        $this->db->where($this->primary_key, $id);
        return $this->db->delete($this->table);
    }

    /**
     * Count active records
     */
    public function count_all($where = array()) {
        if ($this->use_soft_delete) {
            $this->db->where($this->table . '.deleted_at', NULL);
        }
        if (!empty($where)) {
            $this->db->where($where);
        }
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    /**
     * Get paginated, searched, and sorted records
     */
    public function get_paged($where = array(), $search = '', $search_fields = array(), $order_by = NULL, $limit = NULL, $offset = NULL) {
        if ($this->use_soft_delete && !isset($where[$this->table . '.deleted_at !=']) && !isset($where['deleted_at !='])) {
            $this->db->where($this->table . '.deleted_at', NULL);
        }
        if (!empty($where)) {
            $this->db->where($where);
        }
        if (!empty($search) && !empty($search_fields)) {
            $this->db->group_start();
            foreach ($search_fields as $i => $field) {
                if ($i === 0) {
                    $this->db->like($field, $search);
                } else {
                    $this->db->or_like($field, $search);
                }
            }
            $this->db->group_end();
        }
        if (!empty($order_by)) {
            if (is_array($order_by)) {
                foreach ($order_by as $field => $dir) {
                    $this->db->order_by($field, $dir);
                }
            } else {
                $this->db->order_by($order_by);
            }
        } else {
            $this->db->order_by($this->table . '.' . $this->primary_key, 'DESC');
        }
        if ($limit !== NULL) {
            $this->db->limit($limit, $offset);
        }
        return $this->db->get($this->table)->result();
    }
}
