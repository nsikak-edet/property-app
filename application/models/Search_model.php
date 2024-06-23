<?php

class Search_model extends CI_Model
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
		$this->db->select("distinct prop.*,contacts.*,  prop.last_update as last_update, prop.company_id as com_id, prop.tax_record_sent_date as tax_record_sent_date, "
			. "prop.contact_id as con_id, companies.name as company_name, prop.name as name, prop.asking_price as asking_price,"
			. "CONCAT_WS(' ',first_name,middle_name,last_name) as contact_name, contacts.lead_gen_type
			, prop.availability_status as availability_status", false);
		$this->db->join('contacts', 'contacts.contact_id=prop.contact_id', 'left');
		$this->db->join('companies', 'companies.company_id=prop.company_id', 'left');
		$this->db->join('addresses', "companies.company_id=addresses.entity_id AND addresses.entity_type='company'", 'left');
		$this->db->join('property_histories', "property_histories.property_id=prop.property_id", 'left');
		$this->db->from("properties as prop");
		$this->db = $this->setCriteria($criteria, $this->db);

		if (isset($criteria['for_sale'])) {
			if (
				isset($criteria['sort_data']) && is_array($criteria['sort_data'])
				&& (sizeof($criteria['sort_data']) > 0)
			) {
				$orderColumn = $criteria['sort_data']['column'];
				$direction = $criteria['sort_data']['dir'];

				if (($orderColumn == "owner")) {
					$this->db->order_by("contacts.first_name, companies.name $direction");
				} else if (($orderColumn == "name")) {
					$this->db->order_by("prop.name $direction");
				} else if (($orderColumn == "availability_status_update_date")) {
					$this->db->order_by("prop.availability_update_date $direction");
				} else if ($orderColumn != "sn" && $orderColumn != "lease_term_remaining") {
					$this->db->order_by("prop.$orderColumn $direction");
				}
			}
		}


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

		if(isset($criteria['for_sale'])){
			$result = $this->formatForSaleResult($result);
		}else{
			$result = $this->formatSearchResult($result);
		}


		return $result;
	}

	public function orderBy($criteria, &$db)
	{
		if ((isset($criteria['sort_by']) && (strlen($criteria['sort_by']) > 1))) {
			$orderByField = $criteria['sort_by'];
			$db->order_by('prop.property_id asc');
			// $db->order_by('prop.availability_update_date desc');
			$db->order_by("prop.$orderByField");
		} else {
			$db->order_by('prop.property_id asc');
			// $db->order_by('prop.availability_update_date desc');
		}

		return $db;
	}

	public function navigateProperty($criteria, $currentRowId, $type)
	{
		$this->db->select("distinct prop.*,contacts.*, prop.company_id as com_id, "
			. "prop.contact_id as con_id, companies.name as company_name, prop.name as name, "
			. "CONCAT_WS(' ',first_name,middle_name,last_name) as contact_name, contacts.lead_gen_type", false);
		$this->db->join('contacts', 'contacts.contact_id=prop.contact_id', 'left');
		$this->db->join('companies', 'companies.company_id=prop.company_id', 'left');
		$this->db->join('addresses', "companies.company_id=addresses.entity_id AND addresses.entity_type='company'", 'left');

		$this->db->from("properties as prop");
		$this->db = $this->setCriteria($criteria, $this->db);

		if ($type == RecordNavigation::NEXT) {
			$this->db->where('prop.property_id >', $currentRowId);
			$this->db->order_by('prop.property_id asc');
			$this->db->order_by('prop.availability_update_date desc');
		} else {
			$this->db->where('prop.property_id <', $currentRowId);
			$this->db->order_by('prop.property_id desc');
			$this->db->order_by('prop.availability_update_date desc');
		}

		$this->db->limit(1);
		$query = $this->db->get();
		$result = $query->row_array();

		return $result;
	}

	public function navigateOwners($criteria, $currentRowId, $type)
	{
		$this->db->select("prop.*,contacts.*, prop.company_id as com_id, CONCAT_WS('',prop.company_id,'cid',prop.contact_id,'coid') as owner_key,"
			. "prop.contact_id as con_id, companies.name as company_name, prop.name as name, "
			. "CONCAT_WS(' ',first_name,middle_name,last_name) as contact_name, contacts.lead_gen_type", false);
		$this->db->join('contacts', 'contacts.contact_id=prop.contact_id', 'left');
		$this->db->join('companies', 'companies.company_id=prop.company_id', 'left');
		$this->db->join('addresses', "companies.company_id=addresses.entity_id AND addresses.entity_type='company'", 'left');

		$this->db->from("properties as prop");
		$this->db = $this->setCriteria($criteria, $this->db);

		if ($type == RecordNavigation::NEXT) {
			$this->db->where('prop.property_id > ', $currentRowId);
			$this->db->where("CONCAT_WS('',prop.company_id,'cid',prop.contact_id,'coid') != ", 'cidcoid');
			$this->db->group_by('owner_key');
			$this->db->order_by('prop.property_id asc');
			$this->db->order_by('prop.availability_update_date desc');
		} else {
			$this->db->where('prop.property_id < ', $currentRowId);
			$this->db->where("CONCAT_WS('',prop.company_id,'cid',prop.contact_id,'coid') != ", 'cidcoid');
			$this->db->group_by('owner_key');
			$this->db->order_by('prop.property_id desc');
			$this->db->order_by('prop.availability_update_date desc');
		}

		$this->db->limit(1);
		$query = $this->db->get();
		$result = $query->result_array();

		return $result;
	}


	public function searchOwners($criteria, $offset = null)
	{
		$this->db->select("prop.*,contacts.*, prop.company_id as com_id, CONCAT_WS('',prop.company_id,'cid',prop.contact_id,'coid') as owner_key,"
			. "prop.contact_id as con_id, companies.name as company_name, companies.website, prop.name as name, "
			. "CONCAT_WS(' ',first_name,middle_name,last_name) as contact_name, contacts.lead_gen_type", false);
		$this->db->join('contacts', 'contacts.contact_id=prop.contact_id', 'left');
		$this->db->join('companies', 'companies.company_id=prop.company_id', 'left');
		$this->db->join('addresses', "companies.company_id=addresses.entity_id AND addresses.entity_type='company'", 'left');
		$this->db->join('property_histories', "property_histories.property_id=prop.property_id", 'left');

		$this->db->from("properties as prop");
		$this->db = $this->setCriteria($criteria, $this->db);
		$this->db->group_by('owner_key');
		$this->db->order_by('prop.property_id asc');

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

		$result = $this->formatSearchResult($result);

		return $result;
	}

	public function search_x_property($n)
	{
		$sqlCompanySearch = "SELECT "
			. "companies.name,addresses.*,"
			. "'company' as record_type, "
			. " COUNT(DISTINCT(properties.property_id)) as property_count"
			. " FROM `properties` "
			. " INNER JOIN companies ON companies.company_id=properties.company_id"
			. " LEFT JOIN addresses ON addresses.entity_id=companies.company_id AND addresses.entity_type='company'"
			. " LEFT JOIN phones ON phones.entity_id=companies.company_id AND phones.entity_type='company'"
			. " GROUP BY companies.company_id HAVING COUNT(DISTINCT(properties.property_id)) = $n";

		$sqlContactSearch = "SELECT "
			. "concat_ws(' ',first_name,middle_name,last_name) as name,addresses.*,"
			. " 'contact' as record_type,"
			. " COUNT(DISTINCT(properties.property_id)) as property_count FROM `properties` "
			. " INNER JOIN contacts ON contacts.contact_id=properties.contact_id"
			. " LEFT JOIN addresses ON addresses.entity_id=contacts.contact_id AND addresses.entity_type='contact'"
			. " LEFT JOIN phones ON phones.entity_id=contacts.contact_id AND phones.entity_type='contact'"
			. " GROUP BY contacts.contact_id HAVING COUNT(DISTINCT(properties.property_id)) = $n";

		$query = $this->db->query($sqlContactSearch . " UNION " . $sqlCompanySearch);
		$result = $query->result_array();

		return $result;
	}

	public function search_state_property($state)
	{
		$sqlCompanySearch = "SELECT "
			. "companies.name,addresses.*,"
			. "'company' as record_type, "
			. " COUNT(DISTINCT(properties.property_id)) as property_count"
			. " FROM `properties` "
			. " INNER JOIN companies ON companies.company_id=properties.company_id"
			. " LEFT JOIN addresses ON addresses.entity_id=companies.company_id AND addresses.entity_type='company'"
			. " LEFT JOIN phones ON phones.entity_id=companies.company_id AND phones.entity_type='company'"
			. " WHERE properties.state LIKE '%$state%'"
			. " GROUP BY companies.company_id HAVING COUNT(DISTINCT(properties.property_id)) > 0";

		$sqlContactSearch = "SELECT "
			. "concat_ws(' ',first_name,middle_name,last_name) as name,addresses.*,"
			. " 'contact' as record_type,"
			. " COUNT(DISTINCT(properties.property_id)) as property_count FROM `properties` "
			. " INNER JOIN contacts ON contacts.contact_id=properties.contact_id"
			. " LEFT JOIN addresses ON addresses.entity_id=contacts.contact_id AND addresses.entity_type='contact'"
			. " LEFT JOIN phones ON phones.entity_id=contacts.contact_id AND phones.entity_type='contact'"
			. " WHERE properties.state LIKE '%$state%'"
			. " GROUP BY contacts.contact_id HAVING COUNT(DISTINCT(properties.property_id)) > 0";

		$query = $this->db->query($sqlContactSearch . " UNION " . $sqlCompanySearch);
		$result = $query->result_array();

		return $result;
	}

	public function count($criteria)
	{
		$this->db->select("prop.property_id");
		$this->db->join('contacts', 'contacts.contact_id=prop.contact_id', 'left');
		$this->db->join('companies', 'companies.company_id=prop.company_id', 'left');
		$this->db->join('addresses', "companies.company_id=addresses.entity_id AND addresses.entity_type='company'", 'left');
		$this->db->join('property_histories', "property_histories.property_id=prop.property_id", 'left');
		$this->db->from($this->propertyTable . " as prop");
		$this->db = $this->setCriteria($criteria, $this->db);
		$this->db->group_by('prop.property_id');
		$query = $this->db->get();
		$result = $query->result_array();

		return (sizeof($result) > 0) ? sizeof($result) : 0;
	}

	public function countOwners($criteria)
	{
		$this->db->select("CONCAT_WS('',prop.company_id,'cid',prop.contact_id,'coid') as owner_key");
		$this->db->join('contacts', 'contacts.contact_id=prop.contact_id', 'left');
		$this->db->join('companies', 'companies.company_id=prop.company_id', 'left');
		$this->db->join('addresses', "companies.company_id=addresses.entity_id AND addresses.entity_type='company'", 'left');
		$this->db->join('property_histories', "property_histories.property_id=prop.property_id", 'left');
		$this->db->from($this->propertyTable . " as prop");
		$this->db = $this->setCriteria($criteria, $this->db);
		$this->db->group_by('owner_key');
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
								$db->like('prop.name', trim($name));
							} else {
								$db->or_like('prop.name', trim($name));
							}

							$count++;
						}
						$db->group_end();
					}
					break;

				case 'store_no':
					if (strlen($criteria[$key]) > 0)
						$db->where('prop.store_number', $param);
					break;

				case 'city':
					if (strlen($criteria[$key]) > 0)
						$db->like('prop.city', $param);
					break;

				case 'state':
					if ((is_array($criteria[$key])) && (sizeof($criteria[$key]) > 0)) {
						$count = 0;
						$group = $db->group_start();
						$states = $criteria[$key];
						foreach ($states as $state) {
							if ($count == 0) {
								$group->where('prop.state', trim($state));
							} else {
								$group->or_where('prop.state', trim($state));
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
						$group->where('prop.asking_price >=', $askingPrices['min_price']);
						$group->where('prop.asking_price <=', $askingPrices['max_price']);
						$group->group_end();
					} else if ((isset($askingPrices['min_price'])) && ($askingPrices['min_price'] > 0)) {
						$group = $db->group_start();
						$group->where('prop.asking_price =', $askingPrices['min_price']);
						$group->group_end();
					}
					break;

				case 'asking_rate':
					$askingCapRates = $criteria[$key];
					if ((isset($askingCapRates['min_rate'])) && (isset($askingCapRates['max_rate']))
						&& ($askingCapRates['min_rate'] > 0) && ($askingCapRates['max_rate'] > 0)
					) {
						$group = $db->group_start();
						$group->where('prop.asking_cap_rate >=', $askingCapRates['min_rate']);
						$group->where('prop.asking_cap_rate <=', $askingCapRates['max_rate']);
						$group->group_end();
					} else if ((isset($askingCapRates['min_rate'])) && ($askingCapRates['min_rate'] > 0)) {
						$group = $db->group_start();
						$group->where('prop.asking_cap_rate =', $askingCapRates['min_rate']);
						$group->group_end();
					} 
					break;

				case 'company':
					if (strlen($criteria[$key]) > 0)
						$db->like('companies.name', $param);
					break;

				case 'first_name':
					if (strlen($criteria[$key]) > 0)
						$db->like('contacts.first_name', $param);
					break;

				case 'last_name':
					if (strlen($criteria[$key]) > 0)
						$db->like('contacts.last_name', $param);
					break;

				case 'lead_gen_type':
					if ($criteria[$key] == LeadGenOptions::MET_OR_HAVENT_MET) {
						$db->group_start()
							->where("contacts.lead_gen_type =", ucwords(LeadGenOptions::MET))
							->or_where("contacts.lead_gen_type =", ucwords(LeadGenOptions::HAVENT_MET))
							->or_group_start()
							->where("prop.company_id IS NOT NULL", null, false)
							->where("EXISTS(SELECT email FROM contacts cn WHERE cn.company_id=prop.company_id AND cn.lead_gen_type = 'met')")
							->group_end()
							->or_group_start()
							->where("prop.company_id IS NOT NULL", null, false)
							->where('EXISTS(SELECT email FROM contacts cn WHERE cn.company_id=prop.company_id AND cn.lead_gen_type = "haven\'t met")')
							->group_end()
							->group_end();
					}

					if ($criteria[$key] == LeadGenOptions::MET_HAVENT_MET) {
						$db->group_start();
						$db->where("contacts.lead_gen_type", ucwords(LeadGenOptions::MET));
						$db->or_where("contacts.lead_gen_type", ucwords(LeadGenOptions::HAVENT_MET));
						$db->group_end();
					}

					if($criteria[$key] == LeadGenOptions::NOT_MET_OR_HAVENT_MET) {
						$db->group_start()
							->where("contacts.lead_gen_type !=", ucwords(LeadGenOptions::MET))
							->where("contacts.lead_gen_type !=", ucwords(LeadGenOptions::HAVENT_MET))
							->or_group_start()
							->where("prop.company_id IS NOT NULL", null, false)
							->where("NOT EXISTS(SELECT email FROM contacts cn WHERE cn.company_id=prop.company_id AND cn.lead_gen_type = 'met')")
							->where('NOT EXISTS(SELECT email FROM contacts cn WHERE cn.company_id=prop.company_id AND cn.lead_gen_type = "haven\'t met")')
							->group_end()
							->group_end();
					}

					if ($criteria[$key] == LeadGenOptions::MET) {
						$db->group_start()
							->where("contacts.lead_gen_type =", ucwords(LeadGenOptions::MET))
							->or_group_start()
							->where("prop.company_id IS NOT NULL", null, false)
							->where("EXISTS(SELECT email FROM contacts cn WHERE cn.company_id=prop.company_id AND cn.lead_gen_type = 'met')")
							->group_end()
							->group_end();
					}

					if ($criteria[$key] == LeadGenOptions::HAVENT_MET) {
						$db->group_start()
							->where("contacts.lead_gen_type =", ucwords(LeadGenOptions::HAVENT_MET))
							->or_group_start()
							->where("prop.company_id IS NOT NULL", null, false)
							->where('EXISTS(SELECT email FROM contacts cn WHERE cn.company_id=prop.company_id AND cn.lead_gen_type = "haven\'t met")')
							->group_end()
							->group_end();
					}
					break;

				case 'do_not_send':
					if ((strlen($criteria[$key]) > 0) && (strtolower($criteria[$key])) == DoNotSendOptions::YES) {
						$db->group_start()
							->where('contacts.do_not_send ', 0)
							->or_where('companies.do_not_send', 0)
							->group_end();
					} else if ((strlen($criteria[$key]) > 0) && (strtolower($criteria[$key])) == DoNotSendOptions::NO) {
						$db->group_start()
							->where('contacts.do_not_send ', 1)
							->or_where('companies.do_not_send', 1)
							->group_end();
					}
					break;

				case 'do_not_blast':
					if ((strlen($criteria[$key]) > 0) && (strtolower($criteria[$key])) == DoNotSendOptions::YES) {
						$db->group_start()
							->where('contacts.do_not_blast ', 0)
							->or_where('companies.do_not_blast', 0)
							->group_end();
					} else if ((strlen($criteria[$key]) > 0) && (strtolower($criteria[$key])) == DoNotSendOptions::NO) {
						$db->group_start()
							->where('contacts.do_not_blast ', 1)
							->or_where('companies.do_not_blast', 1)
							->group_end();
					}
					break;

				case 'availability_status':
					$availability = $criteria[$key];
					if ((strlen($availability) > 0)) {
						if ($availability == 'blank') {
							$db->group_start()
								->where('prop.availability_status IS NULL', null, false)
								->or_where("prop.availability_status =''", null, false)
								->group_end();
						} else {
							$availability = explode(",", $availability);
							if (is_array($availability) && (sizeof($availability) > 0)) {
								$db->group_start();
								$counter = 0;
								foreach ($availability as $status) {
									if ($counter == 0) {
										$db->like('prop.availability_status', $status);
									} else {
										$db->or_like('prop.availability_status', $status);
									}

									$counter++;
								}
								$db->group_end();
							}
						}
					}
					break;

				case 'for_sale':
				    $db->group_start()
    					    ->group_start()
        						->like('prop.availability_status', AvailabilityStatus::OFF_MARKET)
        						->or_like('prop.availability_status', AvailabilityStatus::ON_MARKET)
        						->or_like('prop.availability_status', AvailabilityStatus::UNDER_CONTRACT)
        						->or_like('prop.availability_status', AvailabilityStatus::UNDER_LOI)
        						->or_like('prop.availability_status', AvailabilityStatus::PIPELINE)
        						->or_where('prop.availability_status IS NULL', null, false)
        						->or_where("prop.availability_status =''", null, false)
        					->group_end()
        					->group_start()
        					    ->group_start()
            					    ->where('prop.availability_status IS NOT NULL', null, false)
            					    ->where("prop.availability_status !=''", null, false)
            					->group_end()
        					    ->or_where('prop.availability_update_date IS NOT NULL', null, false)
        					->group_end()
						->group_end();
					break;

				case 'bad_no':
					if ((strlen($criteria[$key]) > 0) && (strtolower($criteria[$key])) == DoNotSendOptions::YES) {
						$db->group_start()
							->where('contacts.bad_no ', 0)
							->or_where('companies.bad_no', 0)
							->group_end();
					} else if ((strlen($criteria[$key]) > 0) && (strtolower($criteria[$key])) == DoNotSendOptions::NO) {
						$db->group_start()
							->where('contacts.bad_no ', 1)
							->or_where('companies.bad_no', 1)
							->group_end();
					}
					break;

				case 'has_owner':
					if ((strlen($criteria[$key]) > 0) && (strtolower($criteria[$key])) == DoNotSendOptions::NO) {
						$db->group_start()
							->where('prop.contact_id IS NULL', null, false)
							->where('prop.company_id  IS NULL', null, false)
							->group_end();
					} else if ((strlen($criteria[$key]) > 0) && (strtolower($criteria[$key])) == DoNotSendOptions::YES) {
						$db->group_start()
							->where('prop.contact_id IS NOT NULL', null, false)
							->or_where('prop.company_id IS NOT NULL', null, false)
							->group_end();
					}
					break;

				case 'last_dial':
					$range = explode("-", $criteria[$key]);
					if ((sizeof($range) == 1) && (strtolower(trim($range[0])) != "invalid date")) {
						$startDate = $range[0];
						$db->where('contacts.last_dial', date('Y-m-d', strtotime($startDate)));
						$db->or_where('companies.last_dial', date('Y-m-d', strtotime($startDate)));
					} else if ((sizeof($range) == 2) && (strtolower(trim($range[0])) == "invalid date") && (strtolower(trim($range[1])) != "invalid date")) {
						$db->group_start()
							->group_start()
							->where('DATEDIFF(CURDATE(), DATE(contacts.last_dial)) > ', 90)
							->or_group_start()
							->where('contacts.last_dial IS NULL', null, false)
							->where('prop.company_id IS NULL', null, false)
							->group_end()
							->group_end()

							->or_group_start()
							->where('DATEDIFF(CURDATE(), DATE(companies.last_dial)) >', 90)
							->or_group_start()
							->where('companies.last_dial IS NULL', null, false)
							->where('prop.contact_id IS NULL', null, false)
							->group_end()
							->group_end()
							->group_end();
					} else if ((sizeof($range) == 2) && (strtolower(trim($range[1])) != "invalid date") && (strtolower(trim($range[0])) != "invalid date")) {
						$startDate = $range[0];
						$endDate = $range[1];
						$db->group_start()
							->group_start()
							->where('contacts.last_dial >= ', date('Y-m-d', strtotime($startDate)))
							->where('contacts.last_dial <=', date('Y-m-d', strtotime($endDate)))
							->group_end()
							->or_group_start()
							->where('companies.last_dial >= ', date('Y-m-d', strtotime($startDate)))
							->where('companies.last_dial <=', date('Y-m-d', strtotime($endDate)))
							->group_end()
							->group_end();
					} else if ((sizeof($range) == 2) && (strtolower(trim($range[1])) == "invalid date") && (strtolower(trim($range[0])) != "invalid date")) {
						$db->where('contacts.last_dial IS NULL', null, false);
						$db->where('companies.last_dial IS NULL', null, false);
					}
					break;

				case 'tax_record_sent_date':
					$range = explode("-", $criteria[$key]);
					if ((sizeof($range) == 1) && (strtolower(trim($range[0])) != "invalid date")) {
						$startDate = $range[0];
						$db->where('contacts.tax_record_sent_date', date('Y-m-d', strtotime($startDate)));
						$db->or_where('companies.tax_record_sent_date', date('Y-m-d', strtotime($startDate)));
					} else if ((sizeof($range) == 2) && (strtolower(trim($range[1])) != "invalid date")) {
						$startDate = $range[0];
						$endDate = $range[1];
						$db->group_start()
							->group_start()
							->where('contacts.tax_record_sent_date >= ', date('Y-m-d', strtotime($startDate)))
							->where('contacts.tax_record_sent_date <=', date('Y-m-d', strtotime($endDate)))
							->group_end()
							->or_group_start()
							->where('companies.tax_record_sent_date >= ', date('Y-m-d', strtotime($startDate)))
							->where('companies.tax_record_sent_date <=', date('Y-m-d', strtotime($endDate)))
							->group_end()
							->group_end();
					} else if ((sizeof($range) == 2) && (strtolower(trim($range[1])) == "invalid date") && (strtolower(trim($range[0])) != "invalid date")) {
						$db->where('contacts.tax_record_sent_date IS NULL', null, false);
						$db->where('companies.tax_record_sent_date IS NULL', null, false);
					}
					break;

				case 'last_update':
					$range = explode("-", $criteria[$key]);
					if ((sizeof($range) == 1) && (strtolower(trim($range[0])) != "invalid date")) {
						$startDate = $range[0];
						$db->where('prop.last_update', date('Y-m-d', strtotime($startDate)));
					} else if ((sizeof($range) == 2) && (strtolower(trim($range[1])) != "invalid date")) {
						$startDate = $range[0];
						$endDate = $range[1];
						$db->group_start()
							->where('prop.last_update >= ', date('Y-m-d', strtotime($startDate)))
							->where('prop.last_update <=', date('Y-m-d', strtotime($endDate)))
							->group_end();
					} else if ((sizeof($range) == 2) && (strtolower(trim($range[1])) == "invalid date") && (strtolower(trim($range[0])) != "invalid date")) {
						$db->where('prop.last_update IS NULL', null, false);
					}
					break;

				case 'availability_update':
					$range = explode("-", $criteria[$key]);
					if ((sizeof($range) == 1) && (strtolower(trim($range[0])) != "invalid date")) {
						$startDate = $range[0];
						$db->where('prop.availability_update_date', date('Y-m-d', strtotime($startDate)));
					} else if ((sizeof($range) == 2) && (strtolower(trim($range[1])) != "invalid date")) {
						$startDate = $range[0];
						$endDate = $range[1];
						$db->group_start()
							->where('prop.availability_update_date >= ', date('Y-m-d', strtotime($startDate)))
							->where('prop.availability_update_date <=', date('Y-m-d', strtotime($endDate)))
							->group_end();
					} else if ((sizeof($range) == 2) && (strtolower(trim($range[1])) == "invalid date") && (strtolower(trim($range[0])) != "invalid date")) {
						$db->where('prop.availability_update_date IS NULL', null, false);
					}
					break;

				case 'last_sold_date':
					$range = explode("-", $criteria[$key]);
					if ((sizeof($range) == 1) && (strtolower(trim($range[0])) != "invalid date")) {
						$startDate = $range[0];
						$db->where('property_histories.last_sold_date', date('Y-m-d', strtotime($startDate)));
					}else if ((sizeof($range) == 2) && (strtolower(trim($range[1])) != "invalid date")) {
						$startDate = $range[0];
						$endDate = $range[1];
						$db->group_start()
							->where('property_histories.last_sold_date >= ', date('Y-m-d', strtotime($startDate)))
							->where('property_histories.last_sold_date <=', date('Y-m-d', strtotime($endDate)))
							->group_end();
					}else if ((sizeof($range) == 2) && (strtolower(trim($range[1])) == "invalid date") && (strtolower(trim($range[0])) != "invalid date")) {
						$db->where('property_histories.last_sold_date IS NULL', null, false);
					}
					break;

				case 'zip_code':
					if (strlen($criteria[$key]) > 0)
						$db->where('prop.zip_code', $param);
					break;

				case 'street_address':
					if (strlen($criteria[$key]) > 0)
						$db->like('prop.address', $param);
					break;

				case 'property_type':
					if (strlen($criteria[$key]) > 0)
						$db->like('prop.property_type', $param);
					break;

				case 'property_count':
					if (strlen($criteria[$key]) > 0) {
						$states = $criteria['state'];

						$propertyTypeQuery = "";
						if (isset($criteria['property_type'])) {
							$propertyType = $criteria['property_type'];
							$propertyTypeQuery = (strlen(trim($propertyType)) > 0) ? " AND p2.property_type='$propertyType'" : "";
						}

						$propertyName = (isset($criteria['name'])) ? $criteria['name'] : '';
						$stateQuery = $this->composeStateQuery($states, $propertyName);
						$range = explode('-', $criteria[$key]);
						$minVal = null;
						$maxVal = null;
						if (sizeof($range) == 2) {
							$minVal = intval($range[0]);
							$maxVal = intval($range[1]);

							$db->group_start();
							$db->where("EXISTS(SELECT state, name,"
								. "COUNT(p2.company_id) as freq "
								. "FROM properties p2 "
								. "WHERE prop.company_id=p2.company_id $stateQuery $propertyTypeQuery "
								. "HAVING (freq BETWEEN $minVal AND $maxVal))");

							$db->or_where("EXISTS(SELECT state, name,"
								. "COUNT(p2.contact_id) as freq "
								. "FROM properties p2 "
								. "WHERE prop.contact_id=p2.contact_id $stateQuery $propertyTypeQuery "
								. "HAVING (freq BETWEEN $minVal AND $maxVal))");
							$db->group_end();
						} else if (sizeof($range) == 1) {
							$minVal = intval($range[0]);
							$db->group_start();
							$db->where("EXISTS(SELECT state, name,"
								. "COUNT(p2.company_id) as freq "
								. "FROM properties p2 "
								. "WHERE prop.company_id=p2.company_id $stateQuery $propertyTypeQuery "
								. "HAVING (freq = $minVal))");

							$db->or_where("EXISTS(SELECT state, name,"
								. "COUNT(p2.contact_id) as freq "
								. "FROM properties p2 "
								. "WHERE prop.contact_id=p2.contact_id $stateQuery $propertyTypeQuery "
								. "HAVING (freq = $minVal))");
							$db->group_end();
						}
					}
					break;

				case 'owner_phone':
					$param = $criteria[$key];
					if ($param != null) {
						if ($param == FilterOptions::IS_EMPTY) {
							$db->group_start();
							$db->where("NOT EXISTS(SELECT phone "
								. "FROM phones company_phones "
								. "WHERE prop.company_id=company_phones.entity_id AND company_phones.entity_type='company' "
								. " AND prop.company_id > 0)");

							$db->where("NOT EXISTS(SELECT phone "
								. "FROM phones contact_phones "
								. "WHERE prop.contact_id=contact_phones.entity_id AND contact_phones.entity_type='contact' "
								. "AND prop.contact_id > 0)");
							$db->group_end();
						} else if ($param == FilterOptions::IS_NOT_EMPTY) {
							$db->group_start();
							$db->where("EXISTS(SELECT phone "
								. "FROM phones company_phones "
								. "WHERE prop.company_id=company_phones.entity_id AND company_phones.entity_type='company' "
								. "AND company_phones.phone IS NOT NULL AND prop.company_id > 0 AND LENGTH(company_phones.phone) > 0)");

							$db->or_where("EXISTS(SELECT phone "
								. "FROM phones contact_phones "
								. "WHERE prop.contact_id=contact_phones.entity_id AND contact_phones.entity_type='contact' "
								. "AND contact_phones.phone IS NOT NULL AND prop.contact_id > 0 AND LENGTH(contact_phones.phone) > 0)");
							$db->group_end();
						} else {
							$db->group_start();
							$db->where("EXISTS(SELECT phone "
								. "FROM phones company_phones "
								. "WHERE prop.company_id=company_phones.entity_id AND company_phones.entity_type='company' "
								. "AND company_phones.phone LIKE '%$param%')");

							$db->or_where("EXISTS(SELECT phone "
								. "FROM phones contact_phones "
								. "WHERE prop.contact_id=contact_phones.entity_id AND contact_phones.entity_type='contact' "
								. "AND contact_phones.phone LIKE '%$param%')");
							$db->group_end();
						}
					}
					break;

				case 'owner_website':
					$param = $criteria[$key];
					if ($param != null) {
						if ($param == FilterOptions::IS_EMPTY) {
							$db->group_start();
							$db->where("EXISTS(SELECT website "
								. "FROM companies company_website "
								. "WHERE prop.company_id=company_website.company_id "
								. "AND company_website.website IS NULL)");
							$db->group_end();
						} else if ($param == FilterOptions::IS_NOT_EMPTY) {
							$db->group_start();
							$db->where("EXISTS(SELECT website "
								. "FROM companies company_website "
								. "WHERE prop.company_id=company_website.company_id "
								. "AND company_website.website IS NOT NULL AND prop.company_id > 0 AND LENGTH(company_website.website) > 0)");
							$db->group_end();
						} else {
							$db->group_start();
							$db->where("EXISTS(SELECT website "
								. "FROM companies company_website "
								. "WHERE prop.company_id=company_website.company_id "
								. "AND company_website.website LIKE '%$param%')");
							$db->group_end();
						}
					}
					break;

				case 'owner_address':
					$param = $criteria[$key];
					if ($param != null) {
						if ($param == FilterOptions::IS_EMPTY) {
							$db->group_start();
							$db->where("NOT EXISTS(SELECT address "
								. "FROM addresses company_address "
								. "WHERE prop.company_id=company_address.entity_id AND company_address.entity_type='company' "
								. " AND prop.company_id > 0)");

							$db->where("NOT EXISTS(SELECT address "
								. "FROM addresses contact_address "
								. "WHERE prop.contact_id=contact_address.entity_id AND contact_address.entity_type='contact' "
								. "AND prop.contact_id > 0)");
							$db->group_end();
						} else if ($param == FilterOptions::IS_NOT_EMPTY) {
							$db->group_start();
							$db->where("EXISTS(SELECT address "
								. "FROM addresses company_address "
								. "WHERE prop.company_id=company_address.entity_id AND company_address.entity_type='company' "
								. "AND company_address.address IS NOT NULL AND prop.company_id > 0 AND LENGTH(company_address.address) > 0)");

							$db->or_where("EXISTS(SELECT address "
								. "FROM addresses contact_address "
								. "WHERE prop.contact_id=contact_address.entity_id AND contact_address.entity_type='contact' "
								. "AND contact_address.address IS NOT NULL AND prop.contact_id > 0 AND LENGTH(contact_address.address) > 0)");
							$db->group_end();
						} else {
							$db->group_start();
							$db->where("EXISTS(SELECT address "
								. "FROM addresses company_address "
								. "WHERE prop.company_id=company_address.entity_id AND company_address.entity_type='company' "
								. "AND company_address.address LIKE '%$param%')");

							$db->or_where("EXISTS(SELECT address "
								. "FROM addresses contact_address "
								. "WHERE prop.contact_id=contact_address.entity_id AND contact_address.entity_type='contact' "
								. "AND contact_address.address LIKE '%$param%')");
							$db->group_end();
						}
					}
					break;
			}
		}

		return $db;
	}

	private function composeStateQuery($states, $propertyName)
	{
		$stateQuery = "";
		if ((is_array($states)) && (sizeof($states) > 0)) {
			$count = 0;
			foreach ($states as $state) {
				if ($count == 0) {
					$stateQuery = "state='$state'";
				} else {
					$stateQuery .= " OR state='$state'";
				}

				$count++;
			}

			if (strlen($stateQuery) > 0) {
				$stateQuery = " AND (" . $stateQuery . ")";
			}
		}

		if ((strlen($propertyName) > 0)) {
			$propertyName = $this->db->escape_like_str($propertyName);
			$stateQuery .= " AND (p2.name LIKE '%" . $propertyName . "')";
		}

		return $stateQuery;
	}

	private function formatSearchResult($properties)
	{
		$groupedProperties = [];

		foreach ($properties as $property) {
			$uniqueKey = trim($property['company_name']) . trim($property['contact_name']);
			$uniqueKey = (strlen($uniqueKey) > 0) ? $uniqueKey : "not linked";
			$groupedProperties[$uniqueKey]['properties'][] = $property;
			$groupedProperties[$uniqueKey]['property_id'] = $property['property_id'];
			$groupedProperties[$uniqueKey]['email'] = $property['email'];

			if ($property['com_id'] > 0) {
				$company = $this->ci->Company_model->get_company($property['com_id'], true);
				$groupedProperties[$uniqueKey]['detail'] = $company;
				$groupedProperties[$uniqueKey]['type'] = EntityTypes::COMPANY;
			} else if ($property['con_id'] > 0) {
				$contact = $this->ci->Contact_model->get_contact($property['con_id'], true);
				$groupedProperties[$uniqueKey]['detail'] = $contact;
				$groupedProperties[$uniqueKey]['type'] = EntityTypes::CONTACT;
			} else {
				$groupedProperties[$uniqueKey]['type'] = "undefined owner";
				$groupedProperties[$uniqueKey]['detail'] = array('phones' => array(), 'name' => '');
			}
		}

		return $groupedProperties;
	}

	private function formatForSaleResult($properties)
	{
		$groupedProperties = [];

		foreach ($properties as $key => $property) {
			$groupedProperties[$key]['properties'][] = $property;
			$groupedProperties[$key]['property_id'] = $property['property_id'];
			$groupedProperties[$key]['email'] = $property['email'];

			if ($property['com_id'] > 0) {
				$company = $this->ci->Company_model->get_company($property['com_id'], true);
				$groupedProperties[$key]['detail'] = $company;
				$groupedProperties[$key]['type'] = EntityTypes::COMPANY;
			} else if ($property['con_id'] > 0) {
				$contact = $this->ci->Contact_model->get_contact($property['con_id'], true);
				$groupedProperties[$key]['detail'] = $contact;
				$groupedProperties[$key]['type'] = EntityTypes::CONTACT;
			} else {
				$groupedProperties[$key]['type'] = "undefined owner";
				$groupedProperties[$key]['detail'] = array('phones' => array(), 'name' => '');
			}
		}

		return $groupedProperties;
	}

}
