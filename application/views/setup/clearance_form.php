<?php echo form_open('clearance/?clear='.$e_id, array('id'=>'bb_ajax_form','class'=>'form-horizontal')); ?>
	<div id="bb_ajax_msg"></div><br />
    
    <div class="col-lg-12">
        <div class="form-group">
            <div class="col-xs-12">
                <div class="form-material">
                    <input id="clear_id" name="clear_id" type="hidden" value="<?php if(!empty($e_id)){echo $e_id;} ?>">
                    <input class="form-control" type="text" id="status" name="status" required>
                    <label for="status">Clearance Status/Remark</label>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group text-center m-t-40">
        <div class="col-xs-12">
            <button class="btn btn-primary btn-rounded text-uppercase" type="submit">
                <i class="si si-check"></i> Update Clearance
            </button>
        </div>
    </div>
<?php echo form_close(); ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jsform.js"></script>