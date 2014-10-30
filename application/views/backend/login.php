<section class="right-side">

    <div class="row-fluid">
    	<div class="col-lg-6">
		<form id="login-form" method="post" class="form-horizontal" action="<?php echo base_url(); ?>platform/login/login_process">
			<fieldset>
				<!-- Form Name -->
				<legend>Log In</legend>
				<!-- Text input-->
				<div class="form-group">
					<label class="col-md-4 control-label" for="username">Username</label>
					<div class="col-md-6">
						<input id="username" name="username" type="text" placeholder="User Name" class="form-control input-md" required="required">
					</div>
				</div>
				<!-- Password input-->
				<div class="form-group">
					<label class="col-md-4 control-label" for="password">Password </label>
					<div class="col-md-5">
						<input id="password" name="password" type="password" placeholder="placeholder" class="form-control input-md">
						
					</div>
				</div>
				<!-- Button (Double) -->
				<div class="form-group">
					<label class="col-md-4 control-label" for="button1id"></label>
					<div class="col-md-8">
						<button type="submit" id="login" name="login" class="btn btn-success">Login</button>
						<button type="reset" id="cancel" name="cancel" class="btn btn-inverse">Cancel</button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
    </div>
</section>