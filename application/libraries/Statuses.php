<?php
class Statuses{}

class EntityTypes{
    const COMPANY = "company";
    const CONTACT = "contact";
}

class LeadGenTypes{
    const MET = "met";
    const HAVENT_MET = "haven't met";
}

class LeadGenOptions{
	const MET = "met";
	const HAVENT_MET = "haven't met";
	const MET_HAVENT_MET = "met & haven't met";
	const MET_OR_HAVENT_MET = "met or haven't met";
	const NOT_MET_OR_HAVENT_MET = "not met or haven't met";
}

class RecordNavigation{
	const PREVIOUS = "previous";
	const NEXT = "next";
}

class DoNotSendOptions{
	const YES = "yes";
	const NO = "no";
}

class FilterOptions{
	const IS_EMPTY = "empty";
	const IS_NOT_EMPTY = "not-empty";
}

class OwnersFilterOptions{
	const WITH_ADDRESS = "with address";
}

class AvailabilityStatus{
	const ON_MARKET = "On Market";
	const OFF_MARKET = "Off Market";
	const UNDER_LOI = "Under LOI";
	const UNDER_CONTRACT = "Under Contract";
	const PIPELINE = "Pipeline";
}

class BuyerStatus{
	const ACTIVE = "Leads";
	const PIPELINE = "Pipeline";
}


?>
