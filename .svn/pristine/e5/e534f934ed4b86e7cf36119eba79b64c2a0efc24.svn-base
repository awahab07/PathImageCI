<?php 

$this->load->view('frontend/templates/header'); 

?>	
<?php isset($nav) ? $this->load->view($nav) : '';?>
<?php if( isset($message) && strlen($message)>0 && !strstr($_SERVER['REQUEST_URI'],"login")) { ?>
<br><br>
<div class="alert alert-info">
  <p><i class="icon-info-sign"></i><?php echo $message; ?></p>
</div>
<?php } ?>
<?php $this->load->view($main_content); ?>

<?php $this->load->view('frontend/templates/footer'); ?>
