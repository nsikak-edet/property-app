<?php

class Company_model extends CI_Model {

    private $company_table = "companies";
    private $address_table = "addresses";
    private $phone_table = "phones";
    private $ci;

    function __construct() {
        parent::__construct();
        $this->ci = & get_instance();
        $this->ci->load->model("Contact_model");
        $this->ci->load->model("Property_model");
        $this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
    }

    function get_company($company_id, $excludeProperties = false) {
        $company = $this->db->get_where($this->company_table, array('company_id' => $company_id))->row_array();

        if (is_array($company) && sizeof($company) > 0) {
            $company['phones'] = $this->get_phones($company_id);
            $company['addresses'] = $this->get_addresses($company_id);
            $company['contacts'] = $this->ci->Contact_model->get_contacts_by_company_id($company_id);

            if ($excludeProperties == false) {
                $company['properties'] = $this->ci->Property_model->get_properties_by_company_id($company_id);
            }
        }

        return $company;
    }

    function get_company_by_name($name) {
        return $this->db->get_where($this->company_table, array('name' => $name))->row_array();
    }

    public function get_distinct_states($query=null, $limit=20) {
        $this->db->select("state");
        $this->db->from($this->company_table);
        
        if($query != null){
            $this->db->like('state', $query);
        }
        
        if($limit > 0){
           $this->db->limit(20);
        }
        
        $this->db->group_by('state');

        $query = $this->db->get();
        $result = $query->result();


        return $result;
    }

    function pop_company_by_name($name) {
        $company = $this->get_company_by_name(trim($name));
        if (sizeof($company) > 0) {
            return $company['company_id'];
        } else {
            $company_id = $this->add_company(['name' => $name]);
            return $company_id;
        }
    }

    public function save_bulk_company($companies) {
        $this->db->trans_start();
        foreach ($companies as $company) {
            $storedCompany = $this->get_company_by_name($company['name']);

            //insert or update
            if (($storedCompany == null)) {
                $company_id = $this->add_company(['name' => $company['name']]);

                //insert address
                $address = [
                    'address' => $company['address'],
                    'state' => $company['state'],
                    'city' => $company['city'],
                    'zip_code' => $company['zip_code'],
                    'entity_id' => $company_id,
                    'entity_type' => EntityTypes::COMPANY,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert($this->address_table, $address);

                //insert phone numbers
                if (strlen($company['phone_1']) > 0) {
                    $this->db->insert($this->phone_table, [
                        'phone' => $company['phone_1'],
                        'entity_id' => $company_id,
                        'entity_type' => EntityTypes::COMPANY,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }

                if (strlen($company['phone_2']) > 0) {
                    $this->db->insert($this->phone_table, [
                        'phone' => $company['phone_2'],
                        'entity_id' => $company_id,
                        'entity_type' => EntityTypes::COMPANY,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }

        $this->db->trans_complete();
    }

    public function get_phones($company_id) {
        $this->db->where('entity_type', EntityTypes::COMPANY);
        $this->db->where('entity_id', $company_id);
        return $this->db->get($this->phone_table)->result();
    }

    public function getDistinctCompanies($query) {
        $this->db->select("*");
        $this->db->from($this->company_table);
        $this->db->like('name', $query);
        $this->db->limit(20);
        $this->db->order_by('name');

        $query = $this->db->get();
        $result = $query->result();

        return $result;
    }

    public function get_addresses($company_id) {
        $this->db->where('entity_type', EntityTypes::COMPANY);
        $this->db->where('entity_id', $company_id);
        return $this->db->get($this->address_table)->result();
    }

    function get_companies_count() {
        $this->db->from($this->company_table);
        return $this->db->count_all_results();
    }

    function get_companies($params = array()) {

        $params = array_filter($params);
        if (!empty($params)) {
            $this->db->select('companies.*');
            $this->db->from('companies');
            $this->db->join("addresses", "addresses.entity_id=companies.company_id AND addresses.entity_type='company'", "left");
            $this->db->join("phones", "phones.entity_id=companies.company_id AND phones.entity_type='company'", "left");

            foreach ($params as $key => $value) {
                if (($key != 'limit') && (strlen($value) > 0)) {
                    $value = urldecode($value);
                    switch ($key) {
                        case 'street_address':
                            $this->db->like('addresses.address', $value);
                            break;
                        case 'state':
                            $this->db->where('addresses.state', $value);
                            break;
                        case 'zip_code':
                            $this->db->where('addresses.zip_code', $value);
                            break;
                        case 'city':
                            $this->db->where('addresses.city', $value);
                            break;
                        case 'name':
                            $this->db->like('companies.name', $value);
                            break;
                        case 'phone':
                            $this->db->like('phones.phone', $value);
                            break;
                    }
                }
            }

            if (isset($params['limit']) && ($params['limit'] != null)) {
                $this->db->limit($params['limit']);
            }

            $this->db->group_by('companies.company_id');
            $this->db->order_by('name', 'asc');
            $companies = $this->db->get()->result_array();
            foreach ($companies as &$company) {
                $company['phones'] = $this->get_phones($company['company_id']);
                $company['addresses'] = $this->get_addresses($company['company_id']);
            }
        } else {
            $companies = [];
        }

        return $companies;
    }

    function add_company($params) {
        $this->db->insert($this->company_table, $params);
        return $this->db->insert_id();
    }

    function save_new_company($company_data) {
        $company_id = $this->add_company(['name' => $company_data['company_name']]);

        $this->db->trans_begin();
        foreach ($company_data['addresses'] as $address) {
            $address['entity_id'] = $company_id;
            $address['entity_type'] = EntityTypes::COMPANY;
            $address['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert($this->address_table, $address);
        }

        foreach ($company_data['phones'] as $phone) {
            $this->db->insert($this->phone_table, [
                'phone' => $phone,
                'entity_id' => $company_id,
                'entity_type' => EntityTypes::COMPANY,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        $this->db->trans_complete();
    }

    function update_company_data($company_data, $company_id) {
        $company = ['name' => $company_data['company_name']];

        $this->db->trans_begin();
        $this->update_company($company_id, $company);
        $this->delete_phones($company_id);
        $this->delete_addresses($company_id);
        foreach ($company_data['addresses'] as $address) {
            $address['entity_id'] = $company_id;
            $address['entity_type'] = EntityTypes::COMPANY;
            $address['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert($this->address_table, $address);
        }

        foreach ($company_data['phones'] as $phone) {
            $this->db->insert($this->phone_table, [
                'phone' => $phone,
                'entity_id' => $company_id,
                'entity_type' => EntityTypes::COMPANY,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        $this->db->trans_complete();
    }

    function update_company($company_id, $params) {
        $this->db->where('company_id', $company_id);
        return $this->db->update($this->company_table, $params);
    }

    function delete_company($company_id) {
        $this->db->trans_begin();
        $this->db->delete($this->company_table, array('company_id' => $company_id));
        $this->delete_addresses($company_id);
        $this->delete_phones($company_id);
        $this->db->trans_complete();
    }

    function delete_phones($company_id) {
        $this->db->where('entity_type', EntityTypes::COMPANY);
        $this->db->where('entity_id', $company_id);
        $this->db->delete($this->phone_table);
    }

    function delete_addresses($company_id) {
        $this->db->where('entity_type', EntityTypes::COMPANY);
        $this->db->where('entity_id', $company_id);
        $this->db->delete($this->address_table);
    }

}
