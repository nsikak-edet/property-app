<?php

class User_model extends CI_Model
{

    private $userTable = "users";
    private $accountTable = "account_details";
    private $accountTypeTable = "account_types";

    public function __construct(){
        parent::__construct();
        $this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
    }

    public function save($data){
        $this->db->insert($this->userTable,$data);
    }

    public function saveAccount($data){
        $this->db->insert($this->accountTable, $data);
        return $this->db->insert_id();
    }

    public function saveNewAccount($accountInfo){
        $newAccountInfo = array(
            'primary_phone' => $accountInfo['primary_phone'],
            'company_name' => $accountInfo['company_name'],
            'tax_id_number' => $accountInfo['tax_id_number'],
            'created_at' => date('Y-m-d H:i:s')
        );

        $userData = array(
            'email' => $accountInfo['email'],
            'first_name' => $accountInfo['first_name'],
            'last_name' => $accountInfo['last_name'],
            'password' => $accountInfo['password'],
            'username' => '',
            'account_type_id' => 3,
            'confirm_token' => $accountInfo['confirm_token'],
            'super_admin' => 0,
            'admin' => 1,
            'activated' => 0,
            'created_at' => date('Y-m-d h:i:s'),
        );

        $this->db->trans_start();
        $this->save($userData);
        $userId = $this->db->insert_id();

        $newAccountInfo['user_id'] = $userId;
        $accountId = $this->saveAccount($newAccountInfo);
        $this->db->trans_complete();

        return $accountId;
    }

    public function update($data,$id){
        $this->db->where('user_id',$id);
        $this->db->update($this->userTable,$data);
    }

    public function updateOtherAccountDetails($data,$id){
        $this->db->where('user_id',$id);
        $this->db->update($this->accountTable,$data);
    }

    public function promoteUser($promotionRecord, $demotionRecord, $promoteId, $demoteId){
        $this->db->trans_start();
        $this->db->where('user_id', $promoteId);
        $this->db->update($this->userTable, $promotionRecord);

        $this->db->where('user_id', $demoteId);
        $this->db->update($this->userTable, $demotionRecord);

        $account = $this->getAccountDetailsByUserId($demoteId);
        $updateAccountData = array('user_id' => $promoteId);
        $this->db->where('account_detail_id', $account->account_detail_id);
        $this->db->update($this->accountTable, $updateAccountData);

        $this->db->trans_complete();
    }

    public function getUsers($criteria=array()){
        $this->db->select("*,users.user_id as user_id, account_types.name as account_type");
        $this->db->from($this->userTable);
        $this->db->join("account_types", "account_types.account_type_id=users.account_type_id");
        
        $this->db->where('deleted',0);

        foreach($criteria as $key => $value){
            if($key == "account_type_id"){
                $this->db->where('users.account_type_id', $value);
            }

            if($key == "query"){
                $this->db->group_start()
                    ->like('account_details.company_name', $value)
                    ->or_like('users.first_name', $value)
                    ->or_like('users.last_name', $value)
                    ->or_like('users.email', $value)
                    ->group_end();
            }

            if($key == "account_detail_id"){
                $this->db->group_start()
                            ->where('account_details.account_detail_id', $value)
                            ->or_where('users.account_id', $value)
                    ->group_end();
            }
        }

        $query = $this->db->get();
        $result = $query->result();

        return (empty($result)) ? array() : $result;
    }

    public function getReviewers(){
        $this->db->select("*,users.user_id as user_id, account_types.name as account_type");
        $this->db->from($this->userTable);
        $this->db->join("account_types", "account_types.account_type_id=users.account_type_id");
        $this->db->where('deleted',0);
        $this->db->where('users.account_type_id',AccountTypes::REVIEWER);

        $query = $this->db->get();
        $result = $query->result();

        return (empty($result)) ? array() : $result;
    }

    public function getUserByEmail($email){
        $this->db->select("*");
        $this->db->from($this->userTable);
        $this->db->where('email',$email);
        $this->db->where('deleted',0);
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->result();

        return (empty($result)) ? null : $result[0];
    }

    public function getUserByConfirmToken($token){
        $this->db->select("*");
        $this->db->from($this->userTable);
        $this->db->where('confirm_token',$token);
        $this->db->where('deleted',0);
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->result();

        return (empty($result)) ? null : $result[0];
    }

    public function getUserByHash($hash){
        $this->db->select("*");
        $this->db->from($this->userTable);
        $this->db->where('reset_hash',$hash);
        $this->db->where('deleted',0);
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->result();

        return (empty($result)) ? null : $result[0];
    }
   
    public function getUserByUsername($username){
        $this->db->select("*");
        $this->db->from($this->userTable);
        $this->db->where('username',$username);
        $this->db->where('deleted',0);
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->result();

        return (empty($result)) ? null : $result[0];
    }

    public function getUsersAccounts($email){
        $this->db->select("*, users.id as id");
        $this->db->from($this->userTable);
        $this->db->where('email',$email);
        $this->db->where('deleted',0);
        $query = $this->db->get();
        $result = $query->result();

        return (empty($result)) ? null : $result;
    }

    public function getAccountTypes(){
        $this->db->select("*");
        $this->db->from($this->accountTypeTable);
        $this->db->where('name !=','Subscriber');
        $query = $this->db->get();
        $result = $query->result();

        return (empty($result)) ? null : $result;
    }

    public function getAllAccountTypes(){
        $this->db->select("*");
        $this->db->from($this->accountTypeTable);
        $query = $this->db->get();
        $result = $query->result();

        return (empty($result)) ? null : $result;
    }

    public function getUserByUserId($id){
        $this->db->select("*");
        $this->db->from($this->userTable);
        $this->db->where('user_id',$id);
        $this->db->where('deleted',0);
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->result();

        return (empty($result)) ? null : $result[0];
    }


    public function getAllUsers(){
        $this->db->select("*, users.id as id, groups.name as group_name, user_access_types.type as account_type");
        $this->db->from($this->userTable);
        $this->db->join($this->typeTable, "users.access_type_id=user_access_types.type_id", "left");
        $this->db->join("groups", "users.group_id=groups.group_id","left");

        $this->db->where('deleted',0);
        $this->db->order_by('users.username');
        $query = $this->db->get();
        $result = $query->result();

        return (empty($result)) ? null : $result;
    }

    public function getManager($userId){
        $this->db->select("*, users.id as id, groups.name as group_name, user_access_types.type as account_type");
        $this->db->from($this->userTable);
        $this->db->join($this->typeTable, "users.access_type_id=user_access_types.type_id", "left");
        $this->db->join("groups", "users.group_id=groups.group_id","left");
        $this->db->where_not_in('user_access_types.type', array('Regular'));

        $this->db->where('deleted',0);
        $this->db->where('users.id',$userId);
        $this->db->limit(1);

        $query = $this->db->get();
        $result = $query->result();

        if(sizeof($result) > 0){
            return $result[0];
        }

        return null;
    }

    public function getAccessTypes(){
        $this->db->select("*");
        $this->db->from($this->typeTable);
        $query = $this->db->get();
        $result = $query->result();

        return (empty($result)) ? null : $result;
    }




}


