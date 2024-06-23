<?php

require_once APPPATH . "third_party/phpspreadsheet/vendor/autoload.php";

class Company extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Company_model');
        if (!(($this->authenticate->isSuperAdmin()) || ($this->authenticate->isAdmin()))) {
            redirect(base_url());
        }
    }

    function index() {
        $params['limit'] = RECORDS_PER_PAGE;
        $config['base_url'] = 'company/?';
        $config['total_rows'] = $this->Company_model->get_companies_count();
        $data['pagination'] = initializePagination($config['total_rows'], $config['base_url'], true);
        
        $filter = $this->input->get('filter');
        if ($filter != null) {            
            $params = [
                'city' => $this->input->get('city', true),
                'state' => $this->input->get('state', true),
                'zip_code' => $this->input->get('zip_code', true),                
                'street_address' => $this->input->get('street_address', true),
                'phone' => $this->input->get('phone', true),                
                'name' => $this->input->get('name', true),               
                'limit' => null
            ];
        }
        
        $data['companies'] = $this->Company_model->get_companies($params);               
        $data['pageTitle'] = "Companies";

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->load->library("upload");
            $this->load->library("form_validation");
            $config['upload_path'] = 'uploads/';
            $config['allowed_types'] = 'xlsx|xls';
            $config['encrypt_name'] = true;
            $config['overwrite'] = true;
            $this->upload->initialize($config);

            if (empty($_FILES['file']['name'])) {
                $data['uploadError'] = "You must select a valid csv file";
            } else {
                if ($this->upload->do_upload('file')) {
                    $uploadData = $this->upload->data();
                    $fullPath = $uploadData['full_path'];
                    if (file_exists($fullPath)) {
                        $status = $this->bulkUploadToDatabase($uploadData);
                        $this->session->set_flashdata("success", "upload successful");
                        redirect(base_url("company/"));
                    }
                } else {
                    $data['uploadError'] = $this->upload->display_errors();
                }
            }
        }

        $data['body'] = $this->load->view('company/index', $data, true);
        $this->load->view('defaulttemplate', $data);
    }
    
    public function states() {
        $query = $this->input->get('q');
        if (strlen($query) > 0){
            $states = $this->Contact_model->get_distinct_states($query);
            $response = array();
            foreach ($states as $state) {
                $response[] = array(
                    'id' => $state->state,
                    'text' => $state->state
                );
            }
            echo json_encode($response);
        } else {
            echo json_encode([]);
            exit;
        }
    }

    function add() {
        $this->load->library('form_validation');

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $addresses = $this->input->post('address[]', true);
            $phones = $this->input->post('phones[]', true);

            $this->form_validation->set_rules("company_name", "Company Name", 'required|callback_unique_name');
            if (is_array($addresses)) {
                foreach ($addresses as $index => $address) {
                    if ((isset($address['address'])) && strlen($address['address']) > 0) {
                        $this->form_validation->set_rules("address[$index][address]", "Street Address", 'trim');
                    }

                    if ((isset($address['state'])) && strlen($address['state']) > 0) {
                        $this->form_validation->set_rules("address[$index][state]", "State", 'trim');
                    }

                    if ((isset($address['city'])) && strlen($address['city']) > 0) {
                        $this->form_validation->set_rules("address[$index][city]", "City", 'trim');
                    }

                    if ((isset($address['zip_code'])) && strlen($address['zip_code']) > 0) {
                        $this->form_validation->set_rules("address[$index][zip_code]", "Zip Code", 'trim|numeric');
                    }
                }
            }

            if (is_array($phones)) {
                foreach ($phones as $index => $phone) {
                    if ((isset($phone['mobile'])) && strlen($phone['mobile']) > 0) {
                        $this->form_validation->set_rules("phones[$index][mobile]", "Phone", 'callback_valid_phone');
                    }
                }
            }

            if (($this->form_validation->run())) {
                $companyData = $this->getCompanyData();
                $this->Company_model->save_new_company($companyData);

                $this->session->set_flashdata("success", "company added successfully");
                redirect(base_url('company/'));
            }
        }

        $data['pageTitle'] = "New Company";
        $data['body'] = $this->load->view('company/add', $data, true);
        $this->load->view('defaulttemplate', $data);
    }

    function edit($company_id) {

        $this->load->library('form_validation');
        $data['company'] = $this->Company_model->get_company($company_id);
        $this->session->unset_userdata('edited_company_id');

        if (isset($data['company']['company_id'])) {
            if ($this->input->server('REQUEST_METHOD') == 'POST') {
                $addresses = $this->input->post('address[]', true);
                $phones = $this->input->post('phones[]', true);
                $this->session->set_userdata('edited_company_id', $company_id);

                $this->form_validation->set_rules("company_name", "Company Name", 'required|callback_unique_name');
                if (is_array($addresses)) {
                    foreach ($addresses as $index => $address) {
                        if ((isset($address['address'])) && strlen($address['address']) > 0) {
                            $this->form_validation->set_rules("address[$index][address]", "Street Address", 'trim');
                        }

                        if ((isset($address['state'])) && strlen($address['state']) > 0) {
                            $this->form_validation->set_rules("address[$index][state]", "State", 'trim|alpha_numeric_spaces');
                        }

                        if ((isset($address['city'])) && strlen($address['city']) > 0) {
                            $this->form_validation->set_rules("address[$index][city]", "City", 'trim|alpha_numeric_spaces');
                        }

                        if ((isset($address['zip_code'])) && strlen($address['zip_code']) > 0) {
                            $this->form_validation->set_rules("address[$index][zip_code]", "Zip Code", 'trim|numeric');
                        }
                    }
                }

                if (is_array($phones)) {
                    foreach ($phones as $index => $phone) {
                        if ((isset($phone['mobile'])) && strlen($phone['mobile']) > 0) {
                            $this->form_validation->set_rules("phones[$index][mobile]", "Phone", 'callback_valid_phone');
                        }
                    }
                }

                if (($this->form_validation->run())) {
                    $company_data = $this->getCompanyData();
                    $this->Company_model->update_company_data($company_data, $company_id);
                    $this->session->set_flashdata("success", "company updated");
                    redirect(base_url('company/view/' . $company_id));
                }
            }
        } else {
            redirect(base_url());
        }

        $data['pageTitle'] = "Edit Company";
        $data['body'] = $this->load->view('company/edit', $data, true);
        $this->load->view('defaulttemplate', $data);
    }

    function view($company_id) {

        $this->load->library('form_validation');
        $data['company'] = $this->Company_model->get_company($company_id);
        $activeTab = $this->input->get('tab');
        
        if (isset($data['company']['company_id'])) {
            $data['pageTitle'] = "Company";
            $data['activeTabBody'] = $this->load->view('company/contactactivetab', $data, true);
            $data['companySideBody'] = $this->load->view('company/companyside', $data, true);
            $data['activeTab'] = 'contact';

            if ($activeTab == 'property') {
                $data['activeTabBody'] = $this->load->view('company/propertyactivetab', $data, true);
                $data['activeTab'] = 'property';
            }

            $data['companyNav'] = $this->load->view('company/companynav', $data, true);
            $data['body'] = $this->load->view('company/view', $data, true);
            $this->load->view('defaulttemplate', $data);
        } else {
            redirect(base_url());
        }
    }

    function remove($company_id) {
        $company = $this->Company_model->get_company($company_id);
        if (isset($company['company_id'])) {
            $this->Company_model->delete_company($company_id);
            $this->session->set_flashdata("success", "company deleted");
            redirect(base_url('company/'));
        } else {
            redirect(base_url());
        }
    }

    public function unique_name($name) {
        $option = $this->Company_model->get_company_by_name($name);
        $edited_id = $this->session->userdata('edited_company_id');
        if (($option != null) && (($option['company_id'] != $edited_id))) {
            $this->form_validation->set_message('unique_name', 'The {field} already registered');
            return false;
        }
        return true;
    }
    
    public function valid_phone($phone) {
        $formattedPhone = formatPhoneNumber($phone);
        if (!(strlen($formattedPhone) == 14)) {
            $this->form_validation->set_message('valid_phone', 'The {field} is invalid');
            return false;
        }
        return true;
    }

    private function getCompanyData() {
        $addressesInput = $this->input->post('address[]', true);
        $phonesInput = $this->input->post('phones[]', true);

        $formData = [];
        $phones = [];
        if ((is_array($phonesInput))) {
            foreach ($phonesInput as $phone) {
                $phones[] = (isset($phone['mobile'])) ? formatPhoneNumber($phone['mobile']) : "";
                
            }
        }

        $addreses = [];
        if ((is_array($addressesInput))) {
            $newAddress = [];
            foreach ($addressesInput as $address) {
                $newAddress['address'] = (isset($address['address'])) ? $address['address'] : "";
                $newAddress['state'] = (isset($address['state'])) ? $address['state'] : "";
                $newAddress['city'] = (isset($address['city'])) ? $address['city'] : "";
                $newAddress['zip_code'] = (isset($address['zip_code'])) ? $address['zip_code'] : "";
                $addreses[] = $newAddress;
            }
        }

        $formData['phones'] = $phones;
        $formData['addresses'] = $addreses;
        $formData['company_name'] = $this->input->post('company_name', true);

        return $formData;
    }

    private function bulkUploadToDatabase($uploadData) {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($uploadData['full_path']);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; ++$row) {
            $companyName = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
            if (strlen($companyName) > 0) {
                $optionData[] = array(
                    'name' => $companyName,
                    'address' => $worksheet->getCellByColumnAndRow(2, $row)->getValue(),
                    'city' => $worksheet->getCellByColumnAndRow(3, $row)->getValue(),
                    'state' => $worksheet->getCellByColumnAndRow(4, $row)->getValue(),                    
                    'zip_code' => $worksheet->getCellByColumnAndRow(5, $row)->getValue(),
                    'phone_1' => formatPhoneNumber($worksheet->getCellByColumnAndRow(6, $row)->getValue()),
                    'phone_2' => formatPhoneNumber($worksheet->getCellByColumnAndRow(7, $row)->getValue()),
                );
            }
        }

        if (isset($optionData) && (sizeof($optionData) > 0)) {
            $this->Company_model->save_bulk_company($optionData);
        }
    }
    
    public function companies() {
        $query = $this->input->get('q');
        if (strlen($query) > 0) {
            $companies = $this->Company_model->getDistinctCompanies($query);
            $response = array();
            foreach ($companies as $company) {
                $response[] = array(
                    'id' => $company->name,
                    'text' => $company->name
                );
            }
            echo json_encode($response);
        } else {
            echo json_encode([]);
            exit;
        }
    }

}
