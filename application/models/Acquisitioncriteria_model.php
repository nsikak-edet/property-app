<?php

class Acquisitioncriteria_model extends CI_Model
{

	private $acquisition_table = "acquisition_criteria";

	function __construct()
	{
		parent::__construct();
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
	}

	function get_criteria($criteria_id)
	{
		$this->db->select("acquisition_criteria.*, contacts.first_name, contacts.last_name,
		acquisition_criteria.tenant_name, acquisition_criteria.states,
		 acquisition_criteria.landlord_responsibilities,
		 acquisition_criteria.contact_id as contact_id");
		$this->db->from("acquisition_criteria");
		$this->db->join('contacts', 'contacts.contact_id=acquisition_criteria.contact_id', 'left');
		$this->db->where('criteria_id', $criteria_id);
		$this->db->limit(1);

		$query = $this->db->get();
		$result = $query->result();

		return (sizeof($result) > 0) ? $result[0] : null;
	}

	function get_criteria_by_contact($contact_id)
	{
		$this->db->select("acquisition_criteria.*, contacts.first_name, contacts.last_name,
		acquisition_criteria.tenant_name, acquisition_criteria.states,
		 acquisition_criteria.landlord_responsibilities,
		 acquisition_criteria.contact_id as contact_id");
		$this->db->from("acquisition_criteria");
		$this->db->join('contacts', 'contacts.contact_id=acquisition_criteria.contact_id', 'left');
		$this->db->where('acquisition_criteria.contact_id', $contact_id);

		$query = $this->db->get();
		$result = $query->result();

		return $result;
	}

	function get_criteria_by_company($company_id)
	{
		$this->db->select("acquisition_criteria.*, contacts.first_name, contacts.last_name,
		acquisition_criteria.tenant_name, acquisition_criteria.states,
		 acquisition_criteria.landlord_responsibilities,
		 acquisition_criteria.contact_id as contact_id");
		$this->db->from("acquisition_criteria");
		$this->db->join('contacts', 'contacts.contact_id=acquisition_criteria.contact_id', 'left');
		$this->db->join('companies', 'companies.company_id=contacts.company_id', 'left');
		$this->db->where('companies.company_id', $company_id);

		$query = $this->db->get();
		$result = $query->result();

		return $result;
	}

	function add_criteria($params)
	{
		$this->db->insert($this->acquisition_table, $params);
		return $this->db->insert_id();
	}

	function has_criteria($contact_id){
		$this->db->select("*");
		$this->db->from("acquisition_criteria");
		$this->db->join('contacts', 'contacts.contact_id=acquisition_criteria.contact_id', 'left');
		$this->db->where('acquisition_criteria.contact_id', $contact_id);
		$this->db->limit(1);

		$query = $this->db->get();
		$result = $query->result();

		return (sizeof($result) > 0);
	}

	function update($criteria_id, $params)
	{
		$this->db->where('criteria_id', $criteria_id);
		return $this->db->update($this->acquisition_table, $params);
	}

	function delete($criteria_id)
	{
		$this->db->delete($this->acquisition_table, array('criteria_id' => $criteria_id));
	}
}
