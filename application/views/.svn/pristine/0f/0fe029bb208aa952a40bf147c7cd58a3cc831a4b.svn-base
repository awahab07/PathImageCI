<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>html/backend/css/annotate.css">

<!-- Right side column. Contains the navbar and content of the page -->
<aside class="right-side">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Annotate slide capture
            <small>use the controls bellow </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Platform</a></li>
            <li class="active">Upload Image</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row-fluid">
          <div class="span12">

            <form action="<? echo site_url() ?>platform/annotate/upload-image" method="post" enctype="multipart/form-data">
              <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-preview thumbnail" style="width: 200px; height: 150px;"></div>
                <div>
                  <span class="btn btn-file"><span class="fileinput-new">Select Image</span>
                  <span class="fileinput-exists">Change</span><input type="file" name="image" /></span>
                  <a href="#" class="btn fileinput-exists" data-dismiss="fileinput">Remove</a>

                  <div class="form-group fileinput-exists">
                    <label class="col-md-4 control-label" for="title">Image Title</label>
                    <div class="col-md-5">
                      <input id="title" name="title" type="text" placeholder="Enter Image Title" class="form-control input" required>  
                    </div>
                    
                    <div class="col-md-5">
                      <button class="btn btn-primary fileinput-exists" type="submit">Upload</button>
                    </div>
                  </div>
                  
                </div>
              </div>
            </form>

          </div>
        </div>

        <!-- User Images Row -->
        <div class="row-fluid">
          <?php if(count($user_images)): ?><h3>User Images</h3><?php endif; ?>
          <?php foreach ($user_images as $user_image): ?>
            <div class="col-md-4">
              <div class="annotation-link-wrapper">
                <a class="annotate-image-link" href="<?php echo base_url()."platform/annotate/map/{$user_image->id}/{$user_image->first_layer_id}" ?>" >
                  <img class="image image-responsive" src="<?php echo str_replace("\.", base_url(), $user_image->complete_upload_path)?>">
                  <br>
                  <label class="annotation-label"><?php echo $user_image->title?></label>
                  <span class="annotation-msg">Click to Annotate</span>
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
    </section><!-- /.content -->
</aside><!-- /.right-side -->
</div><!-- ./wrapper -->