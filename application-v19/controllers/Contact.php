<?php

require_once APPPATH . "third_party/phpspreadsheet/vendor/autoload.php";

class Contact extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('Contact_model');
		$this->load->model('Company_model');
		$this->load->model('Acquisitioncriteria_model');
		$this->load->model('Activebuyersearch_model');
		if (!(($this->authenticate->isSuperAdmin()) || ($this->authenticate->isAdmin()))) {
			redirect(base_url());
		}
	}

	function index()
	{

		$params['limit'] = RECORDS_PER_PAGE;
		$data['contacts'] = array();
		$data['pageTitle'] = "Contacts";

		$offset = ($this->input->get('per_page') != null) ? $this->input->get('per_page') : 0;
		$limit = RECORDS_PER_PAGE;

		$params = array(
			'city' => $this->input->get('city', true),
			'state' => $this->input->get('state', true),
			'zip_code' => $this->input->get('zip_code', true),
			'email' => $this->input->get('email', true),
			'street_address' => $this->input->get('street_address', true),
			'phone' => $this->input->get('phone', true),
			'company' => $this->input->get('company', true),
			'first_name' => $this->input->get('first_name', true),
			'last_name' => $this->input->get('last_name', true),
			'tax_record_sent_date' => $this->input->get('tax_record_sent_date', true),
			'last_dial' => $this->input->get('last_dial', true),
			'last_update' => $this->input->get('last_update', true),
			'offset' => $offset,
			'limit' => $limit,
			'filter' => ($this->input->get('filter') != null) ? 1 : 0
		);

		$this->session->set_userdata('contact_filter', $params);
		$data['contacts'] = $this->Contact_model->get_contacts($params);
		$config['total_rows'] = $this->Contact_model->get_contacts_count($params);
		$config['base_url'] = 'contact/?';
		$data['totalRecords'] = $config['total_rows'];
		$data['offset'] = $offset;
		$data['pagination'] = initializePagination($config['total_rows'], $config['base_url'], true);

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

		$range = $this->input->get('tax_record_sent_date', true);
		$lastUpdateRange =  $this->input->get('last_update', true);
		$lastDialRange =  $this->input->get('last_dial', true);
		$lastUpdateRange = explode('-', $lastUpdateRange);
		$lastDialRange = explode('-', $lastDialRange);
		$range = explode('-', $range);

		$data['startDate'] = (isset($range[0])) ? $range[0] : null;
		$data['lastDialStartDate'] = (isset($lastDialRange[0])) ? $lastDialRange[0] : null;
		$data['lastUpdateStartDate'] = (isset($lastUpdateRange[0])) ? $lastUpdateRange[0] : null;
		$data['endDate'] = (isset($range[1])) ? $range[1] : null;
		$data['lastUpdateEndDate'] = (isset($lastUpdateRange[1])) ? $lastUpdateRange[1] : null;
		$data['lastDialEndDate'] = (isset($lastDialRange[1])) ? $lastDialRange[1] : null;
		$data['body'] = $this->load->view('contact/index', $data, true);
		$this->load->view('defaulttemplate', $data);
	}

	public function states()
	{
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

	function add()
	{
		$this->load->library('form_validation');

		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$addresses = $this->input->post('address[]', true);
			$phones = $this->input->post('phones[]', true);

			//			$this->form_validation->set_rules("first_name", "First Name", 'required|callback_unique_name');
			//			$this->form_validation->set_rules("last_name", "Last Name", 'required|callback_unique_name');
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
						$this->form_validation->set_rules("address[$index][zip_code]", "Zip Code", 'trim');
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

				$contactId = $this->Contact_model->save_new_contact($contactData);
				$this->session->set_flashdata("success", "contact added successfully");
				redirect(base_url("contact/view/$contactId"));
			}
		}

		$data['pageTitle'] = "New Contact";
		$data['body'] = $this->load->view('contact/add', $data, true);
		$this->load->view('defaulttemplate', $data);
	}

	function edit($contact_id)
	{

		$this->load->library('form_validation');
		$data['contact'] = $this->Contact_model->get_contact($contact_id);
		$this->session->unset_userdata('edited_contact_id');
		$this->session->unset_userdata('edited_email');

		if (isset($data['contact']['contact_id'])) {
			if ($this->input->server('REQUEST_METHOD') == 'POST') {
				$addresses = $this->input->post('address[]', true);
				$phones = $this->input->post('phones[]', true);
				$detachCompany = $this->input->post('detach_company', true);

				$this->session->set_userdata('edited_email', $data['contact']['email']);
				$this->session->set_userdata('edited_contact_id', $data['contact']['contact_id']);

				// $this->form_validation->set_rules("first_name", "First Name", 'required|callback_unique_name');
				// $this->form_validation->set_rules("last_name", "Last Name", 'required|callback_unique_name');
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
							$this->form_validation->set_rules("address[$index][zip_code]", "Zip Code", 'trim');
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
					if ($detachCompany != null) {
						$contact_data['detach_company'] = true;
					}

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


	function acquisition_criteria($contact_id)
	{

		$this->load->library('form_validation');
		$pageType = $this->input->get('page-type', true);

		$criteriaList = $this->Acquisitioncriteria_model->get_criteria_by_contact($contact_id);
		$data['contact'] = $this->Contact_model->get_contact($contact_id);

		// print_r($data['contact']);

		$data['criteriaList'] = $criteriaList;
		$data['pageType'] = $pageType;

		if ($pageType == 'contact') {
			$data['pageTitle'] = "Contact";
			$data['activeTab'] = 'search-criteria';

			$data['activeTabBody'] = $this->load->view('contact/searchcriteria', $data, true);
			$data['contactSideBody'] = $this->load->view('contact/contactside', $data, true);
			$data['contactNav'] = $this->load->view('contact/contactnav', $data, true);
			$data['body'] = $this->load->view('contact/view', $data, true);
			$this->load->view('defaulttemplate', $data);
		} else {
			$criteriaList = $this->Acquisitioncriteria_model->get_criteria_by_company($data['contact']['company_id']);
			$data['pageTitle'] = "Company";
			$data['criteriaList'] = $criteriaList;
			$data['company'] = $this->Company_model->get_company($data['contact']['company_id']);			
			$data['activeTabBody'] = $this->load->view('contact/searchcriteria', $data, true);
			$data['companySideBody'] = $this->load->view('company/companyside', $data, true);
			$data['activeTab'] = 'search-criteria';

			$data['companyNav'] = $this->load->view('company/companynav', $data, true);
			$data['body'] = $this->load->view('company/view', $data, true);
			$this->load->view('defaulttemplate', $data);
		}
	}

	function edit_criteria($criteria_id)
	{

		$this->load->library('form_validation');
		$pageType = $this->input->get('page-type', true);

		$criteria = $this->Acquisitioncriteria_model->get_criteria($criteria_id);
		$data['pageType'] = $pageType;
		$data['criteria'] = $criteria;

		if ($criteria != null) {
			if ($this->input->server('REQUEST_METHOD') == 'POST') {

				$contactRedirect = base_url('contact/acquisition_criteria/' . $criteria->contact_id . "?tab=search-criteria&page-type=contact");
				$companyRedirect = base_url('contact/acquisition_criteria/' . $criteria->contact_id . "?tab=search-criteria&page-type=company");

				$this->form_validation->set_rules("tenant_name", "Tenant Name", 'trim');
				$this->form_validation->set_rules("min_asking_price", "Min Price Range", 'trim|numeric');
				$this->form_validation->set_rules("max_asking_price", "Max Price Range", 'trim|numeric');
				$this->form_validation->set_rules("min_asking_rate", "Min Cap Rate", 'trim|numeric');
				$this->form_validation->set_rules("max_asking_rate", "Max Cap Rate", 'trim|numeric');

				if (($this->form_validation->run())) {

					$tenants = $this->input->post('tenants[]', true);
					$pageType = $this->input->post('page_type', true);
					$availabilityStatus = $this->input->post('availability_status[]', true);
					$buyerStatus = $this->input->post('buyer_status', true);
					$criteriaUpdateDate = $this->input->post('criteria_update_date', true);
					$states = $this->input->post('states[]', true);
					$landlordResponsibilities =  $this->input->post('landlord_responsibilities', true);
					$propertyType = $this->input->post('property_type[]', true);

					$searchCriteria = array(
						'comment' => $this->input->post('comment', true),
						'property_type' => (is_array($propertyType) && (sizeof($propertyType) > 0)) ? implode(", ", $propertyType) : "",
						'lease_term_remaining' => $this->input->post('lease_term_remaining', true),
						'landlord_responsibilities' => (is_array($landlordResponsibilities) && (sizeof($landlordResponsibilities) > 0)) ? implode(", ", $landlordResponsibilities) : "",
						'availability_status' => (is_array($availabilityStatus) && (sizeof($availabilityStatus) > 0)) ? implode(", ", $availabilityStatus) : "",
						'buyer_status' => (is_array($buyerStatus) && (sizeof($buyerStatus) > 0)) ? implode(", ", $buyerStatus) : "",
						'criteria_update_date' => ($criteriaUpdateDate != null) ? date("Y-m-d", strtotime($criteriaUpdateDate)) : null,
						'min_asking_price' => $this->input->post('min_asking_price', true),
						'max_asking_price' => $this->input->post('max_asking_price', true),
						'min_asking_rate' => $this->input->post('min_asking_rate', true),
						'max_asking_rate' => $this->input->post('max_asking_rate', true),
						'states' => '',
						'tenant_name' => '',
					);

					if (is_array($states) && (sizeof($states) > 0)) {
						$searchCriteria['states'] = implode(", ", $states);
					}

					if (is_array($tenants) && (sizeof($tenants) > 0)) {
						$searchCriteria['tenant_name'] =  implode(", ", $tenants);
					}

					$this->Acquisitioncriteria_model->update($criteria_id, $searchCriteria);
					$this->session->set_flashdata("success", "contact search criteria updated");
					redirect(($pageType == 'contact') ? $contactRedirect : $companyRedirect);
				}
			}
		} else {
			redirect(base_url());
		}

		$data['pageTitle'] = 'Edit Criteria';
		$data['body'] = $this->load->view('contact/editcriteria', $data, true);
		$this->load->view('defaulttemplate', $data);
	}

	function add_criteria($contact_id)
	{

		$this->load->library('form_validation');
		$pageType = $this->input->get('page-type', true);

		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$contactRedirect = base_url('contact/acquisition_criteria/' . $contact_id . "?tab=search-criteria&page-type=contact");
			$companyRedirect = base_url('contact/acquisition_criteria/' . $contact_id . "?tab=search-criteria&page-type=company");

			$this->form_validation->set_rules("tenant_name", "Tenant Name", 'trim');
			$this->form_validation->set_rules("min_asking_price", "Min Price Range", 'trim|numeric');
			$this->form_validation->set_rules("max_asking_price", "Max Price Range", 'trim|numeric');
			$this->form_validation->set_rules("min_asking_rate", "Min Cap Rate", 'trim|numeric');
			$this->form_validation->set_rules("max_asking_rate", "Max Cap Rate", 'trim|numeric');

			if (($this->form_validation->run())) {

				$tenants = $this->input->post('tenants[]', true);
				$pageType = $this->input->post('page_type', true);
				$availabilityStatus = $this->input->post('availability_status', true);
				$buyerStatus = $this->input->post('buyer_status', true);
				$criteriaUpdateDate = $this->input->post('criteria_update_date', true);
				$landlordResponsibilities =  $this->input->post('landlord_responsibilities', true);
				$propertyType = $this->input->post('property_type[]', true);

				$states = $this->input->post('states[]', true);
				$searchCriteria = array(
					'comment' => $this->input->post('comment', true),
					'lease_term_remaining' => $this->input->post('lease_term_remaining', true),
					'contact_id' => $this->input->post('contact_id', true),
					'landlord_responsibilities' => (is_array($landlordResponsibilities) && (sizeof($landlordResponsibilities) > 0)) ? implode(", ", $landlordResponsibilities) : "",
					'tenant_name' => (is_array($tenants) && (sizeof($tenants) > 0)) ? implode(", ", $tenants) : "",
					'availability_status' => (is_array($availabilityStatus) && (sizeof($availabilityStatus) > 0)) ? implode(", ", $availabilityStatus) : "",
					'buyer_status' => (is_array($buyerStatus) && (sizeof($buyerStatus) > 0)) ? implode(", ", $buyerStatus) : "",
					'criteria_update_date' => ($criteriaUpdateDate != null) ? date("Y-m-d", strtotime($criteriaUpdateDate)) : null,
					'min_asking_price' => $this->input->post('min_asking_price', true),
					'max_asking_price' => $this->input->post('max_asking_price', true),
					'min_asking_rate' => $this->input->post('min_asking_rate', true),
					'max_asking_rate' => $this->input->post('max_asking_rate', true),
					'property_type' => (is_array($propertyType) && (sizeof($propertyType) > 0)) ? implode(", ", $propertyType) : "",
				);

				if (is_array($states) && (sizeof($states) > 0)) {
					$searchCriteria['states'] = implode(", ", $states);
				}

				$this->Acquisitioncriteria_model->add_criteria($searchCriteria);
				$this->mark_active_buyer($contact_id);
				$this->session->set_flashdata("success", "contact search criteria created");
				redirect(($pageType == 'contact') ? $contactRedirect : $companyRedirect);
			}
		}

		$data['pageTitle'] = "Add New Acquisition Criteria";
		$data['contactID'] = $contact_id;
		$data['pageType'] = $pageType;
		$data['body'] = $this->load->view('contact/addcriteria', $data, true);
		$this->load->view('defaulttemplate', $data);
	}

	function delete_criteria($criteria_id)
	{

		$this->load->library('form_validation');
		$pageType = $this->input->get('page-type', true);

		$criteria = $this->Acquisitioncriteria_model->get_criteria($criteria_id);
		$data['pageType'] = $pageType;

		if ($criteria != null) {
			$contactRedirect = base_url('contact/acquisition_criteria/' . $criteria->contact_id . "?tab=search-criteria&page-type=contact");
			$companyRedirect = base_url('contact/acquisition_criteria/' . $criteria->contact_id . "?tab=search-criteria&page-type=company");
			$this->Acquisitioncriteria_model->delete($criteria_id);

			$this->mark_active_buyer($criteria->contact_id);
			$this->session->set_flashdata("success", "contact search criteria deleted");
			redirect(($pageType == 'contact') ? $contactRedirect : $companyRedirect);
		} else {
			redirect(base_url());
		}
	}

	function view_criteria($criteria_id)
	{
		$criteria = $this->Acquisitioncriteria_model->get_criteria($criteria_id);
		if ($criteria != null) {
			$data['criteria'] = $criteria;
		} else {
			redirect(base_url());
		}

		$data['criteria'] = $criteria;
		$data['pageTitle'] = "Search Criteria";
		$data['body'] = $this->load->view('contact/viewcriteria', $data, true);
		$this->load->view('defaulttemplate', $data);
	}

	private function mark_active_buyer($contact_id)
	{
		$hasCriteria = $this->Acquisitioncriteria_model->has_criteria($contact_id);
		if ($hasCriteria) {
			$this->Contact_model->update_contact($contact_id, ['active_buyer' => 1]);
		} else {
			$this->Contact_model->update_contact($contact_id, ['active_buyer' => 0]);
		}
	}

	function view($contactId)
	{

		$navigation = $this->input->get('type', true);
		$nav = $this->input->get('nav', true);
		$params = $this->session->userdata('contact_filter');
		$params = ($params != null) ? $params : array();

		$nextContact = $this->Contact_model->navigateContact($params, $contactId, RecordNavigation::NEXT);
		$previousContact = $this->Contact_model->navigateContact($params, $contactId, RecordNavigation::PREVIOUS);

		$data['hasNext'] = (is_array($nextContact) && sizeof($nextContact) > 0) && (($nav == null));
		$data['hasPrevious'] = (is_array($previousContact) && sizeof($previousContact) > 0) && (($nav == null));
		if ($navigation == RecordNavigation::NEXT) {
			$contact = $this->Contact_model->navigateContact($params, $contactId, RecordNavigation::NEXT);
			redirect(base_url("contact/view/" . $contact['contact_id']));
		} else if ($navigation == 'previous') {
			$contact = $this->Contact_model->navigateContact($params, $contactId, RecordNavigation::PREVIOUS);
			redirect(base_url("contact/view/" . $contact['contact_id']));
		} else {
			$contact = $this->Contact_model->get_contact($contactId);
		}

		$data['contact'] = $contact;
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

	function remove($contact_id)
	{
		$contact = $this->Contact_model->get_contact($contact_id);
		if (isset($contact['contact_id'])) {
			$this->Contact_model->delete_contact($contact_id);
			$this->session->set_flashdata("success", "contact deleted");
			redirect(base_url('contact/'));
		} else {
			redirect(base_url());
		}
	}

	public function unique_name($name)
	{
		$name = array(
			'first_name' => $this->input->post('first_name', true),
			'middle_name' => $this->input->post('middle_name', true),
			'last_name' => $this->input->post('last_name', true),
		);
		$option = $this->Contact_model->get_contact_by_name($name);
		$edited_id = $this->session->userdata('edited_contact_id');
		if (($option != null) && (($option['contact_id'] != $edited_id))) {
			$this->form_validation->set_message('unique_name', 'The {field} already registered');
			return false;
		}

		return true;
	}

	public function unique_email($email)
	{
		$option = $this->Contact_model->get_contact_by_email($email);
		$editedEmail = $this->session->userdata('edited_email');
		if (($option !== null) && (($option['email'] !== $editedEmail) && (strlen($email) > 0))) {
			$this->form_validation->set_message('unique_email', 'The {field} already registered');
			return false;
		}
		return true;
	}

	public function valid_phone($phone)
	{
		$formattedPhone = formatPhoneNumber($phone);
		if (!(strlen($formattedPhone) == 14)) {
			$this->form_validation->set_message('valid_phone', 'The {field} is invalid');
			return false;
		}
		return true;
	}

	private function getContactData()
	{
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

		$taxRecordSentDate = $this->input->post('tax_record_sent_date', true);
		$lastDialDate = $this->input->post('last_dial', true);
		$activeBuyer = $this->input->post('active_buyer', true);
		$doNotSend = $this->input->post('do_not_send', true);
		$doNotBlast = $this->input->post('do_not_blast', true);
		$lastUpdate = $this->input->post('last_update', true);
		$badNo = $this->input->post('bad_no', true);
		$comment = $this->input->post('comment', true);

		$states = $this->input->post('states', true);
		$acquisitionCriteriaDate = $this->input->post('acquisition_criteria_date', true);
		$tenantName = $this->input->post('tenant_name', true);

		$formData['phones'] = $phones;
		$formData['addresses'] = $addreses;
		$formData['first_name'] = $this->input->post('first_name', true);
		$formData['last_name'] = $this->input->post('last_name', true);
		$formData['company_name'] = $this->input->post('company_name', true);
		$formData['email'] = $this->input->post('email', true);
		$formData['lead_gen_type'] = $this->input->post('lead_gen_type', true);
		$formData['tax_record_sent_date'] = ($taxRecordSentDate != null) ? date('Y-m-d', strtotime($taxRecordSentDate)) : null;
		$formData['last_dial'] = ($lastDialDate != null) ? date('Y-m-d', strtotime($lastDialDate)) : null;
		$formData['last_update'] = ($lastUpdate != null) ? date('Y-m-d', strtotime($lastUpdate)) : null;;
		$formData['do_not_send'] = ($doNotSend != null) ? true : false;
		// $formData['active_buyer'] = ($activeBuyer != null) ? true : false;
		$formData['do_not_blast'] = ($doNotBlast != null) ? true : false;
		$formData['bad_no'] = ($badNo != null) ? true : false;

		return $formData;
	}

	public function all()
	{
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

	private function bulkUploadToDatabase($uploadData)
	{
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
					'name' => array(
						'first_name' => $firstName,
						'last_name' => $lastName,
					),
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
