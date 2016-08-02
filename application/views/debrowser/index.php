<!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>DEBrowser
                        <small>Differential Expression Browser</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php echo BASE_PATH?>"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">DEBrowser</li>
                    </ol>
                </section>
                <!-- Main content -->
                <section class="content">
						<iframe id="debrowser" src="<?php echo DEBROWSER_HOST.$jsonobject?>" frameborder="0" style="height:100vh;width:100%">
						</iframe>
                </section><!-- /.content -->