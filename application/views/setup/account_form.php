<?php echo form_open('accounts/form/'.$param1.'/'.$param2, array('id'=>'bb_ajax_form','class'=>'form-horizontal')); ?>
	<div id="bb_ajax_msg"></div>
    
    <?php if($param1 == 'del') { // delete view ?>
        <div class="col-xs-12 text-center">
            <h3><b>Are you sure?</b></h3>
            <input type="hidden" name="d_account_id" value="<?php if(!empty($d_id)){echo $d_id;} ?>" />
        </div>
        <div class="form-group text-center m-t-40">
            <div class="col-xs-12">
                <button class="btn btn-danger btn-rounded text-uppercase waves-effect waves-light" type="submit">
                    <span class="btn-label"><i class="ti-trash"></i></span> Yes - Delete
                </button>
            </div>
        </div>
    
    <?php } else { // insert/edit view ?>
        <input id="account_id" name="account_id" type="hidden" value="<?php if(!empty($e_id)){echo $e_id;} ?>">
        
        <div class="col-lg-12">
            <div class="form-group">
                <div class="col-sm-6">
                    <div class="form-material">
                        <?php
							$list_school = '';
							if(!empty($allschool)) {
								foreach($allschool as $sch) {
									if(!empty($e_sch_id)) {
										if($e_sch_id == $sch->id) {
											$s_sel = 'selected="selected"';
										} else {$s_sel = '';}
									} else {$s_sel = '';}
									
									$list_school .= '<option value="'.$sch->id.'" '.$s_sel.'>'.$sch->name.'</option>';	
								}
							}
						?>
                        <select class="js-select2 form-control" id="sch_id" name="sch_id" style="width: 100%;" data-placeholder="Department" required>
                            <option value="0">All</option>
                            <?php echo $list_school; ?>
                        </select>
                        <label for="sch_id">School</label>
                    </div>
                </div>
                
                <div class="col-sm-6">
                    <div class="form-material">
                        <?php
							$list_dept = '';
							if(!empty($alldept)) {
								foreach($alldept as $dept) {
									if(!empty($e_dept_id)) {
										if($e_dept_id == $dept->id) {
											$d_sel = 'selected="selected"';
										} else {$d_sel = '';}
									} else {$d_sel = '';}
									
									$list_dept .= '<option value="'.$dept->id.'" '.$d_sel.'>'.$dept->name.'</option>';	
								}
							}
						?>
                        <select class="js-select2 form-control" id="dept_id" name="dept_id" style="width: 100%;" data-placeholder="Department" required>
                            <option value="0">All</option>
                            <?php echo $list_dept; ?>
                        </select>
                        <label for="dept_id">Department</label>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-material">
                        <input class="form-control" type="text" id="othername" name="othername" value="<?php if(!empty($e_othername)){echo $e_othername;} ?>" required>
                        <label for="othername">Other Names</label>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-material">
                        <input class="form-control" type="text" id="lastname" name="lastname" value="<?php if(!empty($e_lastname)){echo $e_lastname;} ?>" required>
                        <label for="lastname">Last Name</label>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-material">
                        <input class="form-control" type="email" id="email" name="email" value="<?php if(!empty($e_email)){echo $e_email;} ?>" required>
                        <label for="email">Email Address</label>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-material">
                        <input class="form-control" type="text" id="phone" name="phone" value="<?php if(!empty($e_phone)){echo $e_phone;} ?>">
                        <label for="phone">Phone Number</label>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-xs-12 col-sm-7">
                    <div class="form-material">
                        <?php $role = array('HOD', 'Bursary', 'Library', 'Sports', 'Dean'); ?>
                        <select class="js-select2 form-control" id="role" name="role" style="width: 100%;" data-placeholder="Role" required>
                            <option></option>
                            <?php foreach($role as $key=>$value) { ?>
                                <option value="<?php echo $value; ?>" <?php if(!empty($e_role)){if($e_role == $value){echo 'selected';}} ?>><?php echo $value; ?></option>
                            <?php } ?>
                        </select>
                        <label for="role">Role</label>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-5">
                    <div class="form-material">
                        <input class="form-control" type="text" id="password" name="password" value="<?php if(empty($e_id)){echo substr(md5(rand()),0,6);} ?>" <?php if(empty($e_id)){echo 'required';} ?>>
                        <label for="password">Password <small class="text-muted">(auto generated)</small></label>
                    </div>
                </div>
            </div>
        </div>
    
        <div class="form-group text-center m-t-40">
            <div class="col-xs-12">
                <button class="btn btn-primary btn-rounded text-uppercase" type="submit">
                    <i class="fa fa-save"></i> Save
                </button>
            </div>
        </div>
    <?php } ?>
<?php echo form_close(); ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jsform.js"></script>
<script src="<?php echo base_url(); ?>assets/js/plugins/select2/select2.full.min.js"></script>
<script>jQuery(function(){App.initHelpers(['select2']);});</script>