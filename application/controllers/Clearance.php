<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Clearance extends CI_Controller {

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
		}

		// start clearance
		if($ebs_user_role == 'Student') {
			$start = $this->input->get('start');
			if($start == 'yes') {
				if($this->Crud->read_single('student_id', $ebs_user_id, 'eb_clearance') <= 0) {
					$student_name = $this->Crud->read_field('id', $ebs_user_id, 'eb_user', 'othername').' '.$this->Crud->read_field('id', $ebs_user_id, 'eb_user', 'lastname');

					$stat = 'Clearance request by '.$student_name.' on '.date('d M, Y');
					$clearance_id = $this->Crud->create('eb_clearance', array('student_id'=>$ebs_user_id, 'status'=>$stat, 'reg_date'=>date(fdate)));

					// notify HOD
					if($clearance_id) {
						$ndept_id = $this->Crud->read_field('id', $ebs_user_id, 'eb_user', 'dept_id');
						$nhod_id = $this->Crud->read_field2('dept_id', $ndept_id, 'role', 'HOD', 'eb_user', 'id');
						$this->Crud->create('eb_notify', array('clearance_id'=>$clearance_id, 'user_id'=>$nhod_id, 'details'=>$stat, 'reg_date'=>date(fdate)));
					}
				}
			}
		}

		// clear student
		$cleared = $this->input->get('clear');
		if($cleared) {
			$data['e_id'] = $cleared;

			if($_POST) {
				$clear_id = $this->input->post('clear_id');
				$status = $this->input->post('status');
				$clear_student_id = $this->Crud->read_field('id', $clear_id, 'eb_clearance', 'student_id');
				if($clear_id && $status) {
					if($ebs_user_role == 'HOD') {
						$upd_clear_data['hod_id'] = $ebs_user_id;
						$upd_clear_data['hod_status'] = $status;
						$stat = 'HOD completed clearance on '.date('d M, Y');
						$upd_clear_data['status'] = $stat;
						$this->Crud->update('id', $clear_id, 'eb_clearance', $upd_clear_data);
						$bus_id = $this->Crud->read_field('role', 'Bursary', 'eb_user', 'id');
						$this->Crud->create('eb_notify', array('clearance_id'=>$clear_id, 'user_id'=>$bus_id, 'details'=>$stat, 'reg_date'=>date(fdate)));
						$this->Crud->create('eb_notify', array('clearance_id'=>$clear_id, 'user_id'=>$clear_student_id, 'details'=>$stat, 'reg_date'=>date(fdate)));
					}

					if($ebs_user_role == 'Bursary') {
						$upd_clear_data['bursary_id'] = $ebs_user_id;
						$upd_clear_data['bursary_status'] = $status;
						$stat = 'Bursary Officer completed clearance on '.date('d M, Y');
						$upd_clear_data['status'] = $stat;
						$this->Crud->update('id', $clear_id, 'eb_clearance', $upd_clear_data);
						$lib_id = $this->Crud->read_field('role', 'Library', 'eb_user', 'id');
						$this->Crud->create('eb_notify', array('clearance_id'=>$clear_id, 'user_id'=>$lib_id, 'details'=>$stat, 'reg_date'=>date(fdate)));
						$this->Crud->create('eb_notify', array('clearance_id'=>$clear_id, 'user_id'=>$clear_student_id, 'details'=>$stat, 'reg_date'=>date(fdate)));
					}

					if($ebs_user_role == 'Library') {
						$upd_clear_data['library_id'] = $ebs_user_id;
						$upd_clear_data['library_status'] = $status;
						$stat = 'Library Officer completed clearance on '.date('d M, Y');
						$upd_clear_data['status'] = $stat;
						$this->Crud->update('id', $clear_id, 'eb_clearance', $upd_clear_data);
						$spo_id = $this->Crud->read_field('role', 'Sports', 'eb_user', 'id');
						$this->Crud->create('eb_notify', array('clearance_id'=>$clear_id, 'user_id'=>$spo_id, 'details'=>$stat, 'reg_date'=>date(fdate)));
						$this->Crud->create('eb_notify', array('clearance_id'=>$clear_id, 'user_id'=>$clear_student_id, 'details'=>$stat, 'reg_date'=>date(fdate)));
					}

					if($ebs_user_role == 'Sports') {
						$upd_clear_data['sport_id'] = $ebs_user_id;
						$upd_clear_data['sport_status'] = $status;
						$stat = 'Sports Officer completed clearance on '.date('d M, Y');
						$upd_clear_data['status'] = $stat;
						$this->Crud->update('id', $clear_id, 'eb_clearance', $upd_clear_data);
						$s_dept_id = $this->Crud->read_field('id', $clear_student_id, 'eb_user', 'dept_id');
						$s_sch_id = $this->Crud->read_field('id', $s_dept_id, 'eb_department', 'school_id');
						$dea_id = $this->Crud->read_field2('role', 'Dean', 'sch_id', $s_sch_id, 'eb_user', 'id');
						$this->Crud->create('eb_notify', array('clearance_id'=>$clear_id, 'user_id'=>$dea_id, 'details'=>$stat, 'reg_date'=>date(fdate)));
						$this->Crud->create('eb_notify', array('clearance_id'=>$clear_id, 'user_id'=>$clear_student_id, 'details'=>$stat, 'reg_date'=>date(fdate)));
					}

					if($ebs_user_role == 'Dean') {
						$upd_clear_data['dean_id'] = $ebs_user_id;
						$upd_clear_data['dean_status'] = $status;
						$stat = 'Dean completed clearance on '.date('d M, Y');
						$upd_clear_data['status'] = $stat;
						$this->Crud->update('id', $clear_id, 'eb_clearance', $upd_clear_data);
						$adm_id = $this->Crud->read_field('role', 'Admin', 'eb_user', 'id');
						$this->Crud->create('eb_notify', array('clearance_id'=>$clear_id, 'user_id'=>$adm_id, 'details'=>$stat, 'reg_date'=>date(fdate)));
						$this->Crud->create('eb_notify', array('clearance_id'=>$clear_id, 'user_id'=>$clear_student_id, 'details'=>$stat, 'reg_date'=>date(fdate)));
					}
				}

				echo $this->Crud->msg('success', 'Clearance Updated');
				echo '<script>location.reload(false);</script>';

				die;
			}
		}
		
		// load records
		$list = '';
		if($ebs_user_role == 'Student') {
			$allrec = $this->Crud->read_single('student_id', $ebs_user_id, 'eb_clearance');
			if(empty($allrec)){$data['start'] = '<a href="'.base_url('clearance?start=yes').'" class="btn btn-primary btn-sm btn-rounded">
				<i class="si si-book-open"></i> Start Clearance
			</a>';}
		} else {
			$allrec = $this->Crud->read('eb_clearance');
		}

		if(!empty($allrec)){
			foreach($allrec as $rec){
				$id = $rec->id;
				$student_id = $rec->student_id;
				$hod_id = $rec->hod_id;
				$hod_status = $rec->hod_status;
				$bursary_id = $rec->bursary_id;
				$bursary_status = $rec->bursary_status;
				$library_id = $rec->library_id;
				$library_status = $rec->library_status;
				$sport_id = $rec->sport_id;
				$sport_status = $rec->sport_status;
				$dean_id = $rec->dean_id;
				$dean_status = $rec->dean_status;
				$status = $rec->status;
				$reg_date = $rec->reg_date;

				$student = $this->Crud->read_field('id', $student_id, 'eb_user', 'othername').' '.$this->Crud->read_field('id', $student_id, 'eb_user', 'lastname');
				$prog_id = $this->Crud->read_field('id', $student_id, 'eb_user', 'prog_id');
				$session_id = $this->Crud->read_field('id', $student_id, 'eb_user', 'session_id');
				$level_id = $this->Crud->read_field('id', $student_id, 'eb_user', 'level_id');
				$dept_id = $this->Crud->read_field('id', $student_id, 'eb_user', 'dept_id');
				$matric = $this->Crud->read_field('id', $student_id, 'eb_user', 'username');
				$programme = $this->Crud->read_field('id', $prog_id, 'eb_programme', 'name');
				$session = $this->Crud->read_field('id', $session_id, 'eb_session', 'name');
				$level = $this->Crud->read_field('id', $level_id, 'eb_level', 'name');
				$dept = $this->Crud->read_field('id', $dept_id, 'eb_department', 'name');
				
				$hod = $this->Crud->read_field('id', $hod_id, 'eb_user', 'othername').' '.$this->Crud->read_field('id', $hod_id, 'eb_user', 'lastname');

				$bursary = $this->Crud->read_field('id', $bursary_id, 'eb_user', 'othername').' '.$this->Crud->read_field('id', $bursary_id, 'eb_user', 'lastname');
				$library = $this->Crud->read_field('id', $library_id, 'eb_user', 'othername').' '.$this->Crud->read_field('id', $library_id, 'eb_user', 'lastname');
				$sport = $this->Crud->read_field('id', $sport_id, 'eb_user', 'othername').' '.$this->Crud->read_field('id', $sport_id, 'eb_user', 'lastname');
				$dean = $this->Crud->read_field('id', $dean_id, 'eb_user', 'othername').' '.$this->Crud->read_field('id', $dean_id, 'eb_user', 'lastname');

				if(!$hod_status) {$hod_status = '<span class="text-danger">Pending</span>';}
				if(!$bursary_status) {$bursary_status = '<span class="text-danger">Pending</span>';}
				if(!$library_status) {$library_status = '<span class="text-danger">Pending</span>';}
				if(!$sport_status) {$sport_status = '<span class="text-danger">Pending</span>';}
				if(!$dean_status) {$dean_status = '<span class="text-danger">Pending</span>';}

				// enable list
				$listing = true; $clear = '';
				if($ebs_user_role == 'HOD') {
					if($student_id == 0) {$listing = false;}
					if($hod_id == 0) {$clear = '<li><a href="javascript:;" class="pop" pageTitle="Clear '.$student.'" pageName="'.base_url('clearance/?clear='.$id).'"><i class="si si-check"></i> Clear Student</a></li>';}
				} else if($ebs_user_role == 'Bursary') {
					if($hod_id == 0) {$listing = false;}
					if($bursary_id == 0) {$clear = '<li><a href="javascript:;" class="pop" pageTitle="Clear '.$student.'" pageName="'.base_url('clearance/?clear='.$id).'"><i class="si si-check"></i> Clear Student</a></li>';}
				} else if($ebs_user_role == 'Library') {
					if($bursary_id == 0) {$listing = false;}
					if($library_id == 0) {$clear = '<li><a href="javascript:;" class="pop" pageTitle="Clear '.$student.'" pageName="'.base_url('clearance/?clear='.$id).'"><i class="si si-check"></i> Clear Student</a></li>';}
				} else if($ebs_user_role == 'Sports') {
					if($library_id == 0) {$listing = false;}
					if($sport_id == 0) {$clear = '<li><a href="javascript:;" class="pop" pageTitle="Clear '.$student.'" pageName="'.base_url('clearance/?clear='.$id).'"><i class="si si-check"></i> Clear Student</a></li>';}
				} else if($ebs_user_role == 'Dean') {
					if($sport_id == 0) {$listing = false;}
					if($dean_id == 0) {$clear = '<li><a href="javascript:;" class="pop" pageTitle="Clear '.$student.'" pageName="'.base_url('clearance/?clear='.$id).'"><i class="si si-check"></i> Clear Student</a></li>';}
				}
				
				if($listing == true) {
					$list .= '
						<tr>
							<td>'.date('d M, Y', strtotime($reg_date)).'</td>
							<td><b>'.$student.'</b> ('.$matric.')<br/><small>'.$session.' '.$programme.'<br/>'.$level.' - '.$dept.'</small></td>
							<td>
								<b>HOD:</b> '.$hod_status.'<br/><br/>
								<b>BURSARY:</b> '.$bursary_status.'<br/><br/>
								<b>LIBRARY:</b> '.$library_status.'<br/><br/>
								<b>SPORT:</b> '.$sport_status.'<br/><br/>
								<b>DEAN:</b> '.$dean_status.'<br/>
							</td>
							<td>'.$status.'</td>
							<td>
								<div class="btn-group">
									<a class="btn btn-danger btn-xs btn-rounded dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="si si-settings"></i>&nbsp;<span class="caret"></span></a>
									<ul class="dropdown-menu dropdown-menu-right" role="menu">
										<li><a target="_blank" href="'.base_url('clearance/form/'.$id).'"><i class="si si-printer"></i> Print Clearance</a></li>
										'.$clear.'
									</ul>
								</div>
							</td>
						</tr>
					';
				}
			}
		}
		$data['list'] = $list;
		
		$data['title'] = app_name.' | Clearance';
		$data['page_active'] = 'clearance';
		
		if($cleared) {
			$this->load->view('setup/clearance_form', $data);
		} else {
			$this->load->view('designs/header', $data);
			$this->load->view('setup/clearance', $data);
			$this->load->view('designs/footer', $data);
		}
	}
	
	public function form($param='') {
		$ebs_user_id = $this->session->userdata('ebs_id');
		$ebs_user_role = $this->session->userdata('ebs_user_role');
		
		$allrec = $this->Crud->read_single('id', $param, 'eb_clearance');
		if(!empty($allrec)){
			foreach($allrec as $rec){
				$id = $rec->id;
				$student_id = $rec->student_id;
				$hod_id = $rec->hod_id;
				$hod_status = $rec->hod_status;
				$bursary_id = $rec->bursary_id;
				$bursary_status = $rec->bursary_status;
				$library_id = $rec->library_id;
				$library_status = $rec->library_status;
				$sport_id = $rec->sport_id;
				$sport_status = $rec->sport_status;
				$dean_id = $rec->dean_id;
				$dean_status = $rec->dean_status;
				$status = $rec->status;
				$reg_date = $rec->reg_date;

				$student = $this->Crud->read_field('id', $student_id, 'eb_user', 'othername').' '.$this->Crud->read_field('id', $student_id, 'eb_user', 'lastname');
				$prog_id = $this->Crud->read_field('id', $student_id, 'eb_user', 'prog_id');
				$session_id = $this->Crud->read_field('id', $student_id, 'eb_user', 'session_id');
				$level_id = $this->Crud->read_field('id', $student_id, 'eb_user', 'level_id');
				$dept_id = $this->Crud->read_field('id', $student_id, 'eb_user', 'dept_id');
				$school_id = $this->Crud->read_field('id', $dept_id, 'eb_department', 'school_id');
				$school = $this->Crud->read_field('id', $school_id, 'eb_school', 'name');
				$matric = $this->Crud->read_field('id', $student_id, 'eb_user', 'username');
				$programme = $this->Crud->read_field('id', $prog_id, 'eb_programme', 'name');
				$session = $this->Crud->read_field('id', $session_id, 'eb_session', 'name');
				$level = $this->Crud->read_field('id', $level_id, 'eb_level', 'name');
				$dept = $this->Crud->read_field('id', $dept_id, 'eb_department', 'name');
				
				$hod = $this->Crud->read_field('id', $hod_id, 'eb_user', 'othername').' '.$this->Crud->read_field('id', $hod_id, 'eb_user', 'lastname');

				$bursary = $this->Crud->read_field('id', $bursary_id, 'eb_user', 'othername').' '.$this->Crud->read_field('id', $bursary_id, 'eb_user', 'lastname');
				$library = $this->Crud->read_field('id', $library_id, 'eb_user', 'othername').' '.$this->Crud->read_field('id', $library_id, 'eb_user', 'lastname');
				$sport = $this->Crud->read_field('id', $sport_id, 'eb_user', 'othername').' '.$this->Crud->read_field('id', $sport_id, 'eb_user', 'lastname');
				$dean = $this->Crud->read_field('id', $dean_id, 'eb_user', 'othername').' '.$this->Crud->read_field('id', $dean_id, 'eb_user', 'lastname');

				if(!$hod_status) {$hod_status = 'Pending';}
				if(!$bursary_status) {$bursary_status = 'Pending';}
				if(!$library_status) {$library_status = 'Pending';}
				if(!$sport_status) {$sport_status = 'Pending';}
				if(!$dean_status) {$dean_status = 'Pending';}

			}
		}
		
		// pdf output parameters
		$pdf_logo = base_url('assets/img/laspo_logo.png');
		$pdf_content = '
			<style>
				.head {text-align:center;}
				.tb {width:auto;}
				.tb .fc {font-weight:bold; width:180px;}
				.tb .lc {border:1px solid #ddd;}
				td.border{border:1px solid #ddd;}
			</style>
			
			<div class="head">
				<table width="100%">
					<tr>
						<td align="left" width="40%">
							<img alt="LAGOS STATE POLYTECHNIC" src="'.$pdf_logo.'" height="50px" />
						</td>
						<td align="left">
							<div style="font-size:14px">
								<b>LETTER OF CLEARANCE</b><br/>
								TO BE COMPLETED BY STUDENT
							</div>
						</td>
					</tr>
				</table><hr style="border:1px solid #ff0;"/>
			</div>
			
			<div>
				<table width="50%">
					<tr>
						<td>'.$school.'</td>
					</tr>
					<tr>
						<td><b>Name of Student:</b> '.$student.'</td>
					</tr>
					<tr>
						<td><b>Matric Number:</b> '.$matric.'</td>
					</tr>
				</table>

				<h3>FOR OFFICIAL USE</h3>
				<div>
					This is to certify the above named is in the department of '.$dept.'. Has met all requirements for the award of '.$level.' for the '.$session.' Session.<br/><br/>
					He/She is therefore qualified to collect his/her completion letter and/or call-up letter after clearance by the underlisted.
				</div>
				
				<h3>ACADEMIC DEPARTMENT</h3>
				<div>
					<b>Head of Department\'s comment:</b> '.$hod_status.'<br/>
					<b>Signature and Stamp:</b> 
				</div>

				<h3>BURSARY DEPARTMENT</h3>
				<div>
					<b>Amount Owing:</b> '.$bursary_status.'<br/>
					<b>Clearing Officer\'s Name:</b> '.$bursary.'<br/>
					<b>Signature and Stamp:</b> 
				</div>

				<h3>LIBRARY DEPARTMENT</h3>
				<div>
					<b>Head of Unit\'s comment:</b> '.$library_status.'<br/>
					<b>Signature and Stamp:</b> 
				</div>

				<h3>SPORTS</h3>
				<div>
					<b>Chairman Sports and Games Committee\'s Comment:</b> '.$sport_status.'<br/>
					<b>Signature and Stamp:</b> 
				</div>

				<h3>DEAN OF SCHOOL\'s COMMENT</h3>
				<div>
					<b>Clearing Officer\'s Name:</b> '.$dean_status.'<br/>
					<b>Signature and Stamp:</b> 
				</div>
			</div>
		';
		
		$data['title'] = 'CLEARANCE FORM';
		$data['pdf_content'] = $pdf_content;
		
		$this->load->view('pdf/print', $data);
	}
}
