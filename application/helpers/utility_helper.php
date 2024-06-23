<?php

require_once APPPATH . "third_party/currencyformatter/vendor/autoload.php";

/**
 * Created by PhpStorm.
 * User: NSSOLVE
 * Date: 3/6/2018
 * Time: 6:39 AM
 */
function tofloat($num)
{
    $dotPos = strrpos($num, '.');
    $commaPos = strrpos($num, ',');
    $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
        ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);
    if (!$sep) {
        return floatval(preg_replace("/[^0-9]/", "", $num));
    }
    return floatval(
        preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
        preg_replace("/[^0-9]/", "", substr($num, $sep + 1, strlen($num)))
    );
}

function getPPRLink($value){
	$link = "https://www.narrpr.com/find.aspx?apMode=Commercial&Action=PropertyDetails&DetailsTab=1&Query=" . urlencode($value);
	return anchor($link,'RPR Link', array('target'=>'_blank'));
}

function toFormat($amount)
{
    $money = new Gerardojbaez\Money\Money($amount, 'USD');
    $amount = $money->format();
    return $amount;
}

function calculatePercentage($num, $percentage = 21)
{
    if ($num > 0) {
        return round(($percentage / 100) * $num, 2);
    }
    return 0;
}

function itemInOrderList($orderItems, $item)
{
    foreach ($orderItems as $orderItem) {
        if ($item->item_id == $orderItem->item_id) {
            return true;
        }
    }
    return false;
}

function getItem($orderItems, $item, $key = null)
{
    foreach ($orderItems as $orderItem) {
        if ($item->item_id == $orderItem->item_id) {
            if ($key != null) {
                return $orderItem->$key;
            }
            return $orderItem;
        }
    }
}

function formatOpenCloseStatus($status)
{
    if ($status == 0) {
        return "<span class='text-success'>Open</span>";
    } else {
        return "<span class='text-danger'>Closed</span>";
    }
}

function formatDate($date)
{
    return (strlen($date) > 0) ? date('m/d/Y', strtotime($date)) : '';
}

function formatTime($time)
{
    if ($time == null) {
        return '00:00';
    }
    return date('H:i', strtotime($time));
}

function to2Decimal($number)
{
    return number_format((float)$number, 2);
}

function isOvertime($timeData, $currentTotalTime)
{
    $sumTotalHours = 0;
    foreach ($timeData as $jobCard) {
        $sumTotalHours += $jobCard->total_time;
    }
    $newTotalTime = $currentTotalTime + $sumTotalHours;
    if (($sumTotalHours > TOTAL_HOURS_PER_WEEK) || ($newTotalTime > TOTAL_HOURS_PER_WEEK)) {
        return true;
    }
    return false;
}

function calculateTotalHours($timeData)
{
    $sumTotalHours = 0;
    foreach ($timeData as $jobCard) {
        $sumTotalHours += $jobCard->total_time;
    }
    return $sumTotalHours;
}

function getHundrethMinutes($hourMinutes)
{
    $hour = floor($hourMinutes);
    $minutes = round(((($hourMinutes - $hour) / 0.01) / 60), 2);
    $time = $hour + $minutes;
    return $time;
}

function getWeekStartEndDays($date)
{
    $custom_date = strtotime(date('d-m-Y', strtotime($date)));
    $week_start = date('d-m-Y', strtotime('monday this week', $custom_date));
    $week_end = date('d-m-Y', strtotime('sunday this week', $custom_date));
    $data['start_day'] = $week_start;
    $data['end_day'] = $week_end;
    $data['date'] = $date;
    return $data;
}

function getFullUploadPath($filename, $subFolder)
{
    return base_url("uploads/$subFolder/" . $filename);
}

function getFileDownloadLink($filename, $subFolder, $linkName=null)
{
    $link = base_url("uploads/$subFolder/" . $filename);
    $displayName = ($linkName != null) ? $linkName : 'File';
    if (is_file(FCPATH . "uploads/$subFolder/" . $filename)) {
        return "<a href='$link' target='_blank' title='$filename'><i class='ion ion-ios-download'></i> $displayName</a>";
    }
    return "<a href='#' >No File</a>";

}

function getFileDetails($fileObject, $subFolder, $filename){
    if(is_object($fileObject)){
        if(strlen($fileObject->review_file) > 0){
            return getFileDownloadLink($fileObject->review_file, $subFolder, $fileObject->review_orig_filename);
        }else{
            switch ($subFolder){
                case 'logos':
                    $filename = $fileObject->logo;
                    break;
                case 'certifications':
                    $filename = $fileObject->proof_of_certification;
                    break;
                case 'firm_profiles':
                    $filename = $fileObject->file;
                    break;
                case 'resumes':
                    $filename = $fileObject->resume;
                    break;
                case 'projects':
                    $filename = $fileObject->project;
                    break;
            }
            return getFileDownloadLink($filename, $subFolder, $fileObject->orig_filename);
        }
    }
}

function getStatusBadge($status){
    if($status == Statuses::APPROVED){
        return "<span class='badge badge-success'>$status</span>";
    }else{
        return "<span class='badge badge-secondary'>$status</span>";
    }
}



function sendEmail($subject, $email, $recipient, $message)
{
    $CI =& get_instance();
    $fromEmail = "";
    $to_email = $recipient;

    $config = Array(
        'protocol' => 'mail',
        'smtp_host' => '',
        'smtp_port' => 465,
        'smtp_user' => '',
        'smtp_pass' => '',
        'mailtype' => 'html',
        'starttls' => true,
        'newline' => "\r\n"
    );

    //Load email library
    $CI->load->library('email', $config);
    $CI->email->from($fromEmail, $subject);
    $CI->email->to($to_email);
    $CI->email->subject($subject);
    $CI->email->message($message);
    //Send mail
    if ($CI->email->send()) {
        return true;
    } else {
//        echo $CI->email->print_debugger();
        return false;
    }
}

function formatPhoneNumber($phone){
 return preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $phone);
}

function filterApprovedRecords(&$records)
{
    foreach ($records as $key => $record) {
        if(($record->status != Statuses::APPROVED) && (isset($record->deleted) && $record->deleted == 0)) {
            unset($records[$key]);
        }
    }

    return $records;
}

function filterNonDeletedRecords(&$records)
{
    foreach ($records as $key => &$record) {
        if ((isset($record->deleted) && ($record->deleted == 1))) {
            unset($records[$key]);
        }
    }

    return $records;
}


function limitRecords($records, $limit=20)
{
    $counter = 1;
    foreach ($records as $key => &$record) {
        if($counter > $limit) {
            unset($records[$key]);
        }

        $counter++;
    }

    return $records;
}

function activeBadge($status)
{
    if ($status == 1) {
        return "<span class='badge badge-success'>Active</span>";
    } else {
        return "<span class='badge badge-secondary'>Inactive</span>";
    }
}

function isBot()
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'secret' => "6Ldv4SAUAAAAAOi-86_wyvywp3Doqsu4aqxH48wG",
        'response' => $_POST['g-recaptcha-response'],
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ]);
    $resp = json_decode(curl_exec($ch));
    curl_close($ch);
    if ($resp->success) {
        return false;
    } else {
        return true;
    }
}

function getPlanPrice($plans, $type, $tier, $duration)
{
    foreach ($plans as $plan) {
        if (($plan->type == $type) && ($plan->tier == $tier)) {
            switch ($duration) {
                case "monthly_price":
                    return $plan->monthly_price;
                    break;
                case "six_monthly_price":
                    return $plan->six_monthly_price;
                    break;
                case "year_monthly_price":
                    return $plan->year_monthly_price;
                    break;
                default:
                    return 0;
                    break;
            }

        }
    }
}

function getStripePlanPriceId($plans, $type, $tier, $duration)
{
    foreach ($plans as $plan) {
        if (($plan->type == $type) && ($plan->tier == $tier)) {
            switch ($duration) {
                case "monthly_price":
                    return $plan->stripe_monthly_price_id;
                    break;
                case "six_monthly_price":
                    return $plan->stripe_six_monthly_price_id;
                    break;
                case "year_monthly_price":
                    return $plan->stripe_year_monthly_price_id;
                    break;
                default:
                    return 0;
                    break;
            }

        }
    }
}


function getDurationTitle($duration)
{
    switch ($duration) {
        case "monthly_price":
            return "Monthly";
            break;
        case "six_monthly_price":
            return "6 Months";
            break;
        case "year_monthly_price":
            return "Annual (12 Months)";
            break;
        default:
            return "";
            break;
    }
}

function getDurationValue($duration)
{
    switch ($duration) {
        case "monthly_price":
            return 1;
            break;
        case "six_monthly_price":
            return 6;
            break;
        case "year_monthly_price":
            return 12;
            break;
        default:
            return 1;
            break;
    }
}

function getDurationByNumericValue($duration)
{
    switch ($duration) {
        case 1:
            return "monthly_price";
            break;
        case 6:
            return "six_monthly_price";
            break;
        case 12:
            return "year_monthly_price";
            break;
        default:
            return "monthly_price";
            break;
    }
}


function getSubscriptionEndDate($duration, $startDate)
{
    switch ($duration) {
        case "monthly_price":
            return date('Y-m-d H:i:s', strtotime($startDate . "+1 month"));
            break;
        case "six_monthly_price":
            return date('Y-m-d H:i:s', strtotime($startDate . "+6 months"));
            break;
        case "year_monthly_price":
            return date('Y-m-d H:i:s', strtotime($startDate . "+12 months"));
            break;
        default:
            return "";
            break;
    }
}

function getPlan($plans, $type, $tier)
{
    foreach ($plans as $plan) {
        if (($plan->type == $type) && ($plan->tier == $tier)) {
            return $plan;
        }
    }
}

function getCertificationTypes() {
    $certificationTypes = [
        'MBE',
        'DBE',
        'DVBE',
        'WBE',
        'LGBTBE',
        'Small Business 8(a)',
        'SBE (Other)'
    ];

    return $certificationTypes;
}

function openSideNav($type){
    $ci =& get_instance();
    $segment = $ci->uri->segment(1);
    $segment2 = $ci->uri->segment(2);
    if(($segment == 'search' || $segment == 'customlist') && ($type=='search')){
        return "open";
    }else if(($segment == 'duplicate')){
        return "open";
    }
}

function getProfileNav(){
    $ci =& get_instance();
    $menu = $ci->load->view("account/quickmenu", [], true);
    return $menu;
}

function isProfileNav(){
    return true;
}

function getCurrentUrl(){
    $ci =& get_instance();
    $segment = $ci->uri->segment(1);
    $segment2 = $ci->uri->segment(2);

    $query = ($ci->input->get('user_id') != null) ? "?user_id=" . $ci->input->get('user_id') : "" ;
    if(($segment == 'account') && !is_string($segment2) ){
        return base_url(uri_string()) . $query;
    }else{
        return base_url('account/');
    }
}

function getSiteSettings(){
    $ci =& get_instance();
    $ci->load->model('Settings_model');
    $config = $ci->Settings_model->get_settings();
    return $config;
}

/***
 * Functions calcultes difference between two dates in days
 * @param $startDate
 * @param $endDate
 * @return float
 */
function getDifferenceBtwDates($startDate, $endDate){
    $diff = abs(strtotime($endDate) - strtotime($startDate));
    $years = floor($diff / (365*60*50*24));
    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
    return $days;
}







