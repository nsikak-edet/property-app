<?php

require_once APPPATH . "third_party/excelwriter/xlsxwriter.class.php";
require_once APPPATH . "third_party/phpspreadsheet/vendor/autoload.php";

//application/third_party/PHP_XLSXWriter-master

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Activebuyer extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('Property_model');
		$this->load->model('Contact_model');
		$this->load->model('Activebuyersearch_model');
		if (!(($this->authenticate->isSuperAdmin()) || ($this->authenticate->isAdmin()))) {
			redirect(base_url());
		}
	}

	function index()
	{

		$searchCriteria = $this->session->userdata('buyerSearchCriteria');
		$params = ($searchCriteria != null) ? (array)$searchCriteria : [];
		$data['showAdvanceSearch'] = ($this->session->userdata('advance_search') == 1);
		$data['pageTitle'] = "Active Buyers";
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

		$data['body'] = $this->load->view("property/activebuyer", $data, true);
		$this->load->view('defaulttemplate', $data);
	}

	function ajaxSearch()
	{
		$results = array();
		$resultCount = 0;
		$searchCriteria = $this->session->userdata('buyerSearchCriteria');
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$name = $this->input->post('name', true);
			$availabilityStatus = $this->input->post('availability_status[]', true);
			$buyerStatus = $this->input->post('buyer_status[]', true);
			$acquisitionUpdate = $this->input->post('acquisition_criteria_update', true);

			$searchCriteria = array(
				'name' => $name,
				'landlord_reponsibilities' => $this->input->post('landlord_reponsibilities', true),
				'lease_term_remaining' => $this->input->post('lease_term_remaining', true),
				'state' => $this->input->post('state', true),
				'availability_status' => $availabilityStatus,
				'buyer_status' => $buyerStatus,
				'acquisition_criteria_update' => $acquisitionUpdate,
				'asking_price' => $this->input->post('asking_price', true),
				'asking_rate' => $this->input->post('asking_rate', true),
				'tenant_names' => $this->input->post('tenant_names', true),
				'property_type' => $this->input->post('property_types', true),
				'start' => $this->input->post('start', true),
				'sort_data' => $this->input->post_get('sort_data', true),
			);

			$validatedCriteria = $this->validateCriteria($searchCriteria);
			$this->session->set_userdata('buyerSearchCriteria', (object)$validatedCriteria);

			$results = $this->Activebuyersearch_model->search($validatedCriteria, 0);
			$resultCount = $this->Activebuyersearch_model->count($validatedCriteria);
		} else if (($searchCriteria != null)) {
			$results = $this->Activebuyersearch_model->search((array)$searchCriteria, 0);
			$resultCount = $this->Activebuyersearch_model->count((array)$searchCriteria);
		}

		$activeBuyers = $this->getFormattedActiveBuyers($results);

		//correct serial numbering
		$searchCriteria = $this->session->userdata('buyerSearchCriteria');
		$counter = (int)$searchCriteria->start + 1;
		foreach ($activeBuyers as &$property) {
			$property['sn'] = $counter;
			$counter++;
		}

		echo json_encode(array('recordsTotal' => (int)$resultCount, 'recordsFiltered' => (int)$resultCount, 'draw' => 1, 'data' => $activeBuyers));

		return;
	}

	public function export_active_buyers()
	{
		$exportArray = $this->session->userdata('exports');
		@$exportArray[] = array('ready' => false, 'name' => '', 'type' => 'Properties');
		$currentIndex = sizeof($exportArray);

		$searchCriteria = $this->session->userdata('buyerSearchCriteria');
		$writer = new XLSXWriter();
		if ($searchCriteria != null) {
			$header = array(
				"Contact Name" => "string",
				"Availability Status" => "string",
				"Property Type" => "string",
				"Min Asking Price" => "dollar",
				"Max Asking Price" => "dollar",
				"Min Asking Cap Rate" => "string",
				"Minimum Lease Term Remaining" => "string",
				"Landlord Responsibilities" => "string",
				"Tenant Name" => "string",
				"States" => "string",
				"Acquisition Criteria Update Date" => "date",
			);

			$writer->setTitle("Active Buyers");
			$writer->writeSheetHeader('active buyers', $header);

			$offset = 0;
			$results = $this->Activebuyersearch_model->search((array)$searchCriteria, $offset);

			$rowCount = 1;
			while (sizeof($results) > 0) {
				foreach ($results as $activeBuyer) {
					$row = array(
						$activeBuyer['contact_name'],
						$activeBuyer['availability_status'],
						$activeBuyer['property_type'],
						$activeBuyer['min_asking_price'],
						$activeBuyer['max_asking_price'],
						$activeBuyer['min_asking_rate'],
						$activeBuyer['lease_term_remaining'],
						$activeBuyer['landlord_responsibilities'],
						$activeBuyer['tenant_name'],
						$activeBuyer['states'],
						$activeBuyer['criteria_update_date'],
					);

					$writer->writeSheetRow('active buyers', $row);
					$rowCount++;
				}
				$offset += 100;
				$results = $this->Activebuyersearch_model->search((array)$searchCriteria, $offset);
			}

			$filename = "active-buyers.xlsx";
			header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
			header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
			header('Content-Transfer-Encoding: binary');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			$writer->writeToStdOut();
		} else {
			redirect(base_url());
		}
	}

	private function getFormattedActiveBuyers($results)
	{

		$searchCriteria = $this->session->userdata('buyerSearchCriteria');
		$activeBuyers = [];
		$counter = (int)$searchCriteria->start + 1;
		foreach ($results as $buyer) {
			$contactName = $buyer['first_name'] . " " . $buyer['last_name'];
			$owner = anchor(base_url('contact/acquisition_criteria/' . $buyer['contact_id'] . "?tab=search-criteria&page-type=contact"), $contactName, 'class="link-class" target="_blank"');

			$buyerRow = array(
				'sn' => $counter,
				'owner' => @$owner,
				'availability_status' => $buyer['availability_status'],
				'property_type' => $buyer['property_type'],
				'min_asking_rate' => number_format(floatval($buyer['min_asking_rate']), 2) . "%" ,
				'max_asking_rate' => number_format(floatval($buyer['max_asking_rate']), 2) . "%",
				'min_asking_price' => "$" . number_format(floatval($buyer['min_asking_price']), 0),
				'max_asking_price' => "$" . number_format(floatval($buyer['max_asking_price']), 0),
				'lease_term_remaining' => $buyer['lease_term_remaining'],
				'landlord_responsibilities' => $buyer['landlord_responsibilities'],
				'name' => $buyer['tenant_name'],
				'state' => $buyer['states'],
				'criteria_update_date' => formatDate($buyer['criteria_update_date']),
			);

			$activeBuyers[] = $buyerRow;
			$counter++;
		}

		return $activeBuyers;
	}


	function search()
	{
		$data['showAdvanceSearch'] = ($this->session->userdata('advance_search') == 1);
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$name = $this->input->post('name', true);
			$propertyCount = $this->input->post('property_count', true);
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
			$this->session->set_userdata('buyerSearchCriteria', (object)$validatedCriteria);
		}

		redirect(base_url("property/"));
	}

	function view($contactId)
	{
		$navigation = $this->input->get('type', true);
		$searchCriteria = $this->session->userdata('buyerSearchCriteria');

		$nextActiveBuyer = $this->Activebuyersearch_model->navigateContact((array)$searchCriteria, $contactId, RecordNavigation::NEXT);
		$previousActiveBuyer = $this->Activebuyersearch_model->navigateContact((array)$searchCriteria, $contactId, RecordNavigation::PREVIOUS);
		$data['hasNext'] = (is_array($nextActiveBuyer) && sizeof($nextActiveBuyer) > 0);
		$data['hasPrevious'] = (is_array($previousActiveBuyer) && sizeof($previousActiveBuyer) > 0);

		if ($navigation == RecordNavigation::NEXT) {
			$contact = $this->Activebuyersearch_model->navigateContact((array)$searchCriteria, $contactId, RecordNavigation::NEXT);
			redirect(base_url("activebuyer/view/" . $contact['contact_id']));
		} else if ($navigation == 'previous') {
			$contact = $this->Activebuyersearch_model->navigateContact((array)$searchCriteria, $contactId, RecordNavigation::PREVIOUS);
			redirect(base_url("activebuyer/view/" . $contact['contact_id']));
		} else {
			$contact = $this->Contact_model->get_contact($contactId);
		}

		if (isset($contact['contact_id'])) {
			$data['pageTitle'] = "";
			$data['activeTab'] = "property";
			$data['contact'] =  $contact;
			$data['activeTabBody'] = $this->load->view('contact/propertyactivebuyer', $data, true);
			$data['contactSideBody'] = $this->load->view('contact/contactside', $data, true);
			$data['contactNav'] = $this->load->view('contact/contactnav', $data, true);
			$data['body'] = $this->load->view('contact/view', $data, true);
			$this->load->view('defaulttemplate', $data);
		} else {
			redirect(base_url());
		}
	}

	private function validateCriteria($criteria)
	{
		foreach ($criteria as $key => $value) {
			if (($key != 'state') && ($key != 'asking_price') && ($key != 'sort_data') &&
				($key != 'availability_status') && ($key != 'landlord_reponsibilities') &&
				($key != 'buyer_status') 
				&& ($key != 'property_type')
				&& ($key != 'tenant_names')
				&& ($key != 'asking_rate') && (strlen($criteria[$key]) == 0)
			) {
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
		$this->session->unset_userdata('buyerSearchCriteria');
		redirect(base_url("activebuyer/"));
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
