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
        $this->db->where('first_name', trim($name['first_name']));
        $this->db->where('middle_name', trim($name['middle_name']));
        $this->db->where('last_name', trim($name['last_name']));
        $contact = $this->db->get_where($this->contact_table)->row_array();
        return $contact;
    }

    function get_contact_by_email($email) {
        $this->db->where('email', trim($email));
        $contact = $this->db->get_where($this->contact_table)->row_array();

        return $contact;
    }

    function search_contact_by_name($name) {
        $this->db->like('first_name', $name);
        $this->db->or_like('middle_name', $name);
        $this->db->or_like('last_name', $name);
        $this->db->or_like('phone', $name);
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

    function get_contacts_count() {
        $this->db->from($this->contact_table);
        return $this->db->count_all_results();
    }

    function get_contacts($params = array()) {

        $params = array_filter($params);
        if (!empty($params)) {
            $this->db->select('contacts.*, companies.name as company_name');
            $this->db->from('contacts');
            $this->db->join("companies", "companies.company_id=contacts.company_id", "left");
            $this->db->join("addresses", "addresses.entity_id=contacts.contact_id AND addresses.entity_type='contact'", "left");
            $this->db->join("phones", "phones.entity_id=contacts.contact_id AND phones.entity_type='contact'", "left");

            foreach ($params as $key => $value) {
                if ((strlen($value) > 0)) {
                    $value = urldecode($value);
                    switch ($key) {
                        case 'city':
                            $this->db->where('addresses.city', $value);
                            break;
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
                        case 'first_name':
                            $this->db->like('contacts.first_name', $value);
                            break;
                        case 'last_name':
                            $this->db->like('contacts.last_name', $value);
                            break;
                        case 'email':
                            $this->db->like('contacts.email', $value);
                            break;
                        case 'company':
                            $this->db->like('companies.name', $value);
                            break;
                        case 'phone':
                            $this->db->like('phones.phone', $value);
                            break;
                    }
                }
            }

            if ((isset($params['limit']) && ($params['limit'] != null))) {
                $this->db->limit(RECORDS_PER_PAGE);
            }

            $this->db->group_by("contacts.contact_id");
            $this->db->order_by('first_name', 'asc');
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

    function get_contacts_by_company_id($company_id) {
        $this->db->select('contacts.*, companies.name as company_name');
        $this->db->join("companies", "companies.company_id=contacts.company_id", "left");
        $this->db->where("companies.company_id", $company_id);
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
        $new_contact = [
            'first_name' => $contact_data['first_name'],
            'middle_name' => $contact_data['middle_name'],
            'last_name' => $contact_data['last_name'],
            'lead_gen_type' => $contact_data['lead_gen_type']
        ];

        $company = $this->ci->Company_model->get_company_by_name(trim($contact_data['company_name']));
        if (sizeof($company) > 0) {
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
    }

    function update_contact_data($contact_data, $contact_id) {
        $contact = [
            'first_name' => $contact_data['first_name'],
            'middle_name' => $contact_data['middle_name'],
            'last_name' => $contact_data['last_name'],
            'lead_gen_type' => $contact_data['lead_gen_type'],
            'email' => $contact_data['email']
        ];

        if (strlen($contact_data['company_name']) > 0) {
            $company = $this->ci->Company_model->get_company_by_name(trim($contact_data['company_name']));
            if (sizeof($company) > 0) {
                $contact['company_id'] = $company['company_id'];
            } else {
                $company_id = $this->ci->Company_model->add_company(['name' => $contact_data['company_name']]);
                $contact['company_id'] = $company_id;
            }
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
