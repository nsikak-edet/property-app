<?php

class Search_model extends CI_Model {

    private $propertyTable = "properties";
    private $ci;

    public function __construct() {
        parent::__construct();
        $this->ci = & get_instance();
        $this->ci->load->model('Contact_model');
        $this->ci->load->model('Company_model');
        $this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
    }

    public function search($criteria, $offset = 0) {
        $this->db->select("prop.*,contacts.*, prop.company_id as com_id, "
                . "prop.contact_id as con_id, companies.name as company_name, prop.name as name, "                
                . "CONCAT_WS(' ',first_name,middle_name,last_name) as contact_name, contacts.lead_gen_type", false);
        $this->db->join('contacts', 'contacts.contact_id=prop.contact_id', 'left');
        $this->db->join('companies', 'companies.company_id=prop.company_id', 'left');
        $this->db->join('addresses', "companies.company_id=addresses.entity_id AND addresses.entity_type='company'", 'left');

        $this->db->from("properties as prop");
        $this->db = $this->setCriteria($criteria, $this->db);

        if (!(array_filter($criteria))) {
            $this->db->limit(200);
        }


        $query = $this->db->get();
        $result = $query->result_array();

        $result = $this->formatSearchResult($result);

        return $result;
    }

    public function search_x_property($n) {
        $sqlCompanySearch = "SELECT "
                . "companies.name,addresses.*,"
                . "'company' as record_type, "
                . " COUNT(DISTINCT(properties.property_id)) as property_count"
                . " FROM `properties` "
                . " INNER JOIN companies ON companies.company_id=properties.company_id"
                . " LEFT JOIN addresses ON addresses.entity_id=companies.company_id AND addresses.entity_type='company'"
                . " LEFT JOIN phones ON phones.entity_id=companies.company_id AND phones.entity_type='company'"
                . " GROUP BY companies.company_id HAVING COUNT(DISTINCT(properties.property_id)) = $n";

        $sqlContactSearch = "SELECT "
                . "concat_ws(' ',first_name,middle_name,last_name) as name,addresses.*,"
                . " 'contact' as record_type,"
                . " COUNT(DISTINCT(properties.property_id)) as property_count FROM `properties` "
                . " INNER JOIN contacts ON contacts.contact_id=properties.contact_id"
                . " LEFT JOIN addresses ON addresses.entity_id=contacts.contact_id AND addresses.entity_type='contact'"
                . " LEFT JOIN phones ON phones.entity_id=contacts.contact_id AND phones.entity_type='contact'"
                . " GROUP BY contacts.contact_id HAVING COUNT(DISTINCT(properties.property_id)) = $n";

        $query = $this->db->query($sqlContactSearch . " UNION " . $sqlCompanySearch);
        $result = $query->result_array();

        return $result;
    }

    public function search_state_property($state) {
        $sqlCompanySearch = "SELECT "
                . "companies.name,addresses.*,"
                . "'company' as record_type, "
                . " COUNT(DISTINCT(properties.property_id)) as property_count"
                . " FROM `properties` "
                . " INNER JOIN companies ON companies.company_id=properties.company_id"
                . " LEFT JOIN addresses ON addresses.entity_id=companies.company_id AND addresses.entity_type='company'"
                . " LEFT JOIN phones ON phones.entity_id=companies.company_id AND phones.entity_type='company'"
                . " WHERE properties.state LIKE '%$state%'"
                . " GROUP BY companies.company_id HAVING COUNT(DISTINCT(properties.property_id)) > 0";

        $sqlContactSearch = "SELECT "
                . "concat_ws(' ',first_name,middle_name,last_name) as name,addresses.*,"
                . " 'contact' as record_type,"
                . " COUNT(DISTINCT(properties.property_id)) as property_count FROM `properties` "
                . " INNER JOIN contacts ON contacts.contact_id=properties.contact_id"
                . " LEFT JOIN addresses ON addresses.entity_id=contacts.contact_id AND addresses.entity_type='contact'"
                . " LEFT JOIN phones ON phones.entity_id=contacts.contact_id AND phones.entity_type='contact'"
                . " WHERE properties.state LIKE '%$state%'"
                . " GROUP BY contacts.contact_id HAVING COUNT(DISTINCT(properties.property_id)) > 0";

        $query = $this->db->query($sqlContactSearch . " UNION " . $sqlCompanySearch);
        $result = $query->result_array();

        return $result;
    }

    public function count($criteria) {
        $this->db->select("COUNT(*)");
        $this->db->from($this->propertyTable);
        $this->db = $this->setCriteria($criteria, $this->db);
        $query = $this->db->get();
        $result = $query->result_array();

        return (sizeof($result) > 0) ? $result[0]['COUNT(*)'] : 0;
    }

    private function setCriteria($criteria, &$db) {
        foreach ($criteria as $key => $param) {
            switch ($key) {
                case 'name':
                    if (strlen($criteria[$key]) > 0) {
                        $db->group_start()
                                ->like('prop.name', $param)
                                ->group_end();
                    }
                    break;

                case 'store_no':
                    if (strlen($criteria[$key]) > 0)
                        $db->where('prop.store_number', $param);
                    break;

                case 'city':
                    if (strlen($criteria[$key]) > 0)
                        $db->like('prop.city', $param);
                    break;

                case 'state':
                    if ((is_array($criteria[$key])) && (sizeof($criteria[$key]) > 0)) {
                        $count = 0;
                        $group = $db->group_start();
                        $states = $criteria[$key];
                        foreach ($states as $state) {
                            if ($count == 0) {
                                $group->where('prop.state', trim($state));
                            } else {
                                $group->or_where('prop.state', trim($state));
                            }
                            $count++;
                        }
                        $group->group_end();
                    }
                    break;

                case 'company':
                    if (strlen($criteria[$key]) > 0)
                        $db->like('companies.name', $param);
                    break;

                case 'first_name':
                    if (strlen($criteria[$key]) > 0)
                        $db->like('contacts.first_name', $param);
                    break;

                case 'last_name':
                    if (strlen($criteria[$key]) > 0)
                        $db->like('contacts.last_name', $param);
                    break;

                case 'lead_gen_type':
                    if ($criteria[$key] == LeadGenTypes::MET) {
                        $db->group_start();
                        $db->where("contacts.lead_gen_type !=", null);
                        $db->where("contacts.lead_gen_type !=", '');
                        $db->group_end();
                    }

                    if ($criteria[$key] == LeadGenTypes::HAVENT_MET) {
                        $db->where("contacts.lead_gen_type", null);
                    }
                    break;

                case 'zip_code':
                    if (strlen($criteria[$key]) > 0)
                        $db->where('prop.zip_code', $param);
                    break;

                case 'street_address':
                    if (strlen($criteria[$key]) > 0)
                        $db->like('prop.address', $param);
                    break;

                case 'property_type':
                    if (strlen($criteria[$key]) > 0)
                        $db->like('prop.property_type', $param);
                    break;

                case 'property_count':
                    if (strlen($criteria[$key]) > 0) {
                        $states = $criteria['state'];
                        $propertyName = $criteria['name'];
                        $stateQuery = $this->composeStateQuery($states, $propertyName);
                        $range = explode('-', $criteria[$key]);
                        $minVal = null;
                        $maxVal = null;
                        if (sizeof($range) == 2) {
                            $minVal = intval($range[0]);
                            $maxVal = intval($range[1]);
                            
                            $db->group_start();
                            $db->where("EXISTS(SELECT state, name,"                                   
                                    . "COUNT(p2.company_id) as freq "                                   
                                    . "FROM properties p2 "
                                    . "WHERE prop.company_id=p2.company_id $stateQuery"
                                    . "HAVING (freq BETWEEN $minVal AND $maxVal))");
                            
                            $db->or_where("EXISTS(SELECT state, name,"                                   
                                    . "COUNT(p2.contact_id) as freq "                                   
                                    . "FROM properties p2 "
                                    . "WHERE prop.contact_id=p2.contact_id $stateQuery"
                                    . "HAVING (freq BETWEEN $minVal AND $maxVal))");
                            $db->group_end();                            
                        } else if (sizeof($range) == 1) {
                            $minVal = intval($range[0]);
                            $db->group_start();
                            $db->where("EXISTS(SELECT state, name,"                                   
                                    . "COUNT(p2.company_id) as freq "                                   
                                    . "FROM properties p2 "
                                    . "WHERE prop.company_id=p2.company_id $stateQuery"
                                    . "HAVING (freq = $minVal))");
                            
                            $db->or_where("EXISTS(SELECT state, name,"                                   
                                    . "COUNT(p2.contact_id) as freq "                                   
                                    . "FROM properties p2 "
                                    . "WHERE prop.contact_id=p2.contact_id $stateQuery"
                                    . "HAVING (freq = $minVal))");
                            $db->group_end();                
                        }
                    }
                    break;
            }
        }

        return $db;
    }

    private function composeStateQuery($states, $propertyName) {
        $stateQuery = "";
        if ((is_array($states)) && (sizeof($states) > 0)) {
            $count = 0;
            foreach ($states as $state) {
                if ($count == 0) {
                    $stateQuery = "state='$state'";
                } else {
                    $stateQuery .= " OR state='$state'";
                }

                $count++;
            }

            if (strlen($stateQuery) > 0) {
                $stateQuery = " AND (" . $stateQuery . ")";
            }
        }

        if ((strlen($propertyName) > 0)) {
            $propertyName = $this->db->escape_like_str($propertyName);
            $stateQuery .= " AND (p2.name LIKE '%" . $propertyName . "')";
        }

        return $stateQuery;
    }

    private function formatSearchResult($properties) {
        $groupedProperties = [];

        foreach ($properties as $property) {
            $uniqueKey = trim($property['company_name']) . trim($property['contact_name']);
            $uniqueKey = (strlen($uniqueKey) > 0) ? $uniqueKey : "not linked";
            $groupedProperties[$uniqueKey]['properties'][] = $property;

            if ($property['com_id'] > 0) {
                $company = $this->ci->Company_model->get_company($property['com_id'], true);                
                $groupedProperties[$uniqueKey]['detail'] = $company;
                $groupedProperties[$uniqueKey]['type'] = EntityTypes::COMPANY;
            } else if ($property['con_id'] > 0) {
                $contact = $this->ci->Contact_model->get_contact($property['con_id'], true);
                $groupedProperties[$uniqueKey]['detail'] = $contact;
                $groupedProperties[$uniqueKey]['type'] = EntityTypes::CONTACT;
            } else {
                $groupedProperties[$uniqueKey]['type'] = "undefined owner";
                $groupedProperties[$uniqueKey]['detail'] = ['phones' => [], 'name' => ''];
            }

//            $groupedProperties[$uniqueKey]['detail'] = $company;
        }
        
//        print_r($groupedProperties);

        return $groupedProperties;
    }

}
