<?php

class Property_model extends CI_Model {

    private $property_table = "properties";
    private $phone_table = "phones";
    private $address_table = "addresses";
    private $ci;

    function __construct() {
        parent::__construct();
        $this->ci = & get_instance();
        $this->ci->load->model("Company_model");
        $this->ci->load->model("Contact_model");
        $this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
    }

    function get_property($property_id) {
        $this->db->select("*, properties.name as property_name, properties.company_id as company_id, properties.contact_id as contact_id");
        $this->db->join('companies', 'companies.company_id=properties.company_id', 'left');
        $this->db->join('contacts', 'contacts.contact_id=properties.contact_id', 'left');
        $property = $this->db->get_where($this->property_table, array('property_id' => $property_id))->row_array();

        return $property;
    }

    function get_property_by_name($name) {
        $this->db->where('name', $name);
        $property = $this->db->get_where($this->property_table)->row_array();
        return $property;
    }

    public function save_bulk_property($properties) {

        $this->db->trans_start();
        foreach ($properties as $property) {
            $propertyData = [
                'name' => $property['name'],
                'store_number' => $property['store_number'],
                'address' => $property['address'],
                'state' => $property['state'],
                'city' => $property['city'],
                'zip_code' => $property['zip_code'],
                'google_map_link' => $property['google_map_link'],
                'property_type' => $property['property_type'],
                'created_at' => date('Y-m-d H:i:s')
            ];

            //insert property
            if ((strlen($property['owner_company']) > 0) && (strlen($property['owner_name']) > 0)) {
                $this->associate_contact($property);
                $this->associate_contact_with_company($property);
                $propertyData['contact_id'] = $property['contact_id'];
            } else if (strlen($property['owner_company']) > 0) {
                $property = $this->associate_company($property);
                $propertyData['company_id'] = $property['company_id'];
            } else if (strlen($property['owner_name']) > 0) {
                $property = $this->associate_contact($property);
                $propertyData['contact_id'] = $property['contact_id'];
            }

            $this->db->insert($this->property_table, $propertyData);
        }

        $this->db->trans_complete();
    }

    private function associate_company(&$property) {
        $storedCompany = $this->ci->Company_model->get_company_by_name($property['owner_company']);

        if ($storedCompany == null) {
            $companyId = $this->ci->Company_model->add_company(['name' => $property['owner_company']]);

            //insert address
            if ((strlen($property['owner_address']) > 0)) {
                $contactAddress = [
                    'address' => $property['owner_address'],
                    'state' => $property['owner_state'],
                    'city' => $property['owner_city'],
                    'zip_code' => $property['owner_zip_code'],
                    'entity_id' => $companyId,
                    'entity_type' => EntityTypes::COMPANY,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert($this->address_table, $contactAddress);
            }


            if (strlen($property['owner_phone_1']) > 0) {
                $this->db->insert($this->phone_table, [
                    'phone' => $property['owner_phone_1'],
                    'entity_id' => $companyId,
                    'entity_type' => EntityTypes::COMPANY,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            if (strlen($property['owner_phone_2']) > 0) {
                $this->db->insert($this->phone_table, [
                    'phone' => $property['owner_phone_2'],
                    'entity_id' => $companyId,
                    'entity_type' => EntityTypes::COMPANY,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            $property['company_id'] = $companyId;
        } else {
            $property['company_id'] = $storedCompany['company_id'];
        }

        return $property;
    }

    private function associate_contact(&$property) {
        $fullName = explode(" ", $property['owner_name']);
        if (sizeof($fullName) == 2) {
            $contactName = ['first_name' => $fullName[0], 'middle_name' => '', 'last_name' => $fullName[1]];
        } else if (sizeof($fullName) == 3) {
            $contactName = ['first_name' => $fullName[0], 'middle_name' => $fullName[1], 'last_name' => $fullName[2]];
        } else if (sizeof($fullName) == 1) {
            $contactName = ['first_name' => $fullName[0], 'middle_name' => null, 'last_name' => null];
        } else if (sizeof($fullName) == 4) {
            $contactName = ['first_name' => $fullName[0] . " ".$fullName[1]. " " . $fullName[2], 'middle_name' => '', 'last_name' => $fullName[3]];
        }

        $property['contact_id'] = null;
        if (isset($contactName)) {
            $storedContactByName = $this->ci->Contact_model->get_contact_by_name($contactName);
            $storedContactByEmail = $this->ci->Contact_model->get_contact_by_email($property['owner_email']);
            if (($storedContactByEmail == null) && ($storedContactByName == null)) {
                $contactData = [
                    'first_name' => $contactName['first_name'],
                    'middle_name' => $contactName['middle_name'],
                    'last_name' => $contactName['last_name'],
                    'email' => $property['owner_email']
                ];

                $contactId = $this->ci->Contact_model->add_contact($contactData);

                //insert address
                $contactAddress = [
                    'address' => $property['owner_address'],
                    'state' => $property['owner_state'],
                    'city' => $property['owner_city'],
                    'zip_code' => $property['owner_zip_code'],
                    'entity_id' => $contactId,
                    'entity_type' => EntityTypes::CONTACT,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert($this->address_table, $contactAddress);

                if (strlen($property['owner_phone_1']) > 0) {
                    $this->db->insert($this->phone_table, [
                        'phone' => $property['owner_phone_1'],
                        'entity_id' => $contactId,
                        'entity_type' => EntityTypes::CONTACT,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }

                if (strlen($property['owner_phone_2']) > 0) {
                    $this->db->insert($this->phone_table, [
                        'phone' => $property['owner_phone_2'],
                        'entity_id' => $contactId,
                        'entity_type' => EntityTypes::CONTACT,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }

                $property['contact_id'] = $contactId;
            } else if ($storedContactByEmail != null) {
                $property['contact_id'] = $storedContactByEmail['contact_id'];
            } else if ($storedContactByName != null) {
                $property['contact_id'] = $storedContactByName['contact_id'];
            }
        }

        return $property;
    }

    private function associate_contact_with_company($property) {
        if (strlen($property['owner_company']) > 0) {
            $storedCompany = $this->ci->Company_model->get_company_by_name($property['owner_company']);
            if ($storedCompany == null) {
                $companyId = $this->ci->Company_model->add_company(['name' => $property['owner_company']]);
                $contact = [
                    'company_id' => $companyId,
                ];
            } else {
                $contact = [
                    'company_id' => $storedCompany['company_id']
                ];
            }

            $this->ci->Contact_model->update_contact($property['contact_id'], $contact);
        }
    }

    function get_properties_count() {
        $this->db->from($this->property_table);
        return $this->db->count_all_results();
    }

    function get_properties($params = array()) {

        $this->db->select("*, companies.name as company_name, properties.name as name, "
                . "concat_ws(' ',contacts.first_name, contacts.middle_name, contacts.last_name) as contact_name");
        $this->db->join('contacts', 'contacts.contact_id=properties.contact_id', 'left');
        $this->db->join('companies', 'companies.company_id=properties.company_id', 'left');

        if (isset($params) && !empty($params)) {
            $this->db->limit($params['limit'], $params['offset']);
        }

        $properties = $this->db->get($this->property_table)->result_array();
        return $properties;
    }

    function get_properties_by_contact($contact_id) {
        $this->db->where('contact_id', $contact_id);
        $properties = $this->db->get($this->property_table)->result_array();
        return $properties;
    }

    function get_properties_by_company_id($company_id) {
        $this->db->where('company_id', $company_id);
        $properties = $this->db->get($this->property_table)->result_array();
        return $properties;
    }

    function add_property($params) {
        $this->db->insert($this->property_table, $params);
        return $this->db->insert_id();
    }

    function save_new_property($property) {

        $this->db->trans_begin();
        $property_data = [
            'name' => $property['name'],
            'store_number' => $property['store_number'],
            'address' => $property['address'],
            'state' => $property['state'],
            'city' => $property['city'],
            'zip_code' => $property['zip_code'],
            'google_map_link' => $property['google_map_link'],
            'property_type' => $property['property_type'],
            'contact_id' => isset($property['contact_id']) ? $property['contact_id'] : null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if (strlen($property['company']) > 0) {
            $property_data['company_id'] = $this->ci->Company_model->pop_company_by_name(trim($property['company']));
            unset($property['company']);
        }

        $this->db->insert($this->property_table, $property_data);
        $this->db->trans_complete();
    }

    public function get_distinct_property_types($query) {
        $this->db->select("*");
        $this->db->from($this->property_table);
        $this->db->like('property_type', $query);
        $this->db->limit(20);
        $this->db->group_by('property_type');

        $query = $this->db->get();
        $result = $query->result();


        return $result;
    }

    public function get_distinct_states($query) {
        $this->db->select("state");
        $this->db->from($this->property_table);
        $this->db->like('state', $query);
        $this->db->limit(20);
        $this->db->group_by('state');

        $query = $this->db->get();
        $result = $query->result();


        return $result;
    }

    function update_property($property_id, $params) {
        if (isset($params['company']) && strlen($params['company']) > 0) {
            $params['contact_id'] = null;
            $params['company_id'] = $this->ci->Company_model->pop_company_by_name(trim($params['company']));
        } else if (isset($params['contact_id']) && $params['contact_id'] > 0) {
            $params['company_id'] = null;
        }
        unset($params['company']);

        $this->db->where('property_id', $property_id);
        return $this->db->update($this->property_table, $params);
    }

    function delete_property($property_id) {
        $this->db->trans_begin();
        $this->db->delete($this->property_table, array('property_id' => $property_id));
        $this->db->trans_complete();
    }

}
