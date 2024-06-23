<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_DB_query_builder extends CI_DB_query_builder
{

    final public function __construct($params)
    {
        parent::__construct($params);
    }

    /**
     * Insert_On_Duplicate_Update_Batch
     *
     * Compiles batch insert strings and runs the queries
     * MODIFIED to do a MySQL 'ON DUPLICATE KEY UPDATE'
     *
     * @access public
     * @param string the table to retrieve the results from
     * @param array an associative array of insert values
     * @return object
     */
    function insert_on_duplicate_update_batch($table = '', $set = NULL)
    {
        if (!is_null($set)) {
            $this->set($set);
        }
        if (count($this->ar_set) == 0) {
            if ($this->db_debug) {
                return $this->display_error('db_must_use_set');
            }
            return FALSE;
        }
        if ($table == '') {
            if (!isset($this->ar_from[0])) {
                if ($this->db_debug) {
                    return $this->display_error('db_must_set_table');
                }
                return FALSE;
            }
            $table = $this->ar_from[0];
        }
        $sql = $this->_insert_on_duplicate_update_batch($this->_protect_identifiers($table, TRUE, NULL, FALSE), array_keys($this->ar_set), array_values($this->ar_set));
        $this->_reset_write();
        return $this->query($sql);
    }

    /**
     * Insert_on_duplicate_update_batch statement
     *
     * Generates a platform-specific insert string from the supplied data
     * MODIFIED to include ON DUPLICATE UPDATE
     *
     * @access public
     * @param string the table name
     * @param array the insert keys
     * @param array the insert values
     * @return string
     */
    private function _insert_on_duplicate_update_batch($table, $keys, $values)
    {
        foreach ($keys as $num => $key) {
            $update_fields[] = $key . '=' . $values[$num];
        }
        return "INSERT INTO " . $table . " (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ") ON DUPLICATE KEY UPDATE " . implode(', ', $update_fields);
    }

}

?>