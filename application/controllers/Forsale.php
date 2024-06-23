<?php

require_once APPPATH . "third_party/excelwriter/xlsxwriter.class.php";
require_once APPPATH . "third_party/phpspreadsheet/vendor/autoload.php";

//application/third_party/PHP_XLSXWriter-master

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Forsale extends CI_Controller
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

		$searchCriteria = $this->session->userdata('forsaleSearchCriteria');
		$params = ($searchCriteria != null) ? (array)$searchCriteria : [];
		$data['totalProperties'] = $this->Property_model->get_properties_count();
		$data['showAdvanceSearch'] = ($this->session->userdata('advance_search') == 1);
		$data['pageTitle'] = "Properties";
		$data['searchCriteria'] = json_encode($searchCriteria);

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

		$data['body'] = $this->load->view("property/forsale", $data, true);
		$this->load->view('defaulttemplate', $data);
	}

	function ajaxSearch($loadProperties = true)
	{
		$results = array();
		$resultCount = 0;
		$searchCriteria = $this->session->userdata('forsaleSearchCriteria');
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
				'for_sale' => 1,
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
				'sort_data' => $this->input->post('sort_data', true),				
			);

			$validatedCriteria = $this->validateCriteria($searchCriteria);
			$this->session->set_userdata('forsaleSearchCriteria', (object)$validatedCriteria);

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

		//correct serial numbering
		$searchCriteria = $this->session->userdata('forsaleSearchCriteria');
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

		$searchCriteria = $this->session->userdata('forsaleSearchCriteria');
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

		$searchCriteria = $this->session->userdata('forsaleSearchCriteria');
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

		$searchCriteria = $this->session->userdata('forsaleSearchCriteria');
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

		$searchCriteria = $this->session->userdata('forsaleSearchCriteria');
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
		$searchCriteria = $this->session->userdata('forsaleSearchCriteria');
		$results = $this->Search_model->search((array)$searchCriteria);
		$properties = $this->getFormattedProperties($results);
	}

	private function getFormattedProperties($results)
	{

		$searchCriteria = $this->session->userdata('forsaleSearchCriteria');
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
					'name' => anchor(base_url('forsale/view/' . $prop['property_id']), $prop['name'], 'class="link-class" target="_blank"'),
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
				$owner = anchor(base_url('forsale/view_owner/' . $prop['property_id']), $prop['detail']['name'], 'class="link-class" target="_blank"');
			} else if (@$prop['type'] == EntityTypes::CONTACT) {
				$owner = anchor(base_url('forsale/view_owner/' . $prop['property_id']), $prop['detail']['name'], 'class="link-class" target="_blank"');
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
				$searchCriteria = $this->session->userdata('forsaleSearchCriteria');
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
			$this->session->set_userdata('forsaleSearchCriteria', (object)$searchCriteria);
		}

		redirect(base_url("property/"));
	}

	private function validateCriteria($criteria)
	{
		foreach ($criteria as $key => $value) {
			if (($key != 'state') && ($key != 'asking_price') && ($key != 'sort_data')  
			&& ($key != 'asking_rate') && (strlen($criteria[$key]) == 0)) {
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
		$this->session->unset_userdata('forsaleSearchCriteria');
		redirect(base_url("forsale/"));
	}

	function view($propertyId)
	{
		$navigation = $this->input->get('type', true);
		$searchCriteria = $this->session->userdata('forsaleSearchCriteria');

		$nextProperty = $this->Search_model->navigateProperty((array)$searchCriteria, $propertyId, RecordNavigation::NEXT);
		$previousProperty = $this->Search_model->navigateProperty((array)$searchCriteria, $propertyId, RecordNavigation::PREVIOUS);
		$data['hasNext'] = null;
		$data['hasPrevious'] = null;

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

	function view_owner($propertyId)
	{
		$this->load->model("Search_model");
		$this->load->model("Property_model");

		$searchCriteria = $this->session->userdata('forsaleSearchCriteria');
		$navigation = $this->input->get('type', true);
		$property = $this->Property_model->get_property($propertyId);
		if (sizeof($property) > 0) {
			$nextRecord =  null;//$this->Search_model->navigateOwners((array)$searchCriteria, $propertyId, RecordNavigation::NEXT);
			$previousRecord = null;//$this->Search_model->navigateOwners((array)$searchCriteria, $propertyId, RecordNavigation::PREVIOUS);

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
