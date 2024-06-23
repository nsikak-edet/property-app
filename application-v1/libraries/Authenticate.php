<?php

class Authenticate {

    private $CI;

    public function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->model("User_model");
    }

    public function isSuperAdmin() {
        if ($this->isLoggedIn() == TRUE) {
            $this->CI->load->model("user_model");
            $username = $this->CI->session->userdata("username");
            $user = $this->CI->user_model->getUserByEmail($username);
            if (($user != null) && (($user->super_admin == 1))) {
                return true;
            }
        }
        return false;
    }

    public function isLoggedIn() {
        $loggedIn = $this->CI->session->userdata('logged_in');
        $sessionToken = $this->CI->session->userdata('token');
        $username = $this->CI->session->userdata("username");
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $generateToken = $this->generateToken($username, $userAgent);
        if (($loggedIn == TRUE) && ($generateToken == $sessionToken)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function generateToken($username, $userAgent) {
        return md5($username . $userAgent);
    }

    public function isAccountType($type) {
        if ($this->isLoggedIn() == TRUE) {
            $this->CI->load->model("user_model");
            $email = $this->CI->session->userdata("email");
            $user = $this->CI->user_model->getUserByUsername($email);
            if (($user != null) && ($user->account_type == $type)) {
                return true;
            }
        }
        return false;
    }

    public function isReviewer() {
        if ($this->isLoggedIn() == TRUE) {
            $this->CI->load->model("user_model");
            $email = $this->CI->session->userdata("email");
            $user = $this->CI->user_model->getUserByEmail($email);
            if (($user != null) && ($user->account_type_id == 2)) {
                return true;
            }
        }
        return false;
    }

    public function isAdmin() {
        if ($this->isLoggedIn() == TRUE) {
            $this->CI->load->model("user_model");
            $email = $this->CI->session->userdata("email");
            $user = $this->CI->user_model->getUserByEmail($email);
            if (($user != null) && ($user->account_type_id == 1)) {
                return true;
            }
        }
        return false;
    }

    public function isSubscriber() {
        if ($this->isLoggedIn() == TRUE) {
            $this->CI->load->model("user_model");
            $email = $this->CI->session->userdata("email");
            $user = $this->CI->user_model->getUserByEmail($email);
            if (($user != null) && ($user->account_type_id == 3)) {
                return true;
            }
        }
        return false;
    }

    public function isDataOwner($accountDetailId) {
        if ($this->isLoggedIn() == TRUE) {
            $this->CI->load->model("user_model");
            $user = $this->CI->session->userdata("user");
            $account = $this->CI->user_model->getAccountDetailsByUserId($user->user_id);
            if (($account != null) && ($account->account_detail_id == $accountDetailId)) {
                return true;
            }
        }
        return false;
    }

    public function isSubscriberUser() {
        if ($this->isLoggedIn() == TRUE) {
            $this->CI->load->model("user_model");
            $email = $this->CI->session->userdata("email");
            $user = $this->CI->user_model->getUserByEmail($email);
            if (($user != null) && ($user->account_type_id == 4)) {
                return true;
            }
        }
        return false;
    }

    public function isMainSubscriber() {
        if ($this->isLoggedIn() == TRUE) {
            $this->CI->load->model("user_model");
            $email = $this->CI->session->userdata("email");
            $user = $this->CI->user_model->getUserByEmail($email);
            if (($user != null) && ($user->account_type_id == 3) && ($user->admin == 1)) {
                return true;
            }
        }
        return false;
    }

    public function isSubscriberEditor() {
        if ($this->isLoggedIn() == TRUE) {
            $this->CI->load->model("user_model");
            $email = $this->CI->session->userdata("email");
            $user = $this->CI->user_model->getUserByEmail($email);
            if (($user != null) && ($user->account_type_id == 4) && ($user->edit_data == 1)) {
                return true;
            }
        }
        return false;
    }

    public function hasActivePlan() {
        if ($this->isMainSubscriber()) {
            $this->CI->load->model("User_model");
            $this->CI->load->model("Plan_model");
            $email = $this->CI->session->userdata("email");
            $user = $this->CI->User_model->getUserByEmail($email);
            $accountInfo = $this->CI->User_model->getAccountDetailsByUserId($user->user_id);
            $plans = $this->CI->Plan_model->getActivePlan($accountInfo->account_detail_id);
            if (($accountInfo != null) && (sizeof($plans) > 0)) {
                return true;
            }
        }
        return false;
    }

    public function userHasActivePlan($userId) {
        if ($this->isMainSubscriber()) {
            $this->CI->load->model("User_model");
            $this->CI->load->model("Plan_model");
            $accountInfo = $this->CI->User_model->getAccountDetailsByUserId($userId);
            $plans = $this->CI->Plan_model->getActivePlan($accountInfo->account_detail_id);
            if (($accountInfo != null) && (sizeof($plans) > 0)) {
                return true;
            }
        }

        return false;
    }

    public function hasConsultantOrCombinedPlan($tier) {
        $subscriber = $this->isMainSubscriber();
        $editor = $this->isSubscriberEditor();
        $subscriberUser = $this->isSubscriberUser();
        if ($subscriber || $editor || $subscriberUser) {
            $this->CI->load->model("User_model");
            $this->CI->load->model("Plan_model");
            $email = $this->CI->session->userdata("email");
            $user = $this->CI->User_model->getUserByEmail($email);
            if ($subscriber) {
                $accountInfo = $this->CI->User_model->getAccountDetailsByUserId($user->user_id);
            } else {
                $accountInfo = $this->CI->User_model->getAccountDetailsById($user->account_id);
            }

            $plans = $this->CI->Plan_model->getActivePlans($accountInfo->account_detail_id);
            foreach ($plans as $plan) {
                if (($accountInfo != null) && ($plan->type == PlanTypes::SUBCONSULTANT) && (($plan->tier == $tier) || ($plan->tier > $tier))) {
                    return true;
                } else if (($accountInfo != null) && ($plan->type == PlanTypes::COMBINED) && ($plan->tier == $tier) || ($plan->tier > $tier)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getAccount() {
        $user = $this->CI->session->userdata('user');
        if ($this->CI->authenticate->isSubscriberUser()) {
            $accountInfo = $this->CI->User_model->getAccountDetailsById($user->account_id);
        } else if ($this->CI->authenticate->isMainSubscriber()) {
            $accountInfo = $this->CI->User_model->getAccountDetailsByUserId($user->user_id);
            $accountInfo->account_id = $accountInfo->account_detail_id;
        } else {
            $accountInfo = $this->CI->User_model->getAccountDetailsByUserId($user->user_id);
        }
        return $accountInfo;
    }

    public function hasCombinedOrPrimePlan() {
        $subscriber = $this->isMainSubscriber();
        $editor = $this->isSubscriberEditor();
        $subscriberUser = $this->isSubscriberUser();
        if ($subscriber || $editor || $subscriberUser) {
            $this->CI->load->model("User_model");
            $this->CI->load->model("Plan_model");
            $email = $this->CI->session->userdata("email");
            $user = $this->CI->User_model->getUserByEmail($email);
            if ($subscriber) {
                $accountInfo = $this->CI->User_model->getAccountDetailsByUserId($user->user_id);
            } else {
                $accountInfo = $this->CI->User_model->getAccountDetailsById($user->account_id);
            }

            $plans = $this->CI->Plan_model->getActivePlans($accountInfo->account_detail_id);
            foreach ($plans as $plan) {
                if (($accountInfo != null) && ($plan->type == PlanTypes::PRIME)) {
                    return true;
                } else if (($accountInfo != null) && ($plan->type == PlanTypes::COMBINED)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function hasFreePlanNoExpiration() {
        $subscriber = $this->isMainSubscriber();
        $editor = $this->isSubscriberEditor();
        $subscriberUser = $this->isSubscriberUser();
        if ($subscriber || $editor || $subscriberUser) {
            $this->CI->load->model("User_model");
            $this->CI->load->model("Plan_model");
            $email = $this->CI->session->userdata("email");
            $user = $this->CI->User_model->getUserByEmail($email);
            if ($subscriber) {
                $accountInfo = $this->CI->User_model->getAccountDetailsByUserId($user->user_id);
            } else {
                $accountInfo = $this->CI->User_model->getAccountDetailsById($user->account_id);
            }
            $plans = $this->CI->Plan_model->getActiveFreePlan($accountInfo->account_detail_id);
            if (($accountInfo != null) && (sizeof($plans) > 0)) {
                $activePlan = $plans[0];
                if ($activePlan->end_date != null) {
                    return false;
                }
                return true;
            }
        }
        return false;
    }

    public function showPlanExpirationWarning() {
        $subscriber = $this->isMainSubscriber();
        if ($subscriber) {
            $this->CI->load->model("User_model");
            $this->CI->load->model("Plan_model");
            $email = $this->CI->session->userdata("email");
            $user = $this->CI->User_model->getUserByEmail($email);
            $accountInfo = $this->CI->User_model->getAccountDetailsByUserId($user->user_id);
            $plans = $this->CI->Plan_model->getActiveFreePlan($accountInfo->account_detail_id);
            if (($accountInfo != null) && (sizeof($plans) > 0)) {
                $activePlan = $plans[0];
                if ($activePlan->end_date != null) {
                    $startDate = date('Y-m-d');
                    $endDate = $activePlan->end_date;
                    $daysLeft = getDifferenceBtwDates($startDate, $endDate);
                    if ($daysLeft <= 30) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /*     * *
     * Checks for combined, prime and subconsultant subscription
     * @param $types
     * @param $tiers
     * @param int $userId
     * @return bool
     */

    public function canViewData($types, $tiers, $userId = 0, $viewMode=false) {
      
        $subscriber = $this->isMainSubscriber();
        $this->CI->load->model("User_model");
        $this->CI->load->model("Plan_model");
        $email = $this->CI->session->userdata("email");
        $user = $this->CI->User_model->getUserByEmail($email);
        if ($subscriber) {
            $accountInfo = $this->CI->User_model->getAccountDetailsByUserId($user->user_id);
        } else {
            $accountInfo = $this->CI->User_model->getAccountDetailsById($user->account_id);
        }

        //check if user account has expired/is inactive
        if (($userId > 0) && ($viewMode == false)) {
            if ($this->userHasActivePlan($userId) == false) {
                return false;
            }
        }

        $plans = $this->CI->Plan_model->getActivePlans($accountInfo->account_detail_id);
        foreach ($plans as $plan) {
            //check if is profile owner
            if (($accountInfo != null) && ($plan->type == PlanTypes::SUBCONSULTANT) && (in_array($plan->tier, $tiers)) && ($userId == $accountInfo->user_id)) {
                return true;
            } else if (($accountInfo != null) && (sizeof($plans) > 0) && (in_array($plan->type, $types)) && (in_array($plan->tier, $tiers))) {
                return true;
            }
        }

        return false;
    }

    public function loadDashboard() {
        if ($this->isLoggedIn())
            redirect(base_url() . "product/");
    }

}
