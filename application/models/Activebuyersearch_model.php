<?php

class Activebuyersearch_model extends CI_Model
{

	private $propertyTable = "properties";
	private $ci;

	public function __construct()
	{
		parent::__construct();
		$this->ci = &get_instance();
		$this->ci->load->model('Contact_model');
		$this->ci->load->model('Company_model');
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
	}

	public function search($criteria, $offset = null)
	{
		$this->db->select("distinct contacts.*, "
			. "ac.*, ac.availability_status as availability_status, ac.property_type as property_type,
			ac.landlord_responsibilities as landlord_responsibilities, ac.tenant_name, "
			. "CONCAT_WS(' ',contacts.first_name,contacts.middle_name,contacts.last_name) as contact_name,
			CONCAT_WS(' ',contacts.first_name,contacts.last_name) as contact_basic_name,
			 contacts.lead_gen_type", false);
		$this->db->join('acquisition_criteria ac', "ac.contact_id=contacts.contact_id", 'left');
		$this->db->from("contacts");

		$this->db = $this->setCriteria($criteria, $this->db);

		if (
			isset($criteria['sort_data']) && is_array($criteria['sort_data'])
			&& (sizeof($criteria['sort_data']) > 0)
		) {
			$orderColumn = $criteria['sort_data']['column'];
			$direction = $criteria['sort_data']['dir'];

			if (($orderColumn == "owner")) {
				$this->db->order_by("contacts.first_name $direction");
			} else if (($orderColumn == "name")) {
				$this->db->order_by("ac.tenant_name $direction");
			} else if (($orderColumn == "state")) {
				$this->db->order_by("ac.states $direction");
			} else if ($orderColumn != "sn") {
				$this->db->order_by("ac.$orderColumn $direction");
			}
		}
		// $this->db->order_by('contacts.contact_id asc');

		if (!(array_filter($criteria))) {
			$this->db->limit(100);
		}

		if (isset($criteria['start']) && (!isset($criteria['no_limit']) && ($offset !== null))) {
			$this->db->limit(100, (int)$criteria['start']);
		}

		if ($offset !== null) {
			$this->db->limit(100, $offset);
		}

		$query = $this->db->get();
		$result = $query->result_array();

		return $result;
	}

	public function navigateContact($criteria, $currentRowId, $type)
	{
		$this->db->select("distinct contacts.*, "
			. "ac.*, ac.availability_status as availability_status,
			ac.landlord_responsibilities as landlord_responsibilities, ac.tenant_name, "
			. "CONCAT_WS(' ',contacts.first_name,contacts.middle_name,contacts.last_name) as contact_name, contacts.lead_gen_type", false);
		$this->db->join('acquisition_criteria ac', "ac.contact_id=contacts.contact_id", 'left');
		$this->db->from("contacts");
		$this->db = $this->setCriteria($criteria, $this->db);

		if ($type == RecordNavigation::NEXT) {
			$this->db->where('contacts.contact_id >', $currentRowId);
			if (isset($criteria['order_by'])) {
				$this->db->order_by($criteria['order_by'] . " desc");
			}
			// $this->db->order_by('contacts.contact_id desc');

		} else {
			$this->db->where('contacts.contact_id <', $currentRowId);
			if (isset($criteria['order_by'])) {
				$this->db->order_by($criteria['order_by'] . " asc");
			}
			// $this->db->order_by('contacts.contact_id asc');

		}

		$this->db->limit(1);
		$query = $this->db->get();
		$result = $query->row_array();

		return $result;
	}

	public function count($criteria)
	{
		$this->db->select("contacts.*, "
			. "ac.*, ac.availability_status as availability_status,
			ac.landlord_responsibilities as landlord_responsibilities, ac.tenant_name, "
			. "CONCAT_WS(' ',contacts.first_name,contacts.middle_name,contacts.last_name) as contact_name, contacts.lead_gen_type", false);
		$this->db->join('acquisition_criteria ac', "ac.contact_id=contacts.contact_id", 'left');
		$this->db->from("contacts");

		$this->db = $this->setCriteria($criteria, $this->db);
		$query = $this->db->get();
		$result = $query->result_array();

		return (sizeof($result) > 0) ? sizeof($result) : 0;
	}

	private function setCriteria($criteria, &$db)
	{
		foreach ($criteria as $key => $param) {
			switch ($key) {
				case 'name':
					if (strlen($criteria[$key]) > 0) {
						$names = explode(',', $criteria[$key]);
						$db->group_start();
						$count = 0;
						foreach ($names as $name) {
							if ($count == 0) {
								$db->like("concat(first_name,' ',last_name)", trim($name));
							} else {
								$db->or_like("concat(first_name,' ', last_name)", trim($name));
							}
							$count++;
						}
						$db->group_end();
					}
					break;

				case 'tenant_names':
					if ((is_array($criteria[$key])) && (sizeof($criteria[$key]) > 0)) {
						$count = 0;
						$group = $db->group_start();
						$tenantNames = $criteria[$key];
						foreach ($tenantNames as $tenantName) {
							if ($count == 0) {
								$group->like('ac.tenant_name', trim($tenantName));
							} else {
								$group->or_like('ac.tenant_name', trim($tenantName));
							}
							$count++;
						}
						$group->group_end();
					}
					break;

				case 'state':
					if ((is_array($criteria[$key])) && (sizeof($criteria[$key]) > 0)) {
						$count = 0;
						$group = $db->group_start();
						$states = $criteria[$key];
						foreach ($states as $state) {
							if ($count == 0) {
								$group->like('ac.states', trim($state));
								$group->or_like('ac.states', "nationwide");
							} else if (($count == 0) && (strtolower($state) == "nationwide")) {
								$group->where('ac.states IS NOT NULL', null, false);
							} else {
								$group->or_like('ac.states', trim($state));
								$group->or_like('ac.states', "nationwide");
							}
							$count++;
						}
						$group->group_end();
					}
					break;

				case 'asking_price':
					$askingPrices = $criteria[$key];
					if ((isset($askingPrices['min_price'])) && (isset($askingPrices['max_price']))
						&& ($askingPrices['min_price'] > 0) && ($askingPrices['max_price'] > 0)
					) {
						$group = $db->group_start();
						$group->where('ac.min_asking_price >=', $askingPrices['min_price']);
						$group->group_end();

						$group = $db->group_start();
						$group->where('ac.max_asking_price <=', $askingPrices['max_price']);
						$group->group_end();
					} else if ((isset($askingPrices['min_price'])) && ($askingPrices['min_price'] > 0)) {
						$group = $db->group_start();
						$group->where('ac.min_asking_price >=', $askingPrices['min_price']);
						$group->group_end();
					}
					break;

				case 'asking_rate':
					$askingCapRates = $criteria[$key];
					if ((isset($askingCapRates['min_rate'])) && (isset($askingCapRates['max_rate']))
						&& ($askingCapRates['min_rate'] > 0) && ($askingCapRates['max_rate'] > 0)
					) {
						$group = $db->group_start();
						$group->where('ac.min_asking_rate >=', $askingCapRates['min_rate']);
						$group->group_end();

						$group = $db->group_start();
						$group->where('ac.max_asking_rate <=', $askingCapRates['max_rate']);
						$group->group_end();
					} else if ((isset($askingCapRates['min_rate'])) && ($askingCapRates['min_rate'] > 0)) {
						$group = $db->group_start();
						$group->where('ac.min_asking_rate <=', $askingCapRates['min_rate']);
						$group->group_end();
					}
					break;

				case 'acquisition_criteria_update':
					$range = explode("-", $criteria[$key]);
					if ((sizeof($range) == 1) && (strtolower(trim($range[0])) != "invalid date")) {
						$startDate = $range[0];
						$db->group_start()
							->where('ac.criteria_update_date', date('Y-m-d', strtotime($startDate)))
							->group_end();
					} else if ((sizeof($range) == 2) && (strtolower(trim($range[1])) != "invalid date")) {
						$startDate = $range[0];
						$endDate = $range[1];
						$db->group_start()
							->where('ac.criteria_update_date >= ', date('Y-m-d', strtotime($startDate)))
							->group_end();

						$db->group_start()
							->where('ac.criteria_update_date <= ', date('Y-m-d', strtotime($endDate)))
							->group_end();
					} else if ((sizeof($range) == 2) && (strtolower(trim($range[1])) == "invalid date") && (strtolower(trim($range[0])) != "invalid date")) {
						$db->group_start()
							->where('ac.criteria_update_date IS NULL', null, false)
							->group_end();
					}
					break;


				case 'lease_term_remaining':
					if (strlen($criteria[$key]) > 0) {
						$db->group_start()
							->where('ac.lease_term_remaining <=', $param)
							->group_end();
					}
					break;

				case 'property_type':
					if ((is_array($criteria[$key])) && (sizeof($criteria[$key]) > 0)) {
						$count = 0;
						$group = $db->group_start();
						$types = $criteria[$key];
						foreach ($types as $type) {
							if (strlen($type) > 0) {
								if ($count == 0) {
									$group->like('ac.property_type', trim($type));
								} else {
									$group->or_like('ac.property_type', trim($type));
								}
							}

							$count++;
						}
						$group->group_end();
					}
					break;

				case 'landlord_reponsibilities':
					if ((is_array($criteria[$key])) && (sizeof($criteria[$key]) > 0)) {
						$count = 0;
						$group = $db->group_start();
						$responsibilities = $criteria[$key];
						foreach ($responsibilities as $responsibility) {
							if (strlen($responsibility) > 0) {
								if ($count == 0) {
									$group->like('ac.landlord_responsibilities', trim($responsibility));
								} else {
									$group->or_like('ac.landlord_responsibilities', trim($responsibility));
								}
							}

							$count++;
						}
						$group->group_end();
					}
					break;

				case 'availability_status':
					if ((is_array($criteria[$key])) && (sizeof($criteria[$key]) > 0)) {
						$count = 0;
						$group = $db->group_start();
						$availabilityStatus = $criteria[$key];
						foreach ($availabilityStatus as $status) {
							if (strlen($status) > 0) {
								if ($count == 0) {
									$group->like('ac.availability_status', trim($status));
								} else {
									$group->or_like('ac.availability_status', trim($status));
								}
							}

							$count++;
						}
						$group->group_end();
					}
					break;

				case 'buyer_status':
					if ((is_array($criteria[$key])) && (sizeof($criteria[$key]) > 0)) {
						$count = 0;
						$group = $db->group_start();
						$availabilityStatus = $criteria[$key];
						foreach ($availabilityStatus as $status) {
						   if($status == 'blank'){
						        $group->where('ac.buyer_status IS NULL', null, false);
						        $group->or_where('ac.buyer_status', '');
						    }else if (strlen($status) > 0) {
								if ($count == 0) {
									$group->like('ac.buyer_status', trim($status));
								} else {
									$group->or_like('ac.buyer_status', trim($status));
								}
							}

							$count++;
						}
						$group->group_end();
					}
					break;
			}
		}

		$db->group_start();
		$db->where("contacts.active_buyer", 1);
		$db->group_end();

		return $db;
	}
}
