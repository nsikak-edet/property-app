<?php

require_once APPPATH . "third_party/phpspreadsheet/vendor/autoload.php";

class Contact extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Contact_model');
        $this->load->model('Company_model');
        if (!(($this->authenticate->isSuperAdmin()) || ($this->authenticate->isAdmin()))) {
            redirect(base_url());
        }
    }

    function index() {
        
        $params['limit'] = RECORDS_PER_PAGE;
        $filter = $this->input->get('filter');
        if ($filter != null) {
            $params = [
                'city' => $this->input->get('city', true),
                'state' => $this->input->get('state', true),
                'zip_code' => $this->input->get('zip_code', true),
                'email' => $this->input->get('email', true),
                'street_address' => $this->input->get('street_address', true),
                'phone' => $this->input->get('phone', true),
                'company' => $this->input->get('company', true),
                'first_name' => $this->input->get('first_name', true),
                'last_name' => $this->input->get('last_name', true),
                'limit' => null
            ];
        }

        $config['total_rows'] = $this->Contact_model->get_contacts_count();
        $data['contacts'] = $this->Contact_model->get_contacts($params);
        $data['pageTitle'] = "Contacts";

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
                        redirect(base_url("contact/"));
                    }
                } else {
                    $data['uploadError'] = $this->upload->display_errors();
                }
            }
        }

        $data['body'] = $this->load->view('contact/index', $data, true);
        $this->load->view('defaulttemplate', $data);
    }

    public function states() {
        $query = $this->input->get('q');
        if (strlen($query) > 0) {
            $contacts = $this->Contact_model->get_distinct_states($query);
            $response = array();
            foreach ($contacts as $contact) {
                $response[] = array(
                    'id' => $contact->state,
                    'text' => $contact->state
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

            $this->form_validation->set_rules("first_name", "First Name", 'required|callback_unique_name');
            $this->form_validation->set_rules("last_name", "Last Name", 'required|callback_unique_name');
            $this->form_validation->set_rules("email", "Email", 'valid_email|callback_unique_email');
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
                $contactData = $this->getContactData();
                $this->Contact_model->save_new_contact($contactData);

                $this->session->set_flashdata("success", "contact added successfully");
                redirect(base_url('contact/'));
            }
        }

        $data['pageTitle'] = "New Contact";
        $data['body'] = $this->load->view('contact/add', $data, true);
        $this->load->view('defaulttemplate', $data);
    }

    function edit($contact_id) {

        $this->load->library('form_validation');
        $data['contact'] = $this->Contact_model->get_contact($contact_id);
        $this->session->unset_userdata('edited_contact_id');
        $this->session->unset_userdata('edited_email');

        if (isset($data['contact']['contact_id'])) {
            if ($this->input->server('REQUEST_METHOD') == 'POST') {
                $addresses = $this->input->post('address[]', true);
                $phones = $this->input->post('phones[]', true);
                $this->session->set_userdata('edited_email', $data['contact']['email']);
                $this->session->set_userdata('edited_contact_id', $data['contact']['contact_id']);

                $this->form_validation->set_rules("first_name", "First Name", 'required|callback_unique_name');
                $this->form_validation->set_rules("last_name", "Last Name", 'required|callback_unique_name');
                $this->form_validation->set_rules("email", "Email", 'valid_email|callback_unique_email');
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
                    $contact_data = $this->getContactData();
                    $this->Contact_model->update_contact_data($contact_data, $contact_id);
                    $this->session->set_flashdata("success", "contact updated");
                    redirect(base_url('contact/view/' . $contact_id));
                }
            }
        } else {
            redirect(base_url());
        }

        $data['pageTitle'] = "Edit Contact";
        $data['body'] = $this->load->view('contact/edit', $data, true);
        $this->load->view('defaulttemplate', $data);
    }

    function view($contact_id) {

        $this->load->library('form_validation');
        $data['contact'] = $this->Contact_model->get_contact($contact_id);
        $activeTab = $this->input->get('tab');
        $data['pageTitle'] = "Contact";
        $data['activeTab'] = 'property';

        if (isset($data['contact']['contact_id'])) {
            $data['activeTabBody'] = $this->load->view('contact/propertyactivetab', $data, true);
            $data['contactSideBody'] = $this->load->view('contact/contactside', $data, true);
            $data['contactNav'] = $this->load->view('contact/contactnav', $data, true);

            $data['body'] = $this->load->view('contact/view', $data, true);
            $this->load->view('defaulttemplate', $data);
        } else {
            redirect(base_url());
        }
    }

    function remove($contact_id) {
        $contact = $this->Contact_model->get_contact($contact_id);
        if (isset($contact['contact_id'])) {
            $this->Contact_model->delete_contact($contact_id);
            $this->session->set_flashdata("success", "contact deleted");
            redirect(base_url('contact/'));
        } else {
            redirect(base_url());
        }
    }

    public function unique_name($name) {
        $name = [
            'first_name' => $this->input->post('first_name', true),
            'middle_name' => $this->input->post('middle_name', true),
            'last_name' => $this->input->post('last_name', true),
        ];
        $option = $this->Contact_model->get_contact_by_name($name);
        $edited_id = $this->session->userdata('edited_contact_id');
        if (($option != null) && (($option['contact_id'] != $edited_id))) {
            $this->form_validation->set_message('unique_name', 'The {field} already registered');
            return false;
        }

        return true;
    }

    public function unique_email($email) {
        $option = $this->Contact_model->get_contact_by_email($email);
        $editedEmail = $this->session->userdata('edited_email');
        if (($option != null) && (($option['email'] != $editedEmail))) {
            $this->form_validation->set_message('unique_email', 'The {field} already registered');
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

    private function getContactData() {
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
        $formData['first_name'] = $this->input->post('first_name', true);
        $formData['last_name'] = $this->input->post('last_name', true);
        $formData['company_name'] = $this->input->post('company_name', true);
        $formData['email'] = $this->input->post('email', true);
        $formData['lead_gen_type'] = $this->input->post('lead_gen_type', true);

        return $formData;
    }

    public function all() {
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

    private function bulkUploadToDatabase($uploadData) {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($uploadData['full_path']);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; ++$row) {
            $firstName = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
            $lastName = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
            if ((strlen($firstName) > 0) && (strlen($lastName) > 0)) {
                $optionData[] = array(
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'name' => [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                    ],
                    'email' => $worksheet->getCellByColumnAndRow(3, $row)->getValue(),
                    'lead_gen_type' => $worksheet->getCellByColumnAndRow(4, $row)->getValue(),
                    'address' => $worksheet->getCellByColumnAndRow(5, $row)->getValue(),
                    'city' => $worksheet->getCellByColumnAndRow(6, $row)->getValue(),
                    'state' => $worksheet->getCellByColumnAndRow(7, $row)->getValue(),
                    'zip_code' => $worksheet->getCellByColumnAndRow(8, $row)->getValue(),
                    'company' => $worksheet->getCellByColumnAndRow(9, $row)->getValue(),
                    'phone_1' => $worksheet->getCellByColumnAndRow(10, $row)->getValue(),
                    'phone_2' => $worksheet->getCellByColumnAndRow(11, $row)->getValue()
                );
            }
        }

        if (isset($optionData) && (sizeof($optionData) > 0)) {
            $this->Contact_model->save_bulk_contact($optionData);
        }
    }

}
