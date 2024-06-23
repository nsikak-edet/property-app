<?php

class Duplicate_model extends CI_Model
{

    private $propertyTable = "properties";
    private $contact_table = "contacts";
    private $company_table = "companies";
    private $address_table = "addresses";
    private $phone_table = "phones";
    private $ci;

    public function __construct()
    {
        parent::__construct();
        $this->ci = &get_instance();
        $this->ci->load->model('Contact_model');
        $this->ci->load->model('Company_model');
        $this->ci->load->model('Property_model');
        $this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
    }

    public function searchCompany($fields)
    {

        $queryFilter = $this->getQueryFilter($fields);
        $whereClause = ($queryFilter['where'] != '') ? " AND " . $queryFilter['where'] : '';
        $orderClause = ($queryFilter['group'] != '') ? " ORDER BY " . $queryFilter['group'] : '';
        $sqlQuery = "SELECT com_a.*,address_a.*,phone_a.phone FROM companies com_a  "
            . "LEFT JOIN addresses address_a ON com_a.company_id=address_a.entity_id AND address_a.entity_type='company'"
            . "LEFT JOIN phones phone_a ON com_a.company_id=phone_a.entity_id AND phone_a.entity_type='company' "
            . "WHERE EXISTS(
                    SELECT 1
                    FROM companies com_b
                        LEFT JOIN addresses address_b ON com_b.company_id=address_b.entity_id AND address_b.entity_type='company' 
                        LEFT JOIN phones phone_b ON com_b.company_id=phone_b.entity_id AND phone_b.entity_type='company'                    
                    WHERE (com_a.company_id <> com_b.company_id) $whereClause LIMIT 1, 1
                  ) GROUP BY com_a.company_id $orderClause LIMIT 50";

        $query = $this->db->query($sqlQuery);
        $result = $query->result_array();
        return $result;
    }

    public function searchProperty($fields)
    {

        $queryFilter = $this->getPropertyQueryFilter($fields);
        $whereClause = ($queryFilter['where'] != '') ? " AND " . $queryFilter['where'] : '';
        $orderClause = ($queryFilter['group'] != '') ? " ORDER BY " . $queryFilter['group'] : '';
        $sqlQuery = "SELECT prop_a.* FROM properties prop_a "
            . "WHERE EXISTS(
                    SELECT 1
                    FROM properties prop_b               
                    WHERE (prop_a.property_id <> prop_b.property_id) $whereClause LIMIT 1, 1
                  ) GROUP BY prop_a.property_id $orderClause LIMIT 50";

        $query = $this->db->query($sqlQuery);
        $result = $query->result_array();

        return $result;
    }


    public function searchContact($fields)
    {
        $queryFilter = $this->getQueryFilter($fields);
        $whereClause = ($queryFilter['where'] != '') ? " AND " . $queryFilter['where'] : '';
        $orderClause = ($queryFilter['group'] != '') ? " ORDER BY " . $queryFilter['group'] : '';
        $sqlQuery = "SELECT con_a.*,address_a.*,phone_a.phone, com.name as company_name FROM contacts con_a  "
            . "LEFT JOIN addresses address_a ON con_a.contact_id=address_a.entity_id AND address_a.entity_type='contact'"
            . "LEFT JOIN phones phone_a ON con_a.contact_id=phone_a.entity_id AND phone_a.entity_type='contact' "
            . "LEFT JOIN companies com ON con_a.company_id=com.company_id "
            . "WHERE EXISTS(
                    SELECT 1
                    FROM contacts con_b
                        LEFT JOIN addresses address_b ON con_b.contact_id=address_b.entity_id AND address_b.entity_type='contact' 
                        LEFT JOIN phones phone_b ON con_b.contact_id=phone_b.entity_id AND phone_b.entity_type='contact'                    
                    WHERE (con_a.contact_id<>con_b.contact_id) $whereClause LIMIT 1, 1
                  ) GROUP BY con_a.contact_id $orderClause LIMIT 50";


        $query = $this->db->query($sqlQuery);
        $result = $query->result_array();
        return $result;
    }

    private function getQueryFilter($criteria)
    {
        $count = 0;
        $whereQuery = "";
        $query = ['where' => '', 'group' => ''];
        foreach ($criteria as $key => $param) {
            switch ($param) {
                case 'name':
                    if ($count == 0) {
                        $query['where'] = "(com_a.name=com_b.name AND com_a.name != '') ";
                        $query['group'] = "com_a.name";
                    } else {
                        $query['where'] .= " AND (com_a.name=com_b.name AND com_a.name != '') ";
                        $query['group'] .= ",com_a.name";
                    }
                    break;

                case 'address':
                    if ($count == 0) {
                        $query['where'] = "(address_a.address=address_b.address AND ((address_a.address != ''))) ";
                        $query['group'] = "address_a.address";
                    } else {
                        $query['where'] .= " AND (address_a.address=address_b.address AND ((address_a.address != ''))) ";
                        $query['group'] .= ",address_a.address";
                    }
                    break;
                case 'city':
                    if ($count == 0) {
                        $query['where'] = "(address_a.city=address_b.city AND address_a.city != '') ";
                        $query['group'] = "address_a.city";
                    } else {
                        $query['where'] .= " AND (address_a.city=address_b.city AND address_a.city != '')  ";
                        $query['group'] .= ",address_a.city";
                    }
                    break;

                case 'state':
                    if ($count == 0) {
                        $query['where'] = "(address_a.state=address_b.state AND address_a.state != '') ";
                        $query['group'] = "address_a.state";
                    } else {
                        $query['where'] .= " AND (address_a.state=address_b.state AND address_a.state != '')";
                        $query['group'] .= ",address_a.state";
                    }
                    break;

                case 'zip_code':
                    if ($count == 0) {
                        $query['where'] = "(address_a.zip_code=address_b.zip_code AND address_a.zip_code != '') ";
                        $query['group'] = "address_a.zip_code";
                    } else {
                        $query['where'] .= " AND (address_a.zip_code=address_b.zip_code AND address_a.zip_code != '') ";
                        $query['group'] .= ",address_a.zip_code";
                    }
                    break;

                case 'phone':
                    if ($count == 0) {
                        $query['where'] = "(phone_a.phone=phone_b.phone AND phone_a.phone != '') ";
                        $query['group'] = "phone_a.phone";
                    } else {
                        $query['where'] .= "AND (phone_a.phone=phone_b.phone AND phone_a.phone != '') ";
                        $query['group'] .= ",phone_a.phone";
                    }
                    break;

                case 'first_name':
                    if ($count == 0) {
                        $query['where'] = "(con_a.first_name=con_b.first_name AND con_a.first_name != '') ";
                        $query['group'] = "con_a.first_name";
                    } else {
                        $query['where'] .= "AND (con_a.first_name=con_b.first_name AND con_a.first_name != '')";
                        $query['group'] .= ",con_a.first_name";
                    }
                    break;

                case 'last_name':
                    if ($count == 0) {
                        $query['where'] = "(con_a.last_name=con_b.last_name AND con_a.last_name != '') ";
                        $query['group'] = "con_a.last_name";
                    } else {
                        $query['where'] .= "AND (con_a.last_name=con_b.last_name AND con_a.last_name != '')";
                        $query['group'] .= ",con_a.last_name";
                    }
                    break;

                case 'email':
                    if ($count == 0) {
                        $query['where'] = "(con_a.email=con_b.email AND con_a.email != '') ";
                        $query['group'] = "con_a.email";
                    } else {
                        $query['where'] .= "AND (con_a.email=con_b.email AND con_a.email != '')";
                        $query['group'] .= ",con_a.email";
                    }
                    break;

                case 'company':
                    if ($count == 0) {
                        $query['where'] = "(com.company_id=com_b.company_id AND com.company_id != '') ";
                        $query['group'] = "com.company_id";
                    } else {
                        $query['where'] .= "AND (com.company_id=com_b.company_id AND com.company_id != '')";
                        $query['group'] .= ",com.company_id";
                    }
                    break;
            }

            $count++;
        }

        return $query;
    }

    private function getPropertyQueryFilter($criteria)
    {
        $count = 0;
        $whereQuery = "";
        $query = ['where' => '', 'group' => ''];
        foreach ($criteria as $key => $param) {
            switch ($param) {
                case 'name':
                    if ($count == 0) {
                        $query['where'] = "(prop_a.name=prop_b.name AND prop_a.name != '') ";
                        $query['group'] = "prop_a.name";
                    } else {
                        $query['where'] .= " AND (prop_a.name=prop_b.name AND prop_a.name != '') ";
                        $query['group'] .= ",prop_a.name";
                    }
                    break;

                case 'address':
                    if ($count == 0) {
                        $query['where'] = "(prop_a.address=prop_b.address AND ((prop_a.address != ''))) ";
                        $query['group'] = "prop_a.address";
                    } else {
                        $query['where'] .= " AND (prop_a.address=prop_b.address AND ((prop_a.address != ''))) ";
                        $query['group'] .= ",prop_a.address";
                    }
                    break;
                case 'city':
                    if ($count == 0) {
                        $query['where'] = "(prop_a.city=prop_b.city AND prop_a.city != '') ";
                        $query['group'] = "prop_a.city";
                    } else {
                        $query['where'] .= " AND (prop_a.city=prop_b.city AND prop_a.city != '')  ";
                        $query['group'] .= ",prop_a.city";
                    }
                    break;

                case 'state':
                    if ($count == 0) {
                        $query['where'] = "(prop_a.state=prop_b.state AND prop_a.state != '') ";
                        $query['group'] = "prop_a.state";
                    } else {
                        $query['where'] .= " AND (prop_a.state=prop_b.state AND prop_a.state != '')";
                        $query['group'] .= ",prop_a.state";
                    }
                    break;

                case 'zip_code':
                    if ($count == 0) {
                        $query['where'] = "(prop_a.zip_code=prop_b.zip_code AND prop_a.zip_code != '') ";
                        $query['group'] = "prop_a.zip_code";
                    } else {
                        $query['where'] .= " AND (prop_a.zip_code=prop_b.zip_code AND prop_a.zip_code != '') ";
                        $query['group'] .= ",prop_a.zip_code";
                    }
                    break;

                case 'contact':
                    if ($count == 0) {
                        $query['where'] = "(prop_a.contact_id=prop_b.contact_id AND prop_a.contact_id != '') ";
                        $query['group'] = "prop_a.contact_id";
                    } else {
                        $query['where'] = " AND (prop_a.contact_id=prop_b.contact_id AND prop_a.contact_id != '') ";
                        $query['group'] .= ",prop_a.contact_id";
                    }
                    break;

                case 'company':
                    if ($count == 0) {
                        $query['where'] = "(prop_a.company_id=prop_b.company_id AND prop_a.company_id != '') ";
                        $query['group'] = "prop_a.company_id";
                    } else {
                        $query['where'] = " AND (prop_a.company_id=prop_b.company_id AND prop_a.company_id != '') ";
                        $query['group'] .= ",prop_a.company_id";
                    }
                    break;

                case 'property_type':
                    if ($count == 0) {
                        $query['where'] = "(prop_a.property_type=prop_b.property_type AND prop_a.property_type != '') ";
                        $query['group'] = "prop_a.property_type";
                    } else {
                        $query['where'] = " AND (prop_a.property_type=prop_b.property_type AND prop_a.property_type != '') ";
                        $query['group'] .= ",prop_a.property_type";
                    }
                    break;
            }

            $count++;
        }

        return $query;
    }

    function mergeData($selections, $parentId, $type)
    {
        $sameType = true;
        $this->db->trans_begin();
        switch ($type) {
            case 'contact':
                $parentContact = $this->ci->Contact_model->get_contact($parentId, true);
                foreach ($selections as $selection) {
                    $contactId = explode(',', $selection);
                    $contactId = (int) $contactId[0];
                    $selectedContact = $this->ci->Contact_model->get_contact($contactId, true);
                    if ((sizeof($selectedContact) == 0) || (sizeof($parentContact) == 0)) {
                        return false;
                    }

                    if ($selectedContact['contact_id'] != $parentContact['contact_id']) {
                        $this->mergeContact($contactId, (int) $parentId);
                    }
                }
                break;

            case 'company':
                $parentCompany = $this->ci->Company_model->get_company($parentId, true);
                foreach ($selections as $selection) {
                    $companyId = explode(',', $selection);
                    $companyId = (int) $companyId[0];
                    $selectedProperty = $this->ci->Company_model->get_company($companyId, true);
                    if ((sizeof($selectedProperty) == 0) || (sizeof($parentCompany) == 0)) {
                        return false;
                    }

                    if ($selectedProperty['company_id'] != $parentCompany['company_id']) {
                        $this->mergeCompany($companyId, (int) $parentId);
                    }
                }
                break;

            case 'property':
                $parentProperty = $this->ci->Property_model->get_property($parentId, true);
                foreach ($selections as $selection) {
                    $propertyId = explode(',', $selection);
                    $propertyId = (int)$propertyId[0];
                    $selectedProperty = $this->ci->Property_model->get_property($propertyId, true);
                    if ((sizeof($selectedProperty) == 0) || (sizeof($parentProperty) == 0)) {
                        return false;
                    }

                    if ($selectedProperty['property_id'] != $parentProperty['property_id']) {
                        $this->mergeProperty($propertyId, (int) $parentId);
                    }
                }
                break;
        }
        $this->db->trans_complete();

        return true;
    }

    private function mergeContact($contactId, $parentId)
    {
        //update data
        $this->db->where('contact_id', $contactId);
        $this->db->delete($this->contact_table);

        //update address
        $this->db->where('entity_id', $contactId);
        $this->db->where('entity_type', EntityTypes::CONTACT);
        $this->db->update($this->address_table, ['entity_id' => $parentId]);

        //update phone
        $this->db->where('entity_id', $contactId);
        $this->db->where('entity_type', EntityTypes::CONTACT);
        $this->db->update($this->phone_table, ['entity_id' => $parentId]);

        //update property
        $this->db->where('contact_id', $contactId);
        $this->db->update($this->propertyTable, ['contact_id' => $parentId]);
    }

    private function mergeCompany($companyId, $parentId)
    {

        //update data
        $this->db->where('company_id', $companyId);
        $this->db->delete($this->company_table);

        //update company on contact
        $this->db->where('company_id', $companyId);
        $this->db->update($this->contact_table, ['company_id' => $parentId]);

        //update address
        $this->db->where('entity_id', $companyId);
        $this->db->where('entity_type', EntityTypes::COMPANY);
        $this->db->update($this->address_table, ['entity_id' => $parentId]);

        //update phone
        $this->db->where('entity_id', $companyId);
        $this->db->where('entity_type', EntityTypes::COMPANY);
        $this->db->update($this->phone_table, ['entity_id' => $parentId]);

        //update property
        $this->db->where('company_id', $companyId);
        $this->db->update($this->propertyTable, ['company_id' => $parentId]);
    }

    private function mergeProperty($propertyId, $parentId)
    {

        //update data
        $this->db->where('property_id', $propertyId);
        $this->db->delete($this->propertyTable);
    }

    public function companyCriteriaSearch($criteria, $offset = 0)
    {
        $this->db->select("*");
        $this->db->from("companies");
        $this->db->join('addresses', "companies.company_id=addresses.entity_id AND addresses.entity_type='company'", 'left');
        $this->db->join('phones', "companies.company_id=phones.entity_id AND phones.entity_type='company'", 'left');
        $this->db = $this->setCriteria($criteria, $this->db);
        $this->db->limit(100);

        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    public function propertyCriteriaSearch($criteria, $offset = 0)
    {
        $this->db->select("*");
        $this->db->from("properties");
        $this->db = $this->setPropertyCriteria($criteria, $this->db);
        $this->db->limit(100);

        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    public function contactCriteriaSearch($criteria, $offset = 0)
    {
        $this->db->select("*");
        $this->db->from("contacts");
        $this->db->join('addresses', "contacts.contact_id=addresses.entity_id AND addresses.entity_type='contact'", 'left');
        $this->db->join('phones', "contacts.contact_id=phones.entity_id AND phones.entity_type='contact'", 'left');
        $this->db->join('companies', "contacts.company_id=companies.company_id", 'left');
        $this->db = $this->setCriteria($criteria, $this->db);
        $this->db->limit(100);

        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    private function setCriteria($criteria, &$db)
    {
        foreach ($criteria as $key => $param) {
            switch ($key) {
                case 'phone':
                    if (strlen($criteria[$key]) > 0) {
                        if ($criteria[$key] == 'empty') {
                            $db->where(['phones.phone' => null]);
                        } else {
                            $db->where('phones.phone', $param);
                        }
                    }
                    break;

                case 'city':
                    if (strlen($criteria[$key]) > 0) {
                        if ($criteria[$key] == 'empty') {
                            $db->where(['addresses.city' => null]);
                        } else {
                            $db->where('addresses.city', $param);
                        }
                    }
                    break;

                case 'state':
                    if (strlen($criteria[$key]) > 0) {
                        if ($criteria[$key] == 'empty') {
                            $db->where(['addresses.state' => null]);
                        } else {
                            $db->like('addresses.state', $param);
                        }
                    }
                    break;

                case 'zip_code':
                    if (strlen($criteria[$key]) > 0) {
                        if ($criteria[$key] == 'empty') {
                            $db->where(['addresses.zip_code' => null]);
                        } else {
                            $db->where('addresses.zip_code', $param);
                        }
                    }
                    break;

                case 'street_address':
                    if (strlen($criteria[$key]) > 0) {
                        if ($criteria[$key] == 'empty') {
                            $db->where(['addresses.address' => null]);
                        } else {
                            $db->like('addresses.address', trim($param));
                        }
                    }
                    break;

                case 'name':
                    if (strlen($criteria[$key]) > 0) {
                        if ($criteria[$key] == 'empty') {
                            $db->where(['companies.name' => null]);
                        } else {
                            $db->like('companies.name', $param);
                        }
                    }
                    break;


                case 'company':
                    if (strlen($criteria[$key]) > 0) {
                        if ($criteria[$key] == 'empty') {
                            $db->where(['companies.name' => null]);
                        } else {
                            $db->like('companies.name', $param);
                        }
                    }
                    break;

                case 'first_name':
                    if (strlen($criteria[$key]) > 0) {
                        if ($criteria[$key] == 'empty') {
                            $db->where(['contacts.first_name' => null]);
                        } else {
                            $db->like('contacts.first_name', $param);
                        }
                    }
                    break;

                case 'last_name':
                    if (strlen($criteria[$key]) > 0) {
                        if ($criteria[$key] == 'empty') {
                            $db->where(['contacts.last_name' => null]);
                        } else {
                            $db->like('contacts.last_name', $param);
                        }
                    }
                    break;

                case 'email':
                    if (strlen($criteria[$key]) > 0) {
                        if ($criteria[$key] == 'empty') {
                            $db->where(['contacts.email' => null]);
                        } else {
                            $db->like('contacts.email', $param);
                        }
                    }
                    break;
            }
        }

        return $db;
    }

    private function setPropertyCriteria($criteria, &$db)
    {
        foreach ($criteria as $key => $param) {
            switch ($key) {
                case 'city':
                    if (strlen($criteria[$key]) > 0) {
                        if ($criteria[$key] == 'empty') {
                            $db->where(['properties.city' => null]);
                        } else {
                            $db->where('properties.city', $param);
                        }
                    }
                    break;

                case 'state':
                    if (strlen($criteria[$key]) > 0) {
                        if ($criteria[$key] == 'empty') {
                            $db->where(['properties.state' => null]);
                        } else {
                            $db->like('properties.state', $param);
                        }
                    }
                    break;

                case 'zip_code':
                    if (strlen($criteria[$key]) > 0) {
                        if ($criteria[$key] == 'empty') {
                            $db->where(['properties.zip_code' => null]);
                        } else {
                            $db->where('properties.zip_code', $param);
                        }
                    }
                    break;

                case 'street_address':
                    if (strlen($criteria[$key]) > 0) {
                        if ($criteria[$key] == 'empty') {
                            $db->where(['properties.address' => null]);
                        } else {
                            $db->like('properties.address', trim($param));
                        }
                    }
                    break;

                case 'name':
                    if (strlen($criteria[$key]) > 0) {
                        if ($criteria[$key] == 'empty') {
                            $db->where(['properties.name' => null]);
                        } else {
                            $db->like('properties.name', $param);
                        }
                    }
                    break;

                case 'property_type':
                    if (strlen($criteria[$key]) > 0) {
                        if ($criteria[$key] == 'empty') {
                            $db->where(['properties.property_type' => null]);
                        } else {
                            $db->like('properties.property_type', $param);
                        }
                    }
                    break;
            }
        }

        return $db;
    }
}
