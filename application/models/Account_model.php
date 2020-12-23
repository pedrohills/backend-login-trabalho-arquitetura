<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_model extends CI_Model{

    public $table = "users";

	public function get($where = NULL){
      if($where != NULL){
        $this->db->where($where);
      }

      $this->db->from($this->table);
      $query = $this->db->get();

      return $query->num_rows() ? $query->result_array() : NULL;

    }
    
    public function create($data) {
        $this->db->insert($this->table, $data);
        return $this->db->affected_rows() ? $this->db->insert_id() : FALSE;
    }
}
