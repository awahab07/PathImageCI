<?php $this->load->view('backend/templates/header'); ?>	
<?php $this->load->view('backend/templates/nav-header'); ?>	
<body class="skin-blue">
<?php $this->load->view('backend/templates/nav-menu'); ?>
<?php //$this->load->view('backend/templates/flash-messages.php'); ?>
 <?php $this->load->view($main_content); ?>
<?php $this->load->view('backend/templates/footer'); ?>
