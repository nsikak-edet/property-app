<?php

class Contact_model extends CI_Model {

    private $contact_table = "contacts";
    private $address_table = "addresses";
    private $phone_table = "phones";
    private $ci;

    function __construct() {
        parent::__construct();
        $this->ci = & get_instance();
        $this->ci->load->model("Company_model");
        $this->ci->load->model("Property_model");
        $this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
    }

    function get_contact($contact_id, $excludeProperty = false) {
        $this->db->select("contacts.*, concat_ws(' ',contacts.first_name, contacts.middle_name, contacts.last_name) as name, companies.name as company_name");
        $this->db->join("companies", "companies.company_id=contacts.company_id", "left");
        $contact = $this->db->get_where($this->contact_table, array('contact_id' => $contact_id))->row_array();
        if (is_array($contact) && sizeof($contact) > 0) {
            $contact['phones'] = $this->get_phones($contact_id);
            $contact['addresses'] = $this->get_addresses($contact_id);

            if ($excludeProperty == false) {
                $contact['properties'] = $this->ci->Property_model->get_properties_by_contact($contact_id);
            }
        }

        return $contact;
    }

    function get_contact_by_name($name) {
    	if(isset($name['first_name'])){
			$this->db->where('first_name', trim($name['first_name']));
		}

    	if(isset($name['middle_name'])){
			$this->db->where('middle_name', trim($name['middle_name']));
		}

    	if(isset($name['last_name'])){
			$this->db->where('last_name', trim($name['last_name']));
		}

        $contact = $this->db->get_where($this->contact_table)->row_array();
        return $contact;
    }

    function get_contact_by_email($email) {
        $this->db->where('email', trim($email));
        $contact = $this->db->get_where($this->contact_table)->row_array();

        return $contact;
    }

    function search_contact_by_name($name) {
    	$name= urldecode($name);
    	$nameWithNoSpace = str_replace(' ','',$name);
		$this->db->like("concat_ws('',trim(first_name),trim(middle_name),trim(last_name)) ",$nameWithNoSpace);
		$this->db->or_like("concat_ws(' ',trim(first_name),trim(middle_name),trim(last_name)) ",$name);
        $this->db->join('phones', "phones.entity_type='" . EntityTypes::CONTACT . "'AND  contacts.contact_id=phones.entity_id", "left");
        $this->db->group_by('contacts.contact_id');
        $contact = $this->db->get_where($this->contact_table)->result();

        return $contact;
    }

    function pop_contact_by_name($name) {
        $contact = $this->get_contact_by_name(trim($name));
        if (sizeof($contact) > 0) {
            return $contact['contact_id'];
        } else {
            $company_id = $this->add_contact(['name' => $name]);
            return $company_id;
        }
    }

    public function save_bulk_contact($contacts) {
        $this->db->trans_start();
        foreach ($contacts as $contact) {
            $storedContact = $this->get_contact_by_name($contact['name']);
            $company = $this->ci->Company_model->get_company_by_name(trim($contact['company']));

            //insert or update
            if (($storedContact == null)) {
                $contact_data = [
                    'first_name' => $contact['first_name'],
                    'middle_name' => $contact['middle_name'],
                    'last_name' => $contact['last_name'],
                    'email' => $contact['email']
                ];

                if (is_array($company) && sizeof($company) > 0) {
                    $contact_data['company_id'] = $company['company_id'];
                } else if (strlen($contact['company']) > 0) {
                    $company_id = $this->ci->Company_model->add_company(['name' => $contact['company']]);
                    $contact_data['company_id'] = $company_id;
                }
                $contact_id = $this->add_contact($contact_data);

                //insert address
                $address = [
                    'address' => $contact['address'],
                    'state' => $contact['state'],
                    'city' => $contact['city'],
                    'zip_code' => $contact['zip_code'],
                    'entity_id' => $contact_id,
                    'entity_type' => EntityTypes::CONTACT,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert($this->address_table, $address);

                //insert phone numbers
                if (strlen($contact['phone_1']) > 0) {
                    $this->db->insert($this->phone_table, [
                        'phone' => $contact['phone_1'],
                        'entity_id' => $contact_id,
                        'entity_type' => EntityTypes::CONTACT,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }

                if (strlen($contact['phone_2']) > 0) {
                    $this->db->insert($this->phone_table, [
                        'phone' => $contact['phone_2'],
                        'entity_id' => $contact_id,
                        'entity_type' => EntityTypes::CONTACT,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
        $this->db->trans_complete();
    }

    public function get_phones($contact_id) {
        $this->db->where('entity_type', EntityTypes::CONTACT);
        $this->db->where('entity_id', $contact_id);
        return $this->db->get($this->phone_table)->result();
    }

    public function get_addresses($contact_id) {
        $this->db->where('entity_type', EntityTypes::CONTACT);
        $this->db->where('entity_id', $contact_id);
        return $this->db->get($this->address_table)->result();
    }

    function get_contacts_count($params = array()) {
		if((isset($params['filter'])) && ($params['filter'] == 0)){
			return 0;
		}

		$this->db->select('distinct contacts.*, companies.name as company_name', false);
		$this->db->from('contacts');
		$this->db->join("companies", "companies.company_id=contacts.company_id", "left");
		$this->db->join("addresses", "addresses.entity_id=contacts.contact_id AND addresses.entity_type='contact'", "left");
		$this->db->join("phones", "phones.entity_id=contacts.contact_id AND phones.entity_type='contact'", "left");
		$this->db = $this->getCriteria($params, $this->db);
        return $this->db->count_all_results();
    }

    function get_contacts($params = array()) {
		if((isset($params['filter'])) && ($params['filter'] == 0)){
			return array();
		}

        if (!empty($params)) {
            $this->db->select('distinct contacts.*, companies.name as company_name', false);
            $this->db->from('contacts');
            $this->db->join("companies", "companies.company_id=contacts.company_id", "left");
            $this->db->join("addresses", "addresses.entity_id=contacts.contact_id AND addresses.entity_type='contact'", "left");
            $this->db->join("phones", "phones.entity_id=contacts.contact_id AND phones.entity_type='contact'", "left");
            $this->db = $this->getCriteria($params, $this->db);

            if ((isset($params['limit']) && ($params['limit'] != null))) {
				$this->db->limit($params['limit'], $params['offset']);
            }

            $this->db->order_by('contacts.contact_id', 'asc');
//			echo $this->db->get_compiled_select(); exit;
            $contacts = $this->db->get()->result_array();
            foreach ($contacts as &$contact) {
                $contact['phones'] = $this->get_phones($contact['contact_id']);
                $contact['addresses'] = $this->get_addresses($contact['contact_id']);
            }


        }else{
            $contacts = [];
        }
        
        return $contacts;
    }

	public function navigateContact($params, $currentRowId, $type) {
		$params = array_filter($params);
		if (!empty($params)) {
			$this->db->select('distinct contacts.*, companies.name as company_name', false);
			$this->db->from('contacts');
			$this->db->join("companies", "companies.company_id=contacts.company_id", "left");
			$this->db->join("addresses", "addresses.entity_id=contacts.contact_id AND addresses.entity_type='contact'", "left");
			$this->db->join("phones", "phones.entity_id=contacts.contact_id AND phones.entity_type='contact'", "left");
			$this->db = $this->getCriteria($params, $this->db);

			if($type == RecordNavigation::NEXT){
				$this->db->where('contacts.contact_id >', $currentRowId);
				$this->db->order_by('contacts.contact_id asc');
			}else{
				$this->db->where('contacts.contact_id <', $currentRowId);
				$this->db->order_by('contacts.contact_id desc');
			}

			$this->db->limit(1);
			$contact = $this->db->get()->row_array();

			if((is_array($contact)) && (sizeof($contact) > 0)){
				$contact['phones'] = $this->get_phones($contact['contact_id']);
				$contact['addresses'] = $this->get_addresses($contact['contact_id']);
			}
		}else{
			$contact = array();
		}

		return $contact;

	}

	public function getCriteria($params, $db){
		foreach ($params as $key => $value) {
			if ((strlen($value) > 0)) {
				$value = urldecode($value);
				switch ($key) {
					case 'city':
						if(strlen($value) > 0){
							$db->where('addresses.city', $value);
						}
						break;
					case 'street_address':
						if (strlen($value) > 0) {
							if ($value == FilterOptions::IS_EMPTY) {
								$db->group_start()
									->where('addresses.address IS NULL', null, false)
									->or_where('addresses.address', '')
									->group_end();
							} else if ($value == FilterOptions::IS_NOT_EMPTY) {
								$db->where('addresses.address IS NOT NULL', null, false);
							} else {
								$db->like('addresses.address', $value);
							}
						}
						break;
					case 'state':
						if(strlen($value) > 0){
							$db->where('addresses.state', $value);
						}
						break;
					case 'zip_code':
						if(strlen($value) > 0){
							$db->where('addresses.zip_code', $value);
						}
						break;
					case 'tax_record_sent_date':
						$range = explode("-", $value);
						if((sizeof($range) == 1) && (strtolower(trim($range[0])) != "invalid date")){
							$startDate = $range[0];
							$db->where('contacts.tax_record_sent_date', date('Y-m-d', strtotime($startDate)));
						}else if((sizeof($range) == 2) && (strtolower(trim($range[1])) != "invalid date")){
							$startDate = $range[0];
							$endDate = $range[1];
							$db->group_start()
								->where('contacts.tax_record_sent_date >= ', date('Y-m-d', strtotime($startDate)))
								->where('contacts.tax_record_sent_date <=', date('Y-m-d', strtotime($endDate)))
								->group_end();
						}else if((sizeof($range) == 2) && (strtolower(trim($range[1])) == "invalid date") && (strtolower(trim($range[0])) != "invalid date")){
							$db->where('contacts.tax_record_sent_date IS NULL', null, false);
						}
						break;
					case 'last_update':
						$range = explode("-", $value);
						if((sizeof($range) == 1) && (strtolower(trim($range[0])) != "invalid date")){
							$startDate = $range[0];
							$db->where('contacts.last_update', date('Y-m-d', strtotime($startDate)));
						}else if((sizeof($range) == 2) && (strtolower(trim($range[1])) != "invalid date")){
							$startDate = $range[0];
							$endDate = $range[1];
							$db->group_start()
								->where('contacts.last_update >= ', date('Y-m-d', strtotime($startDate)))
								->where('contacts.last_update <=', date('Y-m-d', strtotime($endDate)))
								->group_end();
						}else if((sizeof($range) == 2) && (strtolower(trim($range[1])) == "invalid date") && (strtolower(trim($range[0])) != "invalid date")){
							$db->where('contacts.last_update IS NULL', null, false);
						}
						break;
					case 'last_dial':
						$range = explode("-", $value);
						if((sizeof($range) == 1) && (strtolower(trim($range[0])) != "invalid date")){
							$startDate = $range[0];
							$db->where('contacts.last_dial', date('Y-m-d', strtotime($startDate)));
						}else if((sizeof($range) == 2) && (strtolower(trim($range[1])) != "invalid date")){
							$startDate = $range[0];
							$endDate = $range[1];
							$db->group_start()
								->where('contacts.last_dial >= ', date('Y-m-d', strtotime($startDate)))
								->where('contacts.last_dial <=', date('Y-m-d', strtotime($endDate)))
								->group_end();
						}else if((sizeof($range) == 2) && (strtolower(trim($range[1])) == "invalid date") && (strtolower(trim($range[0])) != "invalid date")){
							$db->where('contacts.last_dial IS NULL', null, false);
						}
						break;
					case 'first_name':
						if(strlen($value) > 0){
							$db->like('contacts.first_name', $value);
						}
						break;
					case 'last_name':
						if(strlen($value) > 0){
							$db->like('contacts.last_name', $value);
						}
						break;
					case 'email':
						if(strlen($value) > 0){
							$db->like('contacts.email', $value);
						}
						break;
					case 'company':
						if(strlen($value) > 0){
							$db->like('companies.name', $value);
						}
						break;


					case 'phone':
						if ($value != null) {
							if ($value == FilterOptions::IS_EMPTY) {
								$db->where('phones.phone IS NULL', null, false);
							} else if ($value == FilterOptions::IS_NOT_EMPTY) {
								$db->where('phones.phone IS NOT NULL', null, false);
							} else {
								$db->like('phones.phone', $value);
							}


						}
						break;
				}
			}
		}

		return $db;
	}

    public function get_distinct_states($query) {
        $this->db->select("state");
        $this->db->from($this->contact_table);
        $this->db->join("addresses", "addresses.entity_id=contacts.contact_id AND addresses.entity_type='contact'", "left");
        $this->db->like('addresses.state', $query);
        $this->db->limit(20);
        $this->db->group_by('state');

        $query = $this->db->get();
        $result = $query->result();


        return $result;
    }

    function get_contacts_by_company_id($company_id, $onlyMetOrHaventMet=false) {
        $this->db->select('contacts.*, CONCAT_WS(" ",contacts.first_name,contacts.middle_name,contacts.last_name) as contact_name, companies.name as company_name');
        $this->db->join("companies", "companies.company_id=contacts.company_id", "left");
        $this->db->where("companies.company_id", $company_id);

        if($onlyMetOrHaventMet){
			$this->db->group_start()
					->where("contacts.lead_gen_type =", ucwords(LeadGenOptions::MET))
					->or_where("contacts.lead_gen_type =", ucwords(LeadGenOptions::HAVENT_MET))
				->group_end();
		}

        $this->db->order_by('first_name', 'asc');
        $contacts = $this->db->get($this->contact_table)->result_array();

        foreach ($contacts as &$contact) {
            $contact['phones'] = $this->get_phones($contact['contact_id']);
            $contact['addresses'] = $this->get_addresses($contact['contact_id']);
        }


        return $contacts;
    }

    function add_contact($params) {
        $this->db->insert($this->contact_table, $params);
        return $this->db->insert_id();
    }

    function save_new_contact($contact_data) {
        $new_contact = array(
            'first_name' => $contact_data['first_name'],
            'middle_name' => isset($contact_data['middle_name']) ? $contact_data['middle_name'] : '',
            'email' => isset($contact_data['email']) ? $contact_data['email'] : '',
            'last_name' => $contact_data['last_name'],
            'lead_gen_type' => $contact_data['lead_gen_type'],
			'tax_record_sent_date' => @$contact_data['tax_record_sent_date'],
			'last_update' => @$contact_data['last_update'],
			'last_dial' => @$contact_data['last_dial'],
			'do_not_send' => @$contact_data['do_not_send'],
			'do_not_blast' => @$contact_data['do_not_blast'],
            'active_buyer' => @$contact_data['active_buyer'],
			'bad_no' => @$contact_data['bad_no'],
			'comment' => @$contact_data['comment']
		);

        $company = $this->ci->Company_model->get_company_by_name(trim($contact_data['company_name']));
        if((is_array($company)) && (sizeof($company) > 0)) {
            $new_contact['company_id'] = $company['company_id'];
        } else if ($contact_data['company_name'] != null) {
            $company_id = $this->ci->Company_model->add_company(['name' => $contact_data['company_name']]);
            $new_contact['company_id'] = $company_id;
        }
        $contact_id = $this->add_contact($new_contact);

        $this->db->trans_begin();
        foreach ($contact_data['addresses'] as $address) {
            $address['entity_id'] = $contact_id;
            $address['entity_type'] = EntityTypes::CONTACT;
            $address['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert($this->address_table, $address);
        }

        foreach ($contact_data['phones'] as $phone) {
            $this->db->insert($this->phone_table, [
                'phone' => $phone,
                'entity_id' => $contact_id,
                'entity_type' => EntityTypes::CONTACT,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        $this->db->trans_complete();

        return $contact_id;
    }

    function update_contact_data($contact_data, $contact_id) {
        $contact = array(
            'first_name' => $contact_data['first_name'],
            'middle_name' => @$contact_data['middle_name'],
            'last_name' => $contact_data['last_name'],
            'lead_gen_type' => $contact_data['lead_gen_type'],
            'email' => $contact_data['email'],
			'tax_record_sent_date' => @$contact_data['tax_record_sent_date'],
			'last_update' => @$contact_data['last_update'],
			'last_dial' => @$contact_data['last_dial'],
			'do_not_send' => @$contact_data['do_not_send'],
			'do_not_blast' => @$contact_data['do_not_blast'],
			'bad_no' => @$contact_data['bad_no'],
            'active_buyer' => @$contact_data['active_buyer'],
			'comment' => @$contact_data['comment']
		);

        if (strlen($contact_data['company_name']) > 0) {
            $company = $this->ci->Company_model->get_company_by_name(trim($contact_data['company_name']));
            if (sizeof($company) > 0) {
                $contact['company_id'] = $company['company_id'];
            } else {
                $company_id = $this->ci->Company_model->add_company(['name' => $contact_data['company_name']]);
                $contact['company_id'] = $company_id;
            }
        }

        if(isset($contact_data['detach_company'])){
        	$contact['company_id'] = null;
		}

        $this->db->trans_begin();
        $this->update_contact($contact_id, $contact);
        $this->delete_phones($contact_id);
        $this->delete_addresses($contact_id);
        foreach ($contact_data['addresses'] as $address) {
            $address['entity_id'] = $contact_id;
            $address['entity_type'] = EntityTypes::CONTACT;
            $address['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert($this->address_table, $address);
        }

        foreach ($contact_data['phones'] as $phone) {
            $this->db->insert($this->phone_table, [
                'phone' => $phone,
                'entity_id' => $contact_id,
                'entity_type' => EntityTypes::CONTACT,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        $this->db->trans_complete();
    }

    function update_contact($contact_id, $params) {
        $this->db->where('contact_id', $contact_id);
        return $this->db->update($this->contact_table, $params);
    }

    function delete_contact($contact_id) {
        $this->db->trans_begin();
        $this->db->delete($this->contact_table, array('contact_id' => $contact_id));
        $this->delete_addresses($contact_id);
        $this->delete_phones($contact_id);
        $this->db->trans_complete();
    }

    function delete_phones($contact_id) {
        $this->db->where('entity_type', EntityTypes::CONTACT);
        $this->db->where('entity_id', $contact_id);
        $this->db->delete($this->phone_table);
    }

    function delete_addresses($contact_id) {
        $this->db->where('entity_type', EntityTypes::CONTACT);
        $this->db->where('entity_id', $contact_id);
        $this->db->delete($this->address_table);
    }

}
