<?php

require_once APPPATH . "third_party/phpspreadsheet/vendor/autoload.php";

class Duplicate extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Property_model');
        $this->load->model('Duplicate_model');
        $this->load->model('Company_model');
        if (!(($this->authenticate->isSuperAdmin()) || ($this->authenticate->isAdmin()))) {
            redirect(base_url());
        }
    }

    function index() {

        $jsonContactFields = json_encode('');
        $jsonSearchCriteria = json_encode('');
        $data['pageTitle'] = "Duplicates";

        $states = $this->Contact_model->get_distinct_states(null, 0);
        $data['states'] = $states;

        $this->session->unset_userdata('companyredirect');

        $data['contactDuplicates'] = [];
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $duplicates = $this->input->post('duplicates', true);
            $duplicateContactFields = $this->input->post('contact_duplicates', true);

            $criteria = [
                'phone' => $this->input->post('phone', true),
                'street_address' => $this->input->post('street_address', true),
                'city' => $this->input->post('city', true),
                'state' => $this->input->post('state', true),
                'zip_code' => $this->input->post('zip_code', true),
                'email' => $this->input->post('email', true),
                'company' => $this->input->post('company', true),
                'first_name' => $this->input->post('first_name', true),
                'last_name' => $this->input->post('last_name', true),
            ];

            $criteria = array_filter($criteria);
            if (sizeof($criteria) > 0) {
                $contactDuplicates = $this->Duplicate_model->contactCriteriaSearch($criteria);
                $data['contactDuplicates'] = $contactDuplicates;
                $data['jsonCriteria'] = (object) $criteria;
                $this->session->set_userdata('duplicateCriteria', json_encode($criteria));
            } else if (($duplicateContactFields != null) && ($duplicates == 'contact')) {
                $contactDuplicates = $this->Duplicate_model->searchContact($duplicateContactFields);
                $data['contactDuplicates'] = $contactDuplicates;
                $jsonContactFields = json_encode($duplicateContactFields);
                $this->session->set_userdata('jsonCompanyFields', $jsonContactFields);
            }
        } else {
            $jsonContactFieldsCached = $this->session->userdata('jsonContactFields');
            $jsonCriteriaCached = $this->session->userdata('duplicateCriteria');
            $jsonContactFields = ($jsonContactFieldsCached != null) ? $jsonContactFieldsCached : $jsonContactFields;
            $jsonCriteria = ($jsonCriteriaCached != null) ? $jsonCriteriaCached : $jsonSearchCriteria;

            if (strlen($jsonContactFields) > 2) {
                $data['contactDuplicates'] = $this->Duplicate_model->searchContact(json_decode($jsonContactFields, true));
            } else if (strlen($jsonCriteria) > 2) {
                $data['contactDuplicates'] = $this->Duplicate_model->contactCriteriaSearch(json_decode($jsonCriteria, true));
            }

            $data['jsonCriteria'] = (object) json_decode($jsonCriteria, true);
        }

        $data['jsonContactFields'] = $jsonContactFields;
        $data['body'] = $this->load->view("duplicate/contact", $data, true);
        $this->load->view('defaulttemplate', $data);
    }

    function company() {

        $jsonCompanyFields = json_encode('');
        $jsonSearchCriteria = json_encode('');
        $data['pageTitle'] = "Duplicates";

        $states = $this->Contact_model->get_distinct_states(null, 0);
        $data['states'] = $states;

        $data['companyDuplicates'] = [];
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $duplicates = $this->input->post('duplicates', true);
            $duplicateCompaniesFields = $this->input->post('company_duplicates', true);

            $criteria = [
                'phone' => $this->input->post('phone', true),
                'street_address' => $this->input->post('street_address', true),
                'city' => $this->input->post('city', true),
                'state' => $this->input->post('state', true),
                'zip_code' => $this->input->post('zip_code', true),
                'name' => $this->input->post('name', true)
            ];

            $criteria = array_filter($criteria);
            if (sizeof($criteria) > 0) {
                $companyDuplicates = $this->Duplicate_model->companyCriteriaSearch($criteria);
                $data['companyDuplicates'] = $companyDuplicates;
                $data['jsonCriteria'] = (object) $criteria;
                $this->session->set_userdata('duplicateCriteria', json_encode($criteria));
            } else if (($duplicateCompaniesFields != null) && ($duplicates == 'company')) {
                $companyDuplicates = $this->Duplicate_model->searchCompany($duplicateCompaniesFields);
                $data['companyDuplicates'] = $companyDuplicates;
                $jsonCompanyFields = json_encode($duplicateCompaniesFields);
                $this->session->set_userdata('jsonCompanyFields', $jsonCompanyFields);
            }
        } else {
            $jsonCompanyFieldsCached = $this->session->userdata('jsonCompanyFields');
            $jsonCriteriaCached = $this->session->userdata('duplicateCriteria');
            $jsonCompanyFields = ($jsonCompanyFieldsCached != null) ? $jsonCompanyFieldsCached : $jsonCompanyFields;
            $jsonCriteria = ($jsonCriteriaCached != null) ? $jsonCriteriaCached : $jsonSearchCriteria;

            if (strlen($jsonCompanyFields) > 2) {
                $data['companyDuplicates'] = $this->Duplicate_model->searchCompany(json_decode($jsonCompanyFields, true));
            } else if (strlen($jsonCriteria) > 2) {
                $data['companyDuplicates'] = $this->Duplicate_model->companyCriteriaSearch(json_decode($jsonCriteria, true));
            }

            $data['jsonCriteria'] = (object) json_decode($jsonCriteria, true);
        }

        $this->session->set_userdata('companyredirect', 1);
        $data['jsonCompanyFields'] = $jsonCompanyFields;
        $data['body'] = $this->load->view("duplicate/company", $data, true);
        $this->load->view('defaulttemplate', $data);
    }

    function contact() {
        
    }

    function clear_company_search() {
        $this->session->unset_userdata('jsonCompanyFields');
        $this->session->unset_userdata('duplicateCriteria');
        redirect(base_url("duplicate/company"));
    }
    
    function clear_contact_search() {
        $this->session->unset_userdata('jsonContactFields');
        $this->session->unset_userdata('duplicateCriteria');
        redirect(base_url("duplicate/"));
    }


    function search() {
        $data['showAdvanceSearch'] = ($this->session->userdata('advance_search') == 1);
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $offset = 0;
            $name = $this->input->post('name', true);
            $propertyCount = $this->input->post('property_count', true);
            $propertyStateOwner = $this->input->post('property_state', true);
            $company = $this->input->post('company', true);
            $firstName = $this->input->post('first_name', true);
            $lastName = $this->input->post('last_name', true);
            $searchCriteria = array(
                'name' => $name,
                'city' => $this->input->post('city', true),
                'state' => $this->input->post('state', true),
                'zip_code' => $this->input->post('zip_code', true),
                'store_no' => $this->input->post('store_no', true),
                'street_address' => $this->input->post('street_address', true),
                'property_type' => $this->input->post('property_type', true),
                'property_count' => $propertyCount,
                'lead_gen_type' => $this->input->post('lead_gen_type', true),
                'company' => $company,
                'first_name' => $firstName,
                'last_name' => $lastName
            );

            $validatedCriteria = $this->validateCriteria($searchCriteria);
            $results = $this->Search_model->search($validatedCriteria, $offset);
            $this->session->set_userdata('result', $results);
            $this->session->set_userdata('searchCriteria', (object) $searchCriteria);
        }

        redirect(base_url("property/"));
    }

    public function merge() {
        $selections = $this->input->post('merge_selections[]', true);
        $parentId = $this->input->post('parent_record', true);
        $companyRedirect = $this->session->userdata('companyredirect');
        $type = ($companyRedirect == null) ? "contacts" : "companies";
        if ((is_array($selections)) && (sizeof($selections) > 0) && ($selections != null) && ($parentId != null)) {
            $mergeType = $this->input->post('merge_type');
            $isMerged = $this->Duplicate_model->mergeData($selections, $parentId, $mergeType);
            if ($isMerged) {
                $this->session->set_flashdata('success', 'merge successful');
            } else {
                $this->session->set_flashdata('info', 'merge selections does not match');
            }
        } else {
            $this->session->set_flashdata('info', "you must select $type to merge");
        }

        if ($companyRedirect != null) {
            redirect(base_url("duplicate/company"));
        } else {
            redirect(base_url("duplicate/"));
        }
    }

}
