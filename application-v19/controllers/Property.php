<?php

require_once APPPATH . "third_party/excelwriter/xlsxwriter.class.php";
require_once APPPATH . "third_party/phpspreadsheet/vendor/autoload.php";

//application/third_party/PHP_XLSXWriter-master

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Property extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('Property_model');
		$this->load->model('Search_model');
		if (!(($this->authenticate->isSuperAdmin()) || ($this->authenticate->isAdmin()))) {
			redirect(base_url());
		}
	}

	function index()
	{

		$searchCriteria = $this->session->userdata('searchCriteria');
		$params = ($searchCriteria != null) ? (array)$searchCriteria : [];
		$data['totalProperties'] = $this->Property_model->get_properties_count();
		$data['showAdvanceSearch'] = ($this->session->userdata('advance_search') == 1);
		$data['pageTitle'] = "Properties";
		$data['searchCriteria'] = json_encode($searchCriteria);

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

		$range = $this->input->get('tax_record_sent_date', true);
		$lastUpdateRange = $this->input->get('last_update', true);
		$lastDialRange = $this->input->get('last_dial', true);
		$range = explode('-', $range);
		$lastUpdateRange = explode('-', $lastUpdateRange);
		$lastDialRange = explode('-', $lastDialRange);
		$data['startDate'] = (isset($range[1])) ? $range[0] : null;
		$data['lastDialStartDate'] = (isset($lastDialRange[1])) ? $lastDialRange[0] : null;
		$data['lastUpdateStartDate'] = (isset($lastUpdateRange[1])) ? $lastUpdateRange[0] : null;
		$data['endDate'] = (isset($range[1])) ? $range[1] : null;
		$data['lastUpdateEndDate'] = (isset($lastUpdateRange[1])) ? $lastUpdateRange[1] : null;
		$data['lastDialEndDate'] = (isset($lastDialRange[1])) ? $lastDialRange[1] : null;

		$data['body'] = $this->load->view("property/index", $data, true);
		$this->load->view('defaulttemplate', $data);
	}

	function ajaxSearch($loadProperties = true)
	{
		$results = array();
		$resultCount = 0;
		$searchCriteria = $this->session->userdata('searchCriteria');
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$offset = 0;
			$name = $this->input->post('name', true);
			$propertyCount = $this->input->post('property_count', true);
			$propertyStateOwner = $this->input->post('property_state', true);
			$company = $this->input->post('company', true);
			$firstName = $this->input->post('first_name', true);
			$lastName = $this->input->post('last_name', true);
			$availabilityStatus = $this->input->post('availability_status[]', true);
			$availabilityUpdate = $this->input->post('availability_update', true);
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
				'tax_record_sent_date' => $this->input->post('tax_record_sent_date', true),
				'last_update' => $this->input->post('last_update', true),
				'do_not_send' => $this->input->post('do_not_send', true),
				'do_not_blast' => $this->input->post('do_not_blast', true),
				'bad_no' => $this->input->post('bad_no', true),
				'last_dial' => $this->input->post('last_dial', true),
				'has_owner' => $this->input->post('has_owner', true),
				'owner_phone' => $this->input->post('owner_phone', true),
				'owner_address' => $this->input->post('owner_address', true),
				'start' => $this->input->post('start', true),
				'company' => $company,
				'first_name' => $firstName,
				'last_name' => $lastName,
				'availability_status' => (is_array($availabilityStatus) && (sizeof($availabilityStatus) > 0)) ? implode(",", $availabilityStatus) : "",
				'availability_update' => $availabilityUpdate,
				'asking_price' => $this->input->post('asking_price', true),
				'asking_rate' => $this->input->post('asking_rate', true),
			);

			$validatedCriteria = $this->validateCriteria($searchCriteria);
			$this->session->set_userdata('searchCriteria', (object)$validatedCriteria);

			if ($loadProperties == true) {
				$results = $this->Search_model->search($validatedCriteria, 0);
				$resultCount = $this->Search_model->count($validatedCriteria);
			} else {
				$results = $this->Search_model->searchOwners($validatedCriteria, 0);
				$resultCount = $this->Search_model->countOwners($validatedCriteria);
			}
		} else if (($searchCriteria != null)) {
			if ($loadProperties == true) {
				$results = $this->Search_model->search((array)$searchCriteria, 0);
				$resultCount = $this->Search_model->count((array)$searchCriteria);
			} else {
				$results = $this->Search_model->searchOwners((array)$searchCriteria, 0);
				$resultCount = $this->Search_model->countOwners((array)$searchCriteria);
			}
		}

		$properties = $this->getFormattedProperties($results);
		$owners = $this->getFormattedOwners($results);

		//sort property id in ascending order
		usort($properties, function ($a, $b) {
			return $a['property_id'] > $b['property_id'];
		});

		//correct serial numbering
		$searchCriteria = $this->session->userdata('searchCriteria');
		$counter = (int)$searchCriteria->start + 1;
		foreach ($properties as &$property) {
			$property['sn'] = $counter;
			$counter++;
		}

		if ($loadProperties == true) {
			echo json_encode(array('recordsTotal' => (int)$resultCount, 'recordsFiltered' => (int)$resultCount, 'draw' => 1, 'data' => $properties));
		} else {
			echo json_encode(array('recordsTotal' => (int)$resultCount, 'recordsFiltered' => (int)$resultCount, 'draw' => 1, 'data' => $owners));
		}

		return;
	}

	public function export_properties()
	{
		$exportArray = $this->session->userdata('exports');
		@$exportArray[] = array('ready' => false, 'name' => '', 'type' => 'Properties');
		$currentIndex = sizeof($exportArray);

		$searchCriteria = $this->session->userdata('searchCriteria');
		$writer = new XLSXWriter();
		if ($searchCriteria != null) {
			$header = array(
				"Tenant Name" => "string",
				"Property Street Address" => "string",
				"Property City" => "string",
				"Property State" => "string",
				"Property Zip Code" => "string",
				"Store #" => "string",
				"Owner" => "string",
				"Owner Type" => "string",
				"Owner Phone #" => "string",
				"Owner Email" => "string",
				"Owner Street Address" => "string",
				"Owner City" => "string",
				"Owner State" => "string",
				"Owner Zip Code" => "string",
				"Property Type" => "string",
				"Availability Status" => "string",
				"Availability Status Update Date" => "date",
				"Lease Type" => "string",
				"Annual Rent/NOI" => "dollar",
				"Asking Cap Rate" => "string",
				"Asking Price" => "dollar",
				"Asking Cap Rate" => "string",
				"Lease Commencement Date" => "date",
				"Lease Expiration Date" => "date",
				"Building Size" => "string",
				"Land Size" => "string",
				"Property Link" => "string",
				"Comment" => "string"
			);

			$writer->setTitle("Properties");
			$writer->writeSheetHeader('properties', $header);

			$offset = 0;
			$results = $this->Search_model->search((array)$searchCriteria, $offset);

			$rowCount = 1;
			while (sizeof($results) > 0) {
				foreach ($results as $result) {
					foreach ($result['properties'] as $prop) {
						$phones = '';
						if ((is_array(@$result['detail']['phones']))) {
							$phones = [];
							foreach ($result['detail']['phones'] as $phone) {
								$phones[] = $phone->phone;
							}

							$phones = (is_array($phones)) ? implode(', ', $phones) : '';
						}

						$row = array(
							$prop['name'],
							$prop['address'],
							$prop['city'],
							$prop['state'],
							$prop['zip_code'],
							$prop['store_number'],
							$result['detail']['name'],
							$result['type'],
							$phones,
							$prop['email'],
							@$result['detail']['addresses'][0]->address,
							@$result['detail']['addresses'][0]->city,
							@$result['detail']['addresses'][0]->state,
							@$result['detail']['addresses'][0]->zip_code,
							$prop['property_type'],
							$prop['availability_status'],
							$prop['availability_update_date'],
							$prop['lease_type'],
							$prop['annual_rent'],
							$prop['asking_cap_rate'] . "%",
							$prop['asking_price'],
							$prop['lease_commencement_date'],
							$prop['lease_expiration_date'],
							$prop['building_size'],
							$prop['land_size'],
							$prop['property_link'],
							$prop['comments']
						);

						$writer->writeSheetRow('properties', $row);
						$rowCount++;
					}
				}
				$offset += 100;
				$results = $this->Search_model->search((array)$searchCriteria, $offset);
			}

			$filename = "properties.xlsx";
			header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
			header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
			header('Content-Transfer-Encoding: binary');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			$writer->writeToStdOut();

			@$exportArray[$currentIndex - 1] = array('ready' => true, 'name' => 'properties.xlsx', 'type' => 'Properties');
		} else {
			redirect(base_url());
		}
	}

	public function export_owners()
	{
		$exportArray = $this->session->userdata('exports');
		@$exportArray[] = array('ready' => false, 'name' => '', 'type' => 'Properties');
		$currentIndex = sizeof($exportArray);

		$searchCriteria = $this->session->userdata('searchCriteria');
		$writer = new XLSXWriter();
		if ($searchCriteria != null) {
			$header = array(
				"Owner" => "string",
				"Company" => "string",

				"Contact(s)" => "string",
				"Company Phone(s)" => "string",

				"Company Street Address" => "string",
				"Company City" => "string",
				"Company State" => "string",
				"Company Zip Code" => "string",

				"Contact Phone(s)" => "string",
				"Contact Street Address" => "string",
				"Contact City" => "string",
				"Contact State" => "string",
				"Contact Zip Code" => "string",
				"Contact Email" => "string",
			);
			$writer->writeSheetHeader('owners', $header);

			$offset = 0;
			$results = $this->Search_model->searchOwners((array)$searchCriteria, $offset);

			$rowCount = 1;
			while (sizeof($results) > 0) {
				foreach ($results as $result) {

					$phones = '';
					if ((is_array(@$result['detail']['phones']))) {
						$phones = array();
						foreach ($result['detail']['phones'] as $phone) {
							$phones[] = $phone->phone;
						}

						$phones = (is_array($phones)) ? implode(', ', $phones) : '';
					}

					$companyContacts = array();
					if (@$result['type'] == EntityTypes::COMPANY) {
						$filterLeadGenType = ((@$searchCriteria->lead_gen_type != null) && (($searchCriteria->lead_gen_type == LeadGenOptions::MET_OR_HAVENT_MET)));
						$companyContacts = $this->Contact_model->get_contacts_by_company_id((int)@$result['detail']['company_id'], $filterLeadGenType);
					}

					//contact association with a company
					if (($result['type'] == EntityTypes::CONTACT) && (strlen(@$result['detail']['company_name']) > 0)) {
						$row = array(
							$result['detail']['name'],
							@$result['detail']['company_name'],
							'',
							'',
							'',
							'',
							'',
							'',
							$phones,
							@$result['detail']['addresses'][0]->address,
							@$result['detail']['addresses'][0]->city,
							@$result['detail']['addresses'][0]->state,
							@$result['detail']['addresses'][0]->zip_code,
							@$result['detail']['email'],
						);
						$writer->writeSheetRow('owners', $row);
					} else if (($result['type'] == EntityTypes::CONTACT) && (strlen(@$result['detail']['company_name']) == 0)) {
						$row = array(
							$result['detail']['name'],
							'',
							'',
							'',
							'',
							'',
							'',
							'',
							$phones,
							@$result['detail']['addresses'][0]->address,
							@$result['detail']['addresses'][0]->city,
							@$result['detail']['addresses'][0]->state,
							@$result['detail']['addresses'][0]->zip_code,
							@$result['detail']['email'],

						);
						$writer->writeSheetRow('owners', $row);
					} else if (($result['type'] == EntityTypes::COMPANY) && (sizeof($companyContacts) == 0)) {
						$row = array(
							$result['detail']['name'],
							'',
							'',
							$phones,
							@$result['detail']['addresses'][0]->address,
							@$result['detail']['addresses'][0]->city,
							@$result['detail']['addresses'][0]->state,
							@$result['detail']['addresses'][0]->zip_code,
							'',
							'',
							'',
							'',
							'',
							'',
							'',
						);
						$writer->writeSheetRow('owners', $row);
					} else if (($result['type'] == EntityTypes::COMPANY) && (sizeof($companyContacts) > 0)) {
						$row = array(
							$result['detail']['name'],
							'',
							'',
							$phones,
							@$result['detail']['addresses'][0]->address,
							@$result['detail']['addresses'][0]->city,
							@$result['detail']['addresses'][0]->state,
							@$result['detail']['addresses'][0]->zip_code,
							'',
							'',
							'',
							'',
							'',
							'',
							'',
						);
						$writer->writeSheetRow('owners', $row);

						$ownerName = @$result['detail']['name'];
						$ownerCompany = @$result['detail']['company_name'];

						foreach ($companyContacts as $con) {
							$companyPhones = @$result['detail']['phones'];
							$companyPhones = array_column($companyPhones, 'phone');
							$companyPhones = implode(', ', $companyPhones);

							$contactPhones = array_column($con['phones'], 'phone');
							$contactPhones = implode(', ', $contactPhones);

							$row = array(
								$ownerName,
								$ownerCompany,
								$con['contact_name'],

								$companyPhones,
								@$result['detail']['addresses'][0]->address,
								@$result['detail']['addresses'][0]->city,
								@$result['detail']['addresses'][0]->state,
								@$result['detail']['addresses'][0]->zip_code,

								$contactPhones,
								@$con['addresses'][0]->address,
								@$con['addresses'][0]->city,
								@$con['addresses'][0]->state,
								@$con['addresses'][0]->zip_code,
								@$con['email'],
							);
							$writer->writeSheetRow('owners', $row);
							$ownerCompany = '';
							$ownerName = '';
						}
					}
					$rowCount++;
				}

				$offset += 100;
				$results = $this->Search_model->searchOwners((array)$searchCriteria, $offset);
			}

			$filename = "owners.xlsx";
			header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
			header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
			header('Content-Transfer-Encoding: binary');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			$writer->writeToStdOut();

			@$exportArray[$currentIndex - 1] = array('ready' => true, 'name' => 'properties.xlsx', 'type' => 'Owners');
		} else {
			redirect(base_url());
		}
	}

	public function export_company_owners()
	{
		$exportArray = $this->session->userdata('exports');
		@$exportArray[] = array('ready' => false, 'name' => '', 'type' => 'Properties');
		$currentIndex = sizeof($exportArray);

		$searchCriteria = $this->session->userdata('searchCriteria');
		$writer = new XLSXWriter();
		if ($searchCriteria != null) {
			$header = array(
				"Owner" => "string",
				"Company" => "string",
				"Contact(s)" => "string",
				"Company Phone(s)" => "string",
				"Company Street Address" => "string",
				"Company City" => "string",
				"Company State" => "string",
				"Company Zip Code" => "string",
				"Contact Phone(s)" => "string",
				"Contact Street Address" => "string",
				"Contact City" => "string",
				"Contact State" => "string",
				"Contact Zip Code" => "string",
				"Contact Email" => "string",
			);
			$writer->writeSheetHeader('owners', $header);

			$offset = 0;
			$results = $this->Search_model->searchOwners((array)$searchCriteria, $offset);

			$rowCount = 1;
			while (sizeof($results) > 0) {
				foreach ($results as $result) {

					$phones = '';
					if ((is_array(@$result['detail']['phones']))) {
						$phones = array();
						foreach ($result['detail']['phones'] as $phone) {
							$phones[] = $phone->phone;
						}

						$phones = (is_array($phones)) ? implode(', ', $phones) : '';
					}

					$companyContacts = array();
					if (@$result['type'] == EntityTypes::COMPANY) {
						$filterLeadGenType = ((@$searchCriteria->lead_gen_type != null) && (($searchCriteria->lead_gen_type == LeadGenOptions::MET_OR_HAVENT_MET)));
						$companyContacts = $this->Contact_model->get_contacts_by_company_id((int)@$result['detail']['company_id'], $filterLeadGenType);
					}

					if (($result['type'] == EntityTypes::COMPANY) && (sizeof($companyContacts) == 0)) {
						$row = array(
							$result['detail']['name'],
							'',
							'',
							$phones,
							@$result['detail']['addresses'][0]->address,
							@$result['detail']['addresses'][0]->city,
							@$result['detail']['addresses'][0]->state,
							@$result['detail']['addresses'][0]->zip_code,
							'',
							'',
							'',
							'',
							'',
							'',
							'',
						);
						$writer->writeSheetRow('owners', $row);
					}

					$rowCount++;
				}

				$offset += 100;
				$results = $this->Search_model->searchOwners((array)$searchCriteria, $offset);
			}

			$filename = "company_owners.xlsx";
			header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
			header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
			header('Content-Transfer-Encoding: binary');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			$writer->writeToStdOut();

			@$exportArray[$currentIndex - 1] = array('ready' => true, 'name' => 'properties.xlsx', 'type' => 'Owners');
		} else {
			redirect(base_url());
		}
	}


	public function export_contact_owners()
	{
		$exportArray = $this->session->userdata('exports');
		@$exportArray[] = array('ready' => false, 'name' => '', 'type' => 'Properties');
		$currentIndex = sizeof($exportArray);

		$searchCriteria = $this->session->userdata('searchCriteria');
		$writer = new XLSXWriter();
		if ($searchCriteria != null) {
			$header = array(
				"Owner" => "string",
				"Company" => "string",
				"Contact(s)" => "string",
				"Company Phone(s)" => "string",
				"Company Street Address" => "string",
				"Company City" => "string",
				"Company State" => "string",
				"Company Zip Code" => "string",
				"Contact Phone(s)" => "string",
				"Contact Street Address" => "string",
				"Contact City" => "string",
				"Contact State" => "string",
				"Contact Zip Code" => "string",
				"Contact Email" => "string",
			);
			$writer->writeSheetHeader('owners', $header);

			$offset = 0;
			$results = $this->Search_model->searchOwners((array)$searchCriteria, $offset);

			$rowCount = 1;
			while (sizeof($results) > 0) {
				foreach ($results as $result) {

					$phones = '';
					if ((is_array(@$result['detail']['phones']))) {
						$phones = array();
						foreach ($result['detail']['phones'] as $phone) {
							$phones[] = $phone->phone;
						}

						$phones = (is_array($phones)) ? implode(', ', $phones) : '';
					}

					$companyContacts = array();
					if (@$result['type'] == EntityTypes::COMPANY) {
						$filterLeadGenType = ((@$searchCriteria->lead_gen_type != null) && (($searchCriteria->lead_gen_type == LeadGenOptions::MET_OR_HAVENT_MET)));
						$companyContacts = $this->Contact_model->get_contacts_by_company_id((int)@$result['detail']['company_id'], $filterLeadGenType);
						foreach ($companyContacts as $cContact) {
							//concatenate contacts phones
							$contactPhones = '';
							if ((is_array(@$cContact['phones']))) {
								$contactPhones = array();
								foreach ($cContact['phones'] as $p) {
									$contactPhones[] = $p->phone;
								}

								$contactPhones = (is_array($contactPhones)) ? implode(', ', $contactPhones) : '';
							}

							//associated contact record
							$row = array(
								$cContact['contact_name'],
								@$cContact['company_name'],
								'',
								'',
								'',
								'',
								'',
								'',
								$contactPhones,
								@$cContact['addresses'][0]->address,
								@$cContact['addresses'][0]->city,
								@$cContact['addresses'][0]->state,
								@$cContact['addresses'][0]->zip_code,
								@$cContact['email'],
							);
							$writer->writeSheetRow('owners', $row);
						}
					}

					//contact association with a company
					if (($result['type'] == EntityTypes::CONTACT) && (strlen(@$result['detail']['company_name']) > 0)) {
						$row = array(
							$result['detail']['name'],
							@$result['detail']['company_name'],
							'',
							'',
							'',
							'',
							'',
							'',
							$phones,
							@$result['detail']['addresses'][0]->address,
							@$result['detail']['addresses'][0]->city,
							@$result['detail']['addresses'][0]->state,
							@$result['detail']['addresses'][0]->zip_code,
							@$result['detail']['email'],
						);
						$writer->writeSheetRow('owners', $row);
					} else if (($result['type'] == EntityTypes::CONTACT) && (strlen(@$result['detail']['company_name']) == 0)) {
						$row = array(
							$result['detail']['name'],
							'',
							'',
							'',
							'',
							'',
							'',
							'',
							$phones,
							@$result['detail']['addresses'][0]->address,
							@$result['detail']['addresses'][0]->city,
							@$result['detail']['addresses'][0]->state,
							@$result['detail']['addresses'][0]->zip_code,
							@$result['detail']['email'],

						);
						$writer->writeSheetRow('owners', $row);
					}

					$rowCount++;
				}

				$offset += 100;
				$results = $this->Search_model->searchOwners((array)$searchCriteria, $offset);
			}

			$filename = "contacts_owners.xlsx";
			header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
			header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
			header('Content-Transfer-Encoding: binary');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			$writer->writeToStdOut();

			@$exportArray[$currentIndex - 1] = array('ready' => true, 'name' => 'properties.xlsx', 'type' => 'Owners');
		} else {
			redirect(base_url());
		}
	}

	public function export($type = null)
	{
		$searchCriteria = $this->session->userdata('searchCriteria');
		$results = $this->Search_model->search((array)$searchCriteria);
		$properties = $this->getFormattedProperties($results);
	}

	private function getFormattedProperties($results)
	{

		$searchCriteria = $this->session->userdata('searchCriteria');
		$properties = [];
		$counter = (int)$searchCriteria->start + 1;
		foreach ($results as $data) {
			foreach ($data['properties'] as $prop) {
				$owner = '';
				if (@$data['type'] == EntityTypes::COMPANY) {
					$owner = anchor(base_url('company/view/' . $data['detail']['company_id']), $data['detail']['name'], 'class="link-class" target="_blank"');
				} else if (@$data['type'] == EntityTypes::CONTACT) {
					$owner = anchor(base_url('contact/view/' . $data['detail']['contact_id']), $data['detail']['name'], 'class="link-class" target="_blank"');
				}

				if ((is_array(@$data['detail']['phones']))) {
					$phones = [];
					foreach ($data['detail']['phones'] as $phone) {
						$phones[] = $phone->phone;
					}

					$phones = (is_array($phones)) ? implode(', ', $phones) : '';
				}

				$editLink = base_url('property/edit/' . $prop['property_id']);
				$editButton = "<a href='$editLink' class='btn btn-outline btn-primary btn-xs p--3 pr--5'>
                                    <i class='fi fi-pencil pr-0 mr-0 ml-1'></i> Edit</a>";

				$deleteLink = base_url('property/remove/' . $prop['property_id']);
				$deleteButton = "<a href='$deleteLink' name='property' class='btn btn-outline-danger delete-button-confirm btn-xs p--3 pr--5'>
                                    <i class='fi fi-close pr-0 mr-0 ml-1'></i> Delete</a>";
				$propertyRow = array(
					'property_id' => @$prop['property_id'],
					'sn' => $counter,
					'name' => anchor(base_url('property/view/' . $prop['property_id']), $prop['name'], 'class="link-class" target="_blank"'),
					'address' => $prop['address'],
					'city' => $prop['city'],
					'state' => $prop['state'],
					'annual_rent' => moneyFormat(floatval($prop['annual_rent']), "USD"),
					'asking_cap_rate' => number_format(floatval($prop['asking_cap_rate']), 2) . "%",
					'asking_price' => "$" . number_format(floatval($prop['asking_price']), 0),
					'availability_status' => $prop['availability_status'],
					'availability_status_update_date' => formatDate($prop['availability_update_date']),
					'zip_code' => $prop['zip_code'],
					'store_number' => $prop['store_number'],
					'owner' => @$owner,
					'type' => @$data['type'],
					'phones' => @$phones,
					'email' => $prop['email'],
					'owner_address' => @$data['detail']['addresses'][0]->address,
					'owner_city' => @$data['detail']['addresses'][0]->city,
					'owner_state' => @$data['detail']['addresses'][0]->state,
					'owner_zip_code' => @$data['detail']['addresses'][0]->zip_code,
					'property_type' => $prop['property_type'],
					'tax_record_sent' => ($prop['tax_record_sent_date'] != null) ? formatDate($prop['tax_record_sent_date']) : '',
					'last_update' => ($prop['last_update'] != null) ? formatDate($prop['last_update']) : '',
					'last_dial' => (@$data['detail']['last_dial']) ? formatDate(@$data['detail']['last_dial']) : '',
					'tax_record_sent' => (@$data['detail']['tax_record_sent']) ? formatDate(@$data['detail']['tax_record_sent']) : '',
					'lead_gen_type' => (@$data['detail']['lead_gen_type']),
					'do_not_blast' => (@$data['detail']['do_not_blast']) ? "Yes" : "No",
					'do_not_send' => (@$data['detail']['do_not_send']) ? "Yes" : "No",
					'bad_no' => (@$data['detail']['bad_no']) ? "Yes" : "No",
					'lease_term_remaining' => '',
					'action' => $editButton . " " . $deleteButton
				);

				// Return the number of days between the two dates:   
				$leaseExpirationDate = $prop['lease_expiration_date'];
				if (strlen($leaseExpirationDate) > 0) {
					$leaseExpirationDate = date('Y-m-d', strtotime($leaseExpirationDate));
					$days = round(abs(strtotime($leaseExpirationDate) - now()) / 86400);
					$leaseTermRemaining = round($days / 360, 2);
					$propertyRow['lease_term_remaining'] = $leaseTermRemaining;
				}


				$properties[] = $propertyRow;
				$counter++;
			}
		}

		return $properties;
	}

	private function getFormattedOwners($results)
	{
		$propertyOwners = [];
		foreach ($results as $prop) {
			$contacts = '';

			$owner = '';
			if (@$prop['type'] == EntityTypes::COMPANY) {
				$owner = anchor(base_url('property/view_owner/' . $prop['property_id']), $prop['detail']['name'], 'class="link-class" target="_blank"');
			} else if (@$prop['type'] == EntityTypes::CONTACT) {
				$owner = anchor(base_url('property/view_owner/' . $prop['property_id']), $prop['detail']['name'], 'class="link-class" target="_blank"');
			}

			if ((is_array(@$prop['detail']['phones']))) {
				$phones = [];
				foreach ($prop['detail']['phones'] as $phone) {
					$phones[] = $phone->phone;
				}

				$phones = (is_array($phones)) ? implode(', ', $phones) : '';
			}

			$affilicatedCompany = '';
			if (@$prop['type'] == EntityTypes::CONTACT && ((int)@$prop['detail']['company_id'] > 0)) {
				$affilicatedCompany = anchor(base_url('company/view/' . $prop['detail']['company_id']), $prop['detail']['company_name'], 'class="link-class" target="_blank"');
			} else if (@$prop['type'] == EntityTypes::COMPANY) {
				$searchCriteria = $this->session->userdata('searchCriteria');
				$filterLeadGenType = ((@$searchCriteria->lead_gen_type != null) && (($searchCriteria->lead_gen_type == LeadGenOptions::MET_OR_HAVENT_MET)));
				$contacts = $this->Contact_model->get_contacts_by_company_id((int)@$prop['detail']['company_id'], $filterLeadGenType);
				$groupedContacts = array_column($contacts, 'contact_name');
				$contacts = implode(", ", $groupedContacts);
			}

			if (@$prop['type'] == "undefined owner") {
				continue;
			}

			$ownerRow = array(
				'type' => @$prop['type'],
				'owner' => @$owner,
				'company' => @$affilicatedCompany,
				'contacts' => @$contacts,
				'phones' => @$phones,
				'owner_address' => @$prop['detail']['addresses'][0]->address,
				'owner_city' => @$prop['detail']['addresses'][0]->city,
				'owner_state' => @$prop['detail']['addresses'][0]->state,
				'owner_zip_code' => @$prop['detail']['addresses'][0]->zip_code,
				'email' => @$prop['detail']['email'],
			);

			$propertyOwners[] = $ownerRow;
		}


		return $propertyOwners;
	}

	function search()
	{
		$data['showAdvanceSearch'] = ($this->session->userdata('advance_search') == 1);
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$offset = 0;
			$name = $this->input->post('name', true);
			$propertyCount = $this->input->post('property_count', true);
			$propertyStateOwner = $this->input->post('property_state', true);
			$company = $this->input->post('company', true);
			$firstName = $this->input->post('first_name', true);
			$lastName = $this->input->post('last_name', true);
			$availabilityStatus = $this->input->post('availability_status', true);
			$availabilityUpdate = $this->input->post('availability_update', true);
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
				'last_name' => $lastName,
				'availability_status' => (is_array($availabilityStatus)) ? implode(",", $availabilityStatus) : "",
				'availability_update' => $availabilityUpdate,
			);

			$validatedCriteria = $this->validateCriteria($searchCriteria);
			$this->session->set_userdata('searchCriteria', (object)$searchCriteria);
		}

		redirect(base_url("property/"));
	}

	private function validateCriteria($criteria)
	{
		foreach ($criteria as $key => $value) {
			if (($key != 'state') && ($key != 'asking_price')  && ($key != 'asking_rate') && (strlen($criteria[$key]) == 0)) {
				unset($criteria[$key]);
			} else if (($key == 'state') && (is_array($criteria[$key])) && (sizeof($criteria[$key]) == 0)) {
				unset($criteria[$key]);
			}
		}

		return $criteria;
	}

	function reset_form()
	{
		$this->session->unset_userdata('result');
		$this->session->unset_userdata('resultCount');
		$this->session->unset_userdata('searchCriteria');
		redirect(base_url("property/"));
	}

	function add()
	{
		$this->load->library('form_validation');

		if ($this->input->server('REQUEST_METHOD') == 'POST') {

			$this->form_validation->set_rules("name", "Tenant Name", 'required');
			$this->form_validation->set_rules("google_map_link", "Map Link", 'trim');
			$this->form_validation->set_rules("property_link", "Property Link", 'trim');
			$contactId = $this->input->post('contact_id', true);
			$company = $this->input->post('company', true);
			$availabilityStatus = $this->input->post('availability_status', true);
			$availabilityUpdateDate = $this->input->post('availability_update_date', true);

			if (($this->form_validation->run())) {
				$taxRecordSentDate = $this->input->post('tax_record_sent_date', true);
				$lastUpdateDate = $this->input->post('last_update', true);
				$leaseCommencementDate = $this->input->post('lease_commencement_date', true);
				$leaseExpirationDate = $this->input->post('lease_expiration_date', true);
				$propertyData = array(
					'name' => $this->input->post('name', true),
					'store_number' => $this->input->post('store_number', true),
					'address' => $this->input->post('address', true),
					'state' => $this->input->post('state', true),
					'city' => $this->input->post('city', true),
					'zip_code' => $this->input->post('zip_code', true),
					'google_map_link' => $this->input->post('google_map_link', true),
					'property_type' => $this->input->post('property_type', true),
					'tax_record_sent_date' => ($taxRecordSentDate != null) ? date('Y-m-d', strtotime($taxRecordSentDate)) : null,
					'last_update' => ($lastUpdateDate != null) ? date('Y-m-d', strtotime($lastUpdateDate)) : null,
					'availability_status' => (sizeof($availabilityStatus) > 0) ? implode(",", $availabilityStatus) : "",
					'availability_update_date' => ($availabilityUpdateDate != null) ? date('Y-m-d', strtotime($availabilityUpdateDate)) : null,
					'property_link' => $this->input->post('property_link', true),
					'lease_type' => $this->input->post('lease_type', true),
					'annual_rent' => $this->input->post('annual_rent', true),
					'asking_cap_rate' => $this->input->post('asking_cap_rate', true),
					'asking_price' => $this->input->post('asking_price', true),
					'building_size' => $this->input->post('building_size', true),
					'land_size' => $this->input->post('land_size', true),
					'comments' => $this->input->post('comments', true),
					'lease_commencement_date' => ($leaseCommencementDate != null) ? date('Y-m-d', strtotime($leaseCommencementDate)) : null,
					'lease_expiration_date' => ($leaseExpirationDate != null) ? date('Y-m-d', strtotime($leaseExpirationDate)) : null
				);

				if ($contactId != null) {
					$propertyData['contact_id'] = $contactId;
				} else {
					$propertyData['company'] = $company;
				}

				$propertyId = $this->Property_model->save_new_property($propertyData);
				$this->session->set_flashdata("success", "property added successfully");
				redirect(base_url("property/view/$propertyId"));
			}
		}

		$data['pageTitle'] = "New Property";
		$data['body'] = $this->load->view('property/add', $data, true);
		$this->load->view('defaulttemplate', $data);
	}

	function edit($property_id)
	{

		$this->load->library('form_validation');
		$data['property'] = $this->Property_model->get_property($property_id);
		$this->session->unset_userdata('edited_property_id');

		if (isset($data['property']['property_id'])) {
			if ($this->input->server('REQUEST_METHOD') == 'POST') {
				$this->form_validation->set_rules("name", "Tenant Name", 'required');
				$this->session->set_userdata('edited_property_id', $property_id);

				$contactId = $this->input->post('contact_id', true);
				$company = $this->input->post('company', true);
				$propertyType = $this->input->post('property_type', true);
				$leaseCommencementDate = $this->input->post('lease_commencement_date', true);
				$leaseExpirationDate = $this->input->post('lease_expiration_date', true);

				if (($this->form_validation->run())) {
					$taxRecordSentDate = $this->input->post('tax_record_sent_date', true);
					$lastUpdateDate = $this->input->post('last_update', true);
					$availabilityStatus = $this->input->post('availability_status', true);
					$availabilityUpdateDate = $this->input->post('availability_update_date', true);

					$propertyData = array(
						'name' => $this->input->post('name', true),
						'store_number' => $this->input->post('store_number', true),
						'address' => $this->input->post('address', true),
						'state' => $this->input->post('state', true),
						'city' => $this->input->post('city', true),
						'zip_code' => $this->input->post('zip_code', true),
						'google_map_link' => $this->input->post('google_map_link', true),
						'tax_record_sent_date' => ($taxRecordSentDate != null) ? date('Y-m-d', strtotime($taxRecordSentDate)) : null,
						'last_update' => ($lastUpdateDate != null) ? date('Y-m-d', strtotime($lastUpdateDate)) : null,
						'availability_update_date' => ($availabilityUpdateDate != null) ? date('Y-m-d', strtotime($availabilityUpdateDate)) : null,
						'availability_status' => (sizeof($availabilityStatus) > 0) ? implode(",", $availabilityStatus) : "",
						'property_link' => $this->input->post('property_link', true),
						'lease_type' => $this->input->post('lease_type', true),
						'annual_rent' => $this->input->post('annual_rent', true),
						'asking_cap_rate' => $this->input->post('asking_cap_rate', true),
						'asking_price' => $this->input->post('asking_price', true),
						'building_size' => $this->input->post('building_size', true),
						'land_size' => $this->input->post('land_size', true),
						'comments' => htmlspecialchars($this->input->post('comments')),
						'lease_commencement_date' => ($leaseCommencementDate != null) ? date('Y-m-d', strtotime($leaseCommencementDate)) : null,
						'lease_expiration_date' => ($leaseExpirationDate != null) ? date('Y-m-d', strtotime($leaseExpirationDate)) : null
					);

					if ($propertyType != null) {
						$propertyData['property_type'] = $this->input->post('property_type', true);
					}

					if ($contactId != null) {
						$propertyData['contact_id'] = $contactId;
					}

					if ($company != null) {
						$propertyData['company'] = $company;
					}

					if ($company == "unlink") {
						$propertyData['company'] = null;
					}

					if($contactId == "unlink"){
						$propertyData['contact_id'] = null;
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

	function view($propertyId)
	{
		$navigation = $this->input->get('type', true);
		$searchCriteria = $this->session->userdata('searchCriteria');

		$nextProperty = $this->Search_model->navigateProperty((array)$searchCriteria, $propertyId, RecordNavigation::NEXT);
		$previousProperty = $this->Search_model->navigateProperty((array)$searchCriteria, $propertyId, RecordNavigation::PREVIOUS);
		$data['hasNext'] = (is_array($nextProperty) && sizeof($nextProperty) > 0);
		$data['hasPrevious'] = (is_array($previousProperty) && sizeof($previousProperty) > 0);

		if ($navigation == RecordNavigation::NEXT) {
			$property = $this->Search_model->navigateProperty((array)$searchCriteria, $propertyId, RecordNavigation::NEXT);
			redirect(base_url("property/view/" . $property['property_id']));
		} else if ($navigation == 'previous') {
			$property = $this->Search_model->navigateProperty((array)$searchCriteria, $propertyId, RecordNavigation::PREVIOUS);
			redirect(base_url("property/view/" . $property['property_id']));
		} else {
			$data['property'] = $this->Property_model->get_property($propertyId);
		}

		if (isset($data['property']['property_id'])) {

			$leaseExpirationDate = $data['property']['lease_expiration_date'];
			$data['property']['lease_term_remaining'] = '';
			if (strlen($leaseExpirationDate) > 0) {
				$leaseExpirationDate = date('Y-m-d', strtotime($leaseExpirationDate));
				$days = round(abs(strtotime($leaseExpirationDate) - now()) / 86400);
				$leaseTermRemaining = round($days / 360, 2);
				$data['property']['lease_term_remaining'] = $leaseTermRemaining;
			}


			$data['pageTitle'] = "Property";
			$data['body'] = $this->load->view('property/view', $data, true);
			$this->load->view('defaulttemplate', $data);
		} else {
			redirect(base_url());
		}
	}

	function details($propertyId)
	{
		$data['property'] = $this->Property_model->get_property($propertyId);
		if (isset($data['property']['property_id'])) {
			$data['pageTitle'] = "Property";
			$data['hideNext'] = true;
			$data['body'] = $this->load->view('property/view', $data, true);
			$this->load->view('defaulttemplate', $data);
		} else {
			redirect(base_url());
		}
	}

	public function search_contacts()
	{
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

	function remove($property_id)
	{
		$property = $this->Property_model->get_property($property_id);
		if (isset($property['property_id'])) {
			$this->Property_model->delete_property($property_id);
			$this->session->set_flashdata("success", "property deleted");
			redirect(base_url('property/'));
		} else {
			redirect(base_url());
		}
	}

	public function unique_name($name)
	{
		$option = $this->Property_model->get_property_by_name($name);
		$edited_id = $this->session->userdata('edited_property_id');
		if (($option != null) && (($option['property_id'] != $edited_id))) {
			$this->form_validation->set_message('unique_name', 'The {field} already registered');
			return false;
		}
		return true;
	}

	public function property_types()
	{
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

	private function bulkUploadToDatabase($uploadData)
	{
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

			$availabilityStatus = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
			$availabilityStatusUpdateDate = $worksheet->getCellByColumnAndRow(19, $row)->getFormattedValue();

			$leaseType = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
			$annualRent = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
			$askingCapRate = $worksheet->getCellByColumnAndRow(22, $row)->getValue();
			$askingPrice = $worksheet->getCellByColumnAndRow(23, $row)->getValue();
			$leaseCommencementDate = $worksheet->getCellByColumnAndRow(24, $row)->getFormattedValue();
			$leaseExpirationDate = $worksheet->getCellByColumnAndRow(25, $row)->getFormattedValue();
			$buildingSize = $worksheet->getCellByColumnAndRow(26, $row)->getValue();
			$landSize = $worksheet->getCellByColumnAndRow(27, $row)->getValue();
			$propertyLink = $worksheet->getCellByColumnAndRow(28, $row)->getValue();
			$comments = $worksheet->getCellByColumnAndRow(29, $row)->getValue();

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

					'availability_status' => $availabilityStatus,
					'availability_update_date' => (strlen($availabilityStatusUpdateDate) > 0) ? date('Y-m-d', strtotime($availabilityStatusUpdateDate)) : null,
					'lease_type' => $leaseType,
					'annual_rent' => $annualRent,
					'asking_cap_rate' => $askingCapRate,
					'asking_price' => $askingPrice,
					'lease_commencement_date' => (strlen($leaseCommencementDate) > 0) ? date('Y-m-d', strtotime($leaseCommencementDate)) : null,
					'lease_expiration_date' => (strlen($leaseExpirationDate) > 0) ? date('Y-m-d', strtotime($leaseExpirationDate)) : null,
					'building_size' => $buildingSize,
					'land_size' => $landSize,
					'property_link' => $propertyLink,
					'comments' => $comments
				);
			}
		}

		if (isset($optionData) && (sizeof($optionData) > 0)) {
			$this->Property_model->save_bulk_property($optionData);
		}
	}

	function view_owner($propertyId)
	{
		$this->load->model("Search_model");
		$this->load->model("Property_model");

		$searchCriteria = $this->session->userdata('searchCriteria');
		$navigation = $this->input->get('type', true);
		$property = $this->Property_model->get_property($propertyId);
		if (sizeof($property) > 0) {
			$nextRecord = $this->Search_model->navigateOwners((array)$searchCriteria, $propertyId, RecordNavigation::NEXT);
			$previousRecord = $this->Search_model->navigateOwners((array)$searchCriteria, $propertyId, RecordNavigation::PREVIOUS);

			//navigation
			if (($navigation == RecordNavigation::NEXT)) {
				redirect(base_url("property/view_owner/" . $nextRecord[0]['property_id']));
			} else if (($navigation == RecordNavigation::PREVIOUS)) {
				redirect(base_url("property/view_owner/" . $previousRecord[0]['property_id']));
			}


			if ($property['company_id'] > 0) {
				$this->showCompanyPage($property['company_id'], $nextRecord, $previousRecord, $propertyId);
			} else if (($property['contact_id'] > 0)) {
				$this->showContactPage($property['contact_id'], $nextRecord, $previousRecord, $propertyId);
			} else {
				$this->showUndefinedPage($nextRecord, $previousRecord, $propertyId);
			}
		}
	}

	private function showUndefinedPage($nextRecord, $previousRecord, $propertyId)
	{

		$this->load->model('Contact_model');
		$data['hasNext'] = (is_array($nextRecord) && sizeof($nextRecord) > 0);
		$data['hasPrevious'] = (is_array($previousRecord) && sizeof($previousRecord) > 0);

		$data['nextRecordLink'] = base_url('property/view_owner/' . $propertyId . "?type=next");
		$data['previousRecordLink'] = base_url('property/view_owner/' . $propertyId . "?type=previous");

		$data['pageTitle'] = "Undefined Owner";

		$data['body'] = $this->load->view('property/undefinedowner', $data, true);
		$this->load->view('defaulttemplate', $data);
	}

	private function showContactPage($contactId, $nextRecord, $previousRecord, $propertyId)
	{

		$this->load->model('Contact_model');
		$contact = $this->Contact_model->get_contact($contactId);
		$data['hasNext'] = (is_array($nextRecord) && sizeof($nextRecord) > 0);
		$data['hasPrevious'] = (is_array($previousRecord) && sizeof($previousRecord) > 0);
		$data['nextRecordLink'] = base_url('property/view_owner/' . $propertyId . "?type=next");
		$data['previousRecordLink'] = base_url('property/view_owner/' . $propertyId . "?type=previous");

		$data['contact'] = $contact;
		$activeTab = $this->input->get('tab');
		$data['pageTitle'] = "Contact";
		$data['activeTab'] = 'property';
		$data['isViewOwnerPage'] = true;

		if (isset($data['contact']['contact_id'])) {
			$data['activeTabBody'] = $this->load->view('contact/propertyactivetab', $data, true);
			$data['contactSideBody'] = $this->load->view('contact/contactside', $data, true);
			$data['contactNav'] = $this->load->view('contact/contactnav', $data, true);
			$data['body'] = $this->load->view('contact/view', $data, true);
			$this->load->view('defaulttemplate', $data);
		}
	}


	private function showCompanyPage($companyId, $nextRecord, $previousRecord, $propertyId)
	{
		$this->load->model('Company_model');
		$company = $this->Company_model->get_company($companyId);
		$data['hasNext'] = (is_array($nextRecord) && sizeof($nextRecord) > 0);
		$data['hasPrevious'] = (is_array($previousRecord) && sizeof($previousRecord) > 0);

		$data['nextRecordLink'] = base_url('property/view_owner/' . $propertyId . "?type=next");
		$data['previousRecordLink'] = base_url('property/view_owner/' . $propertyId . "?type=previous");

		$data['company'] = $company;
		$activeTab = $this->input->get('tab');

		if (isset($data['company']['company_id'])) {
			$data['pageTitle'] = "Company";
			$data['isViewOwnerPage'] = true;
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
		}
	}


	public function states()
	{
		$query = $this->input->get('q');
		if (strlen($query) > 0) {
			$properties = $this->Property_model->get_distinct_states($query);
			$response = array();
			$distinct = array();
			foreach ($properties as $property) {
				if(array_key_exists($property->state, $distinct) == false){
				    $response[] = array(
    					'id' => $property->state,
    					'text' => $property->state
    				);
    				$distinct[$property->state] = strtoupper($property->state);
				}
			}

			$response[] = array(
				'id' => 'Nationwide',
				'text' => 'Nationwide'
			);

			echo json_encode($response);
		} else {
			echo json_encode([]);
			exit;
		}
	}

	public function tenants()
	{
		$query = $this->input->get('q');
		if (strlen($query) > 0) {
			$properties = $this->Property_model->get_distinct_tenants($query);
			$response = array();
			foreach ($properties as $property) {
				$response[] = array(
					'id' => $property->name,
					'text' => $property->name
				);
			}
			echo json_encode($response);
		} else {
			echo json_encode([]);
			exit;
		}
	}
}
