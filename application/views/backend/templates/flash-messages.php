<!-- Flash Messages -->
<section class="right-side">
	<?php // Success/Message flash message ?>
	<?php if($this->session->flashdata('message')){?>
		<?php $message_array = is_array($this->session->flashdata('message'))?$this->session->flashdata('message'):array($this->session->flashdata('message')); ?>
		<?php foreach($message_array as $message) : ?>
		<div class="alert alert-info">
			<button data-dismiss="alert" class="close" type="button">x</button>
			<strong>Message!</strong> <?=$message?>
		</div>
		<?php endforeach; ?>
	<?php } ?>

	<?php //Success flash message ?>
	<?php if($this->session->flashdata('success')){?>
		<?php $message_array = is_array($this->session->flashdata('success'))?$this->session->flashdata('success'):array($this->session->flashdata('success')); ?>
		<?php foreach($message_array as $message) : ?>
		<div class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">x</button>
			<strong>Success!</strong> <?=$message?>
		</div>
		<?php endforeach; ?>
	<?php } ?>


	<?php //<?php //Success flash message ?>
	<?php if($this->session->flashdata('warning')){?>
		<?php $message_array = is_array($this->session->flashdata('warning'))?$this->session->flashdata('warning'):array($this->session->flashdata('warning')); ?>
		<?php foreach($message_array as $message) : ?>
		<div class="alert">
			<button data-dismiss="alert" class="close" type="button">x</button>
			<strong>Warning!</strong> <?=$message?>
		</div>
		<?php endforeach; ?>
	<?php } ?>


	<?php //<?php //Success flash message ?>        
	<?php if($this->session->flashdata('error')){?>
		<?php $message_array = is_array($this->session->flashdata('error'))?$this->session->flashdata('error'):array($this->session->flashdata('error')); ?>
		<?php foreach($message_array as $message) : ?>
		<div class="alert alert-danger">
			<button data-dismiss="alert" class="close" type="button">x</button>
			<strong>Error!</strong> <?=$message?>
		</div>
		<?php endforeach; ?>
	<?php } ?>
</section>