<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("user_model");
        $this->load->library("form_validation");
        $this->load->library("bcrypt");
    }

    public function index()
    {
        if ($this->authenticate->isLoggedIn() == false) {
            $data['pageTitle'] = "Admin Login";
            $data['body'] = $this->load->view('user/adminlogin', $data, true);
            $this->load->view('adminauthtemplate', $data);

        } else if ($this->authenticate->isSuperAdmin() || $this->authenticate->isAdmin()) {
            redirect(base_url("user/all"));
        } else {
            redirect(base_url("account/"));
        }
    }

    public function remove($userId)
    {
        if (($this->authenticate->isSuperAdmin()) || ($this->authenticate->isAdmin())
            || $this->authenticate->isMainSubscriber() ) {
            $user = $this->user_model->getUserByUserId($userId);
            if (($user != null) && ($user->super_admin == 1)) {
                $this->session->set_flashdata("info", "you can't delete super admin account!");
                redirect(base_url("user/all"));
            }else if(($user != null) && (($user->account_type_id == 3) && (($this->authenticate->isSuperAdmin() == false) || ($this->authenticate->isAdmin() == false)))){
                $this->session->set_flashdata("info", "you can't delete main account!");
                redirect(base_url("user/all"));
            }

            $userData = array(
                'deleted' => 1,
            );
            $this->user_model->update($userData, $userId);
            $this->session->set_flashdata("success", "user deleted!");
            redirect(base_url("user/all"));
        } else {
            redirect(base_url("user/"));
        }
    }

    public function create_admin_user()
    {
        if ($this->authenticate->isSuperAdmin() == false) {
            redirect(base_url());
        }
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->form_validation->set_rules("email", "Email", "required|callback_unique_email");
            $this->form_validation->set_rules("first_name", "First Name", "required");
            $this->form_validation->set_rules("last_name", "First Name", "required");
            $this->form_validation->set_rules('account_type_id', "Account Type", 'required');
            $this->form_validation->set_rules("password", "Password", "required|min_length[6]");
            $this->form_validation->set_rules("confirm_password", "Confirm Password", "required|matches[password]");
            if ($this->form_validation->run() == true) {
                $passwordFromUser = $this->input->post('password');
                $hashedPassword = $this->bcrypt->hashPassword($passwordFromUser);
                $userData = array(
                    'email' => $this->input->post('email', true),
                    'first_name' => $this->input->post('first_name', true),
                    'last_name' => $this->input->post('last_name', true),
                    'password' => $hashedPassword,
                    'account_type_id' => $this->input->post('account_type_id', true),
                    'super_admin' => 0,
                    'admin' => 1,
                    'activated' => 1,
                    'created_at' => date('Y-m-d h:i:s'),
                );
                $this->user_model->save($userData);
                $this->session->set_flashdata("success", "user created!");
                redirect(base_url("user/all"));
            }
        }
        $data['pageTitle'] = "Create New User";
        $data['accountTypes'] = $this->user_model->getAccountTypes();
        $data['body'] = $this->load->view('user/new', $data, true);
        $this->load->view('defaulttemplate', $data);
    }

    public function all($userTypeId = null)
    {
        $accountTypeId = $this->input->get('account_type_id', true);
        $query = $this->input->get('query', true);

        if ($this->authenticate->isSuperAdmin() || $this->authenticate->isAdmin()) {

            $searchCriteria = array();
            if(($query != null) && (strlen($query) > 0)){
                $searchCriteria['query'] = $query;
            }

           
            $users = $this->user_model->getUsers($searchCriteria);
            $data['accountTypes'] = $this->user_model->getAllAccountTypes();
            $data['pageTitle'] = "Users";
            $data['users'] = $users;
            $data['body'] = $this->load->view('user/index', $data, true);
            $this->load->view('defaulttemplate', $data);
        } else {
            redirect(base_url("user"));
        }
    }

    public function edit($userId)
    {
        if (($this->authenticate->isLoggedIn())) {
            $user = $this->user_model->getUserByUserId($userId);
            $loggedUser = $this->session->userdata('user');
            if ($user != null) {

                //check if other user trying to modify superadmin
                if(($user->super_admin == 1) && ($this->authenticate->isSuperAdmin() == false)){
                    $this->session->set_flashdata('info', "you can't edit user");
                    redirect(base_url('user/all'));
                }

                //check if user can edit password
                if ((($this->authenticate->isAdmin() == false) && ($this->authenticate->isSuperAdmin() == false)
                        && ($this->authenticate->isMainSubscriber() == false)) && ($loggedUser->user_id != $user->user_id)
                ) {
                    $this->session->set_flashdata('info', "you can't edit user");
                    redirect(base_url('user/all'));
                }

                if ($this->input->server('REQUEST_METHOD') == 'POST') {
                    $this->form_validation->set_rules("first_name", "First Name", "required");
                    $this->form_validation->set_rules("last_name", "First Name", "required");
//                    $this->form_validation->set_rules('account_type_id', "Account Type", 'required');

                    if (isset($_POST['new_password']) && strlen($_POST['new_password']) > 0) {
                        $this->form_validation->set_rules('new_password', "New Password", 'trim|required|min_length[6]');
                        $this->form_validation->set_rules('confirm_new_password', "Confirm New Password", 'trim|required|matches[new_password]');
                    }
                    $this->form_validation->set_error_delimiters('', '');

                    if ($this->form_validation->run() == true) {
                        $passwordFromUser = $this->input->post('new_password', true);
                        $hashedPassword = $this->bcrypt->hashPassword($passwordFromUser);
                        $newRecord = array(
                            'first_name' => $this->input->post('first_name', true),
                            'last_name' => $this->input->post('last_name', true),
                            'created_at' => date('Y-m-d H:i:s')
                        );
                        if ($this->authenticate->isSuperAdmin()) {
                            $newRecord['activated'] = ($this->input->post('activated', true) != null) ? 1 : 0;
                        }

                        if ($this->authenticate->isMainSubscriber()) {
                            $newRecord['edit_data'] = ($this->input->post('edit_data', true) != null) ? 1 : 0;
                        }

                        if (strlen($passwordFromUser) > 0) {
                            $newRecord['password'] = $hashedPassword;
                        }

                        $this->user_model->update($newRecord, $userId);
                        $user = $this->user_model->getUserByUserId($userId);

                        if($user->user_id == $loggedUser->user_id){
                            $this->session->set_userdata('user', $user);
                        }

                        $this->session->set_flashdata('success', "user updated");
                        redirect(base_url("user/edit/$userId"));
                    }
                }
            } else {
                $this->session->set_flashdata('info', "user not found!");
                redirect(base_url("user/all"));
            }
            $data['pageTitle'] = "Edit User";
            $data['accountTypes'] = $this->user_model->getAccountTypes();
            $data['user'] = $user;
            $data['body'] = $this->load->view('user/edit', $data, true);
            $this->load->view('defaulttemplate', $data);
        } else {
            redirect(base_url());
        }
    }
    
    public function unique_email($email)
    {
        $this->load->model('user_model');
        $user = $this->user_model->getUserByEmail($email);
        if ($user != null) {
            $this->form_validation->set_message('unique_email', 'The {field} already registered');
            return false;
        }
        return true;
    }

    public function login()
    {
        if ($this->authenticate->isLoggedIn() == true) {
            redirect(base_url("user/all"));
        }
        $this->form_validation->set_rules("username", "Username", "trim|required");
        $this->form_validation->set_rules("password", "Password", "trim|required");
        $this->form_validation->set_error_delimiters('', '');
        $email = $this->input->post('username', true);
        $password = $this->input->post('password', true);
        //process if validation fails - external login validation
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            if ($this->form_validation->run() == true) {
                $user = $this->user_model->getUserByEmail($email);
                if ($user != null) {
                    //check if user's password matches encrypted version in db
                    $db_hash = $user->password;
                    $valid_password = $this->bcrypt->checkPassword(trim($password), $db_hash);
                    if (($valid_password == true) && ($user->activated == 1)) {
                        //login user and redirect to dashboard
                        $this->createLoginSession($user);
                        redirect(base_url("user/"));
                    } else {
                        $this->session->set_flashdata("error", "invalid username or password");
                        redirect(base_url('user/login'));
                    }
                } else {
                    $this->session->set_flashdata("error", "invalid username or password");
                    redirect(base_url('user/login'));
                }
            } else {
                $this->session->set_flashdata("error", "invalid username or password");
                redirect(base_url('user/login'));
            }
        }
        $data['loginError'] = $this->session->flashdata("login_error");
        $data['pageTitle'] = "Secured Login";
        $data['body'] = $this->load->view('user/adminlogin', $data, true);
        $this->load->view('adminauthtemplate', $data);
    }

    private function createLoginSession($user)
    {
        $this->load->library('authenticate');
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $token = $this->authenticate->generateToken($user->email, $userAgent);
        $session_data = array(
            'logged_in' => true,
            'username' => $user->email,
            'email' => $user->email,
            'activated' => $user->activated,
            'userAgent' => $userAgent,
            'token' => $token,
            'user_id' => $user->user_id,
            'user' => $user,
            'created_at' => $user->created_at,
        );
        $this->session->set_userdata($session_data);
    }

    public function logout()
    {
        $this->session->sess_destroy();
        $isReviewer = $this->authenticate->isReviewer();
        if ($this->authenticate->isAdmin() || $this->authenticate->isSuperAdmin() ) {
            if($isReviewer){
                $user = $this->session->userdata('user');
                $this->Review_model->checkback_files($user->user_id);
            }
            redirect(base_url("user/"));
        } else {
            redirect(base_url("user/"));
        }

    }

}
