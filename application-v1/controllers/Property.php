<?php

require_once APPPATH . "third_party/phpspreadsheet/vendor/autoload.php";

class Property extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Property_model');
        $this->load->model('Search_model');
        if (!(($this->authenticate->isSuperAdmin()) || ($this->authenticate->isAdmin()))) {
            redirect(base_url());
        }
    }

    function index() {

        $searchCriteria = $this->session->userdata('searchCriteria');
        $params = ($searchCriteria != null) ? (array)$searchCriteria : [];
        
        $results = $this->Search_model->search($params, 0);
        $data['properties'] = $results;
        $data['search'] = (object)$params;
        $data['totalProperties'] = $this->Property_model->get_properties_count();
        $data['showAdvanceSearch'] = ($this->session->userdata('advance_search') == 1);
        $data['pageTitle'] = "Properties";

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->load->library("upload");
            $this->load->library("form_validation");
            $config['upload_path'] = 'uploads/';
            $config['allowed_types'] = 'xlsx|xls';
            $config['encrypt_name'] = true;
            $config['overwrite'] = true;
            $this->upload->initialize($config);

            if (empty($_FILES['file']['name'])) {
                $data['uploadError'] = "You must select a valid excel file";
            } else {
                if ($this->upload->do_upload('file')) {
                    $uploadData = $this->upload->data();
                    $fullPath = $uploadData['full_path'];
                    if (file_exists($fullPath)) {
                        $status = $this->bulkUploadToDatabase($uploadData);
                        $this->session->set_flashdata("success", "upload successful");
                        redirect(base_url("property/"));
                    }
                } else {
                    $data['uploadError'] = $this->upload->display_errors();
                }
            }
        }

        $data['body'] = $this->load->view("property/index", $data, true);
        $this->load->view('defaulttemplate', $data);
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
            $this->session->set_userdata('searchCriteria', (object) $searchCriteria);
        }
        
        redirect(base_url("property/"));

    }

    private function validateCriteria($criteria) {
        foreach ($criteria as $key => $value){
            if (($key != 'state') && (strlen($criteria[$key]) == 0)) {
                unset($criteria[$key]);
            } else if (($key == 'state') && (is_array($criteria[$key])) && (sizeof($criteria[$key]) == 0)) {
                unset($criteria[$key]);
            }
        }

        return $criteria;
    }

    function reset_form() {
        $this->session->unset_userdata('result', $results);
        $this->session->unset_userdata('resultCount', $totalRecordsFound);
        $this->session->unset_userdata('searchCriteria', (object) $searchCriteria);
        redirect(base_url("property/"));
    }

    function add() {
        $this->load->library('form_validation');

        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $this->form_validation->set_rules("name", "Property Name", 'required');
            $this->form_validation->set_rules("google_map_link", "Map Link", 'valid_url');
            $contactId = $this->input->post('contact_id', true);
            $company = $this->input->post('company', true);

            if (($this->form_validation->run())) {
                $propertyData = [
                    'name' => $this->input->post('name', true),
                    'store_number' => $this->input->post('store_number', true),
                    'address' => $this->input->post('address', true),
                    'state' => $this->input->post('state', true),
                    'city' => $this->input->post('city', true),
                    'zip_code' => $this->input->post('zip_code', true),
                    'google_map_link' => $this->input->post('google_map_link', true),
                    'property_type' => $this->input->post('property_type', true),
                ];

                if ($contactId != null) {
                    $propertyData['contact_id'] = $contactId;
                } else {
                    $propertyData['company'] = $company;
                }

                $this->Property_model->save_new_property($propertyData);
                $this->session->set_flashdata("success", "property added successfully");
                redirect(base_url('property/'));
            }
        }

        $data['pageTitle'] = "New Property";
        $data['body'] = $this->load->view('property/add', $data, true);
        $this->load->view('defaulttemplate', $data);
    }

    function edit($property_id) {

        $this->load->library('form_validation');
        $data['property'] = $this->Property_model->get_property($property_id);
        $this->session->unset_userdata('edited_property_id');

        if (isset($data['property']['property_id'])) {
            if ($this->input->server('REQUEST_METHOD') == 'POST') {
                $this->form_validation->set_rules("name", "Property Name", 'required');
                $this->session->set_userdata('edited_property_id', $property_id);

                $contactId = $this->input->post('contact_id', true);
                $company = $this->input->post('company', true);
                $propertyType = $this->input->post('property_type', true);

                if (($this->form_validation->run())) {
                    $propertyData = [
                        'name' => $this->input->post('name', true),
                        'store_number' => $this->input->post('store_number', true),
                        'address' => $this->input->post('address', true),
                        'state' => $this->input->post('state', true),
                        'city' => $this->input->post('city', true),
                        'zip_code' => $this->input->post('zip_code', true),
                        'google_map_link' => $this->input->post('google_map_link', true),
                    ];

                    if ($propertyType != null) {
                        $propertyData['property_type'] = $this->input->post('property_type', true);
                    }

                    if ($contactId != null) {
                        $propertyData['contact_id'] = $contactId;
                    }

                    if ($company != null) {
                        $propertyData['company'] = $company;
                    }

                    $this->Property_model->update_property($property_id, $propertyData);
                    $this->session->set_flashdata("success", "property updated");
                    redirect(base_url('property/view/' . $property_id));
                }
            }
        } else {
            redirect(base_url());
        }

        $data['pageTitle'] = "Edit Property";
        $data['body'] = $this->load->view('property/edit', $data, true);
        $this->load->view('defaulttemplate', $data);
    }

    function view($property_id) {
        $data['property'] = $this->Property_model->get_property($property_id);        
        if (isset($data['property']['property_id'])) {
            $data['pageTitle'] = "Property";
            $data['body'] = $this->load->view('property/view', $data, true);
            $this->load->view('defaulttemplate', $data);
        } else {
            redirect(base_url());
        }
    }

    public function search_contacts() {
        $query = $this->input->get('q');
        if (strlen($query) > 0) {
            $contacts = $this->Contact_model->search_contact_by_name($query);
            $response = array();
            foreach ($contacts as $contact) {
                $response[] = array(
                    'id' => $contact->contact_id,
                    'text' => "$contact->first_name $contact->middle_name $contact->last_name [Email: $contact->email]"
                );
            }
            echo json_encode($response);
        } else {
            echo json_encode([]);
            exit;
        }
    }

    function remove($property_id) {
        $property = $this->Property_model->get_property($property_id);
        if (isset($property['property_id'])) {
            $this->Property_model->delete_property($property_id);
            $this->session->set_flashdata("success", "property deleted");
            redirect(base_url('property/'));
        } else {
            redirect(base_url());
        }
    }

    public function unique_name($name) {
        $option = $this->Property_model->get_property_by_name($name);
        $edited_id = $this->session->userdata('edited_property_id');
        if (($option != null) && (($option['property_id'] != $edited_id))) {
            $this->form_validation->set_message('unique_name', 'The {field} already registered');
            return false;
        }
        return true;
    }

    public function property_types() {
        $query = $this->input->get('q');
        if (strlen($query) > 0) {
            $properties = $this->Property_model->get_distinct_property_types($query);
            $response = array();
            foreach ($properties as $property) {
                $response[] = array(
                    'id' => $property->property_type,
                    'text' => $property->property_type
                );
            }
            echo json_encode($response);
        } else {
            echo json_encode([]);
            exit;
        }
    }

    private function bulkUploadToDatabase($uploadData) {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($uploadData['full_path']);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; ++$row) {
            $propertyName = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
            $propertyType = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
            $storeNo = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
            $address = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
            $city = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
            $state = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
            $zipCode = $worksheet->getCellByColumnAndRow(7, $row)->getValue();

            $ownerName = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
            $ownerCompany = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
            $ownerEmail = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
            $ownerAddress = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
            $ownerCity = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
            $ownerState = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
            $ownerZipCode = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
            $ownerPhone1 = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
            $ownerPhone2 = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
            $googleMapLink = $worksheet->getCellByColumnAndRow(17, $row)->getValue();

            if ((strlen($propertyName) > 0)) {
                $optionData[] = array(
                    'name' => $propertyName,
                    'store_number' => $storeNo,
                    'address' => $address,
                    'state' => $state,
                    'city' => $city,
                    'zip_code' => $zipCode,
                    'google_map_link' => $googleMapLink,
                    'property_type' => $propertyType,
                    'owner_name' => $ownerName,
                    'owner_company' => $ownerCompany,
                    'owner_email' => $ownerEmail,
                    'owner_address' => $ownerAddress,
                    'owner_city' => $ownerCity,
                    'owner_state' => $ownerState,
                    'owner_zip_code' => $ownerZipCode,
                    'owner_phone_1' => $ownerPhone1,
                    'owner_phone_2' => $ownerPhone2,
                );
            }
        }

        if (isset($optionData) && (sizeof($optionData) > 0)) {
            $this->Property_model->save_bulk_property($optionData);
        }
    }

    public function states() {
        $query = $this->input->get('q');
        if (strlen($query) > 0) {
            $properties = $this->Property_model->get_distinct_states($query);
            $response = array();
            foreach ($properties as $property) {
                $response[] = array(
                    'id' => $property->state,
                    'text' => $property->state
                );
            }
            echo json_encode($response);
        } else {
            echo json_encode([]);
            exit;
        }
    }

}
