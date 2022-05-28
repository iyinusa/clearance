<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends CI_Controller {

	function __construct() {
        parent::__construct();
    }
	
	public function index() {
		// redirect if coming from native login
		$s_data = array ('ebs_redirect' => uri_string());
		$this->session->set_userdata($s_data);
		if($this->session->userdata('logged_in') == FALSE){
			redirect(base_url('login'), 'refresh');
		} else {
			$ebs_user_id = $this->session->userdata('ebs_id');
			$ebs_user_role = $this->session->userdata('ebs_user_role');
			$permit = array('Admin');
			if(!in_array($ebs_user_role, $permit)){
				redirect(base_url('profile'), 'refresh');	
			}	
		}
		
		// load records
		$list = '';
		$allrec = $this->Crud->read('eb_user');
		if(!empty($allrec)){
			foreach($allrec as $rec){
				$id = $rec->id;
				$dept_id = $rec->dept_id;
				$email = $rec->email;
				$phone = $rec->phone;
				$othername = $rec->othername;
				$lastname = $rec->lastname;
				$role = $rec->role;

				$dept = $this->Crud->read_field('id', $dept_id, 'eb_department', 'name');
				if($dept){$dept = '<br/><small class="text-muted">'.$dept.'</small>';}

				$sch = '';
				if($role == 'Dean') {
					$sch = $this->Crud->read_field('id', $rec->sch_id, 'eb_school', 'name');
					if($sch){$sch = '<br/><small class="text-muted">'.$sch.'</small>';}
				}
				
				$edit_link = '<li><a href="javascript:;" class="pop text-primary" pageTitle="Edit '.$othername.' '.$lastname.'" pageName="'.base_url('accounts/form/add/'.$id).'"><i class="si si-pencil"></i> Manage Record</a></li>';
				
				$del_link = '<li><a href="javascript:;" class="pop text-danger" pageTitle="Delete '.$othername.' '.$lastname.'" pageName="'.base_url('accounts/form/del/'.$id).'"><i class="si si-trash"></i> Delete Record</a></li>';
				
				if($role != 'Admin' && $role != 'Student') {
					$list .= '
						<tr>
							<td><b>'.$othername.' '.$lastname.' '.$sch.' '.$dept.'</b></td>
							<td>'.$phone.'</td>
							<td>'.$email.'</td>
							<td>'.$role.'</td>
							<td>
								<div class="btn-group">
									<a class="btn btn-danger btn-xs btn-rounded dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="si si-settings"></i>&nbsp;<span class="caret"></span></a>
									<ul class="dropdown-menu" role="menu">
										'.$edit_link.'
										'.$del_link.'
									</ul>
								</div>
							</td>
						</tr>
					';
				}
			}
		}
		$data['list'] = $list;
		
		$data['title'] = app_name.' | Accounts';
		$data['page_active'] = 'account';
		
		$this->load->view('designs/header', $data);
		$this->load->view('setup/account', $data);
		$this->load->view('designs/footer', $data);
	}
	
	public function form($param1='',$param2='') {
		$data['param1'] = $param1;
		$data['param2'] = $param2;
		$data['allschool'] = $this->Crud->read_order('eb_school', 'name', 'asc');
		$data['alldept'] = $this->Crud->read_order('eb_department', 'name', 'asc');
		$err_msg = '';
		
		//////////////// INSERT/EDIT RECORDS /////////////
		if($param1 == 'add' || $param1 == ''){
			if($param2 != '') {
				$getrec = $this->Crud->read_single('id', $param2, 'eb_user');
				if(!empty($getrec)){
					foreach($getrec as $rec){
						$data['e_id'] = $rec->id;
						$data['e_dept_id'] = $rec->dept_id;
						$data['e_sch_id'] = $rec->sch_id;
						$data['e_phone'] = $rec->phone;
						$data['e_email'] = $rec->email;
						$data['e_othername'] = $rec->othername;
						$data['e_lastname'] = $rec->lastname;
						$data['e_role'] = $rec->role;
					}
				}
			}
			
			if($_POST){
				$account_id = $_POST['account_id'];
				$sch_id = $_POST['sch_id'];
				$dept_id = $_POST['dept_id'];
				$othername = $_POST['othername'];
				$lastname = $_POST['lastname'];
				$phone = $_POST['phone'];
				$email = $_POST['email'];
				$password = $_POST['password'];
				$role = $_POST['role'];
				
				if($account_id != ''){
					// if password not empty, update password
					if($password != '') {
						$upd_user_pass = array('password'=>md5($password));
						if($this->Crud->update('id', $student_id, 'eb_user', $upd_user_pass) > 0) {
							$err_msg = $this->Crud->msg('success', 'Password Changed');
						}
					}
					
					$upd_data = array(
						'username' => $email,
						'sch_id' => $sch_id,
						'dept_id' => $dept_id,
						'othername' => $othername,
						'lastname' => $lastname,
						'phone' => $phone,
						'email' => $email,
						'role' => $role
					);
					$upd_id = $this->Crud->update('id', $account_id, 'eb_user', $upd_data);
					if($upd_id){
						$err_msg = $this->Crud->msg('success', 'Record Updated');
						
						echo '<script>window.location.replace("'.base_url('accounts').'");</script>';	
					} else {
						$err_msg .= $this->Crud->msg('info', 'No Record Changes');
					}
				} else {
					if($this->Crud->check('username', $email, 'eb_user') > 0){
						$err_msg = $this->Crud->msg('danger', 'User already created');
					} else {
						// check if someone has email registered
						if($this->Crud->check('email', $email, 'eb_user') > 0) {
							$err_msg = $this->Crud->msg('danger', 'Email already attached to another account');
						} else {
							// now register user
							$password = md5($password);
							$country_id = $this->Crud->read_field('name', 'Nigeria', 'eb_country', 'id');
							if($country_id == '') {$country_id = 1;}
							
							$ins_user = array(
								'username' => $email,
								'sch_id' => $sch_id,
								'dept_id' => $dept_id,
								'password' => $password,
								'othername' => $othername,
								'lastname' => $lastname,
								'phone' => $phone,
								'email' => $email,
								'country' => $country_id,
								'role' => $role,
								'activate' => 1,
								'reg_date' => date(fdate),
							);
							$user_id = $this->Crud->create('eb_user', $ins_user);
							if($user_id > 0) {
								$err_msg = $this->Crud->msg('success', 'Record Created');
								echo '<script>window.location.replace("'.base_url('accounts').'");</script>';	
							} else {
								$err_msg = $this->Crud->msg('warning', 'Please try later');
							}
						}
					}
				}
				
				echo $err_msg;
				exit;
			}
		//////////////// DELETE RECORDS /////////////
		} else if($param1 == 'del'){ 
			if($param2 != '') {
				$getrec = $this->Crud->read_single('id', $param2, 'eb_user');
				if(!empty($getrec)){
					foreach($getrec as $rec){
						$data['d_id'] = $rec->id;
					}
				}
			}
			
			if($_POST){
				$d_account_id = $_POST['d_account_id'];
				if($_POST){
					if($this->Crud->delete('id', $d_account_id, 'eb_user') > 0) {
						$err_msg = $this->Crud->msg('success', 'Record Deleted');
						echo '<script>window.location.replace("'.base_url('accounts').'");</script>';
					}
				}
				
				echo $err_msg;
				exit;
			}
		}
		
		$this->load->view('setup/account_form', $data);
	}
}
