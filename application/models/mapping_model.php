<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mapping_model extends CI_Model
{
	public $table_name = "field_mapping_table";


	public function get_count($sql){
        $db_2 = $this->load->database('default_2_transform_data', TRUE);
		$query = $db_2->query($sql);
		$result = $query->row_array();
		return  $result["count(*)"];
	}

	public function get_content($sql){
        $db_2 = $this->load->database('default_2_transform_data', TRUE);

        $query = $db_2->query($sql);
		$result = $query->result_array();
		return $result;
	}

	function get($id) {
        $db_2 = $this->load->database('default_2_transform_data', TRUE);

        $db_2->where('id', $id);
		$query = $db_2->get($this->table_name);
		$result = $query->result_array();

		if(count($result) > 0) {
			return $result[0];
		}
		else {
			return false;
		}
	}

	function save($data) {
        $db_2 = $this->load->database('default_2_transform_data', TRUE);

        $db_2->insert($this->table_name, $data);
		return true;
	}

	function update($id, $data)
	{
        $db_2 = $this->load->database('default_2_transform_data', TRUE);

        $db_2->where('id', $id);
        $db_2->update($this->table_name, $data);
		return true;
	}

	function delete($id){
        $db_2 = $this->load->database('default_2_transform_data', TRUE);

        $db_2->where('id', $id);
        $db_2->delete($this->table_name);
		return true;
	}
}

