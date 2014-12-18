 <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <li class="active">
                            <a href="<?php echo BASE_PATH?>">
                                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-bar-chart-o"></i>
                                <span>NGS Tracking</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?php echo BASE_PATH?>/ngsimport"><i class="fa fa-angle-double-right"></i>Excel Import</a></li>
                                <li><a href="<?php echo BASE_PATH?>/ngstrack"><i class="fa fa-angle-double-right"></i>NGS Tracking</a></li>
                                <li><a href="<?php echo BASE_PATH?>/experimentseries"><i class="fa fa-angle-double-right"></i>Series</a></li>
                                <li><a href="<?php echo BASE_PATH?>/protocols"><i class="fa fa-angle-double-right"></i>Protocols</a></li>
                                <li><a href="<?php echo BASE_PATH?>/contributors"><i class="fa fa-angle-double-right"></i>Contributors</a></li>
                                <li><a href="<?php echo BASE_PATH?>/lanes"><i class="fa fa-angle-double-right"></i>Lanes</a></li>
                                <li><a href="<?php echo BASE_PATH?>/samples"><i class="fa fa-angle-double-right"></i>Samples</a></li>
                                <li><a href="<?php echo BASE_PATH?>/dirs"><i class="fa fa-angle-double-right"></i>Dirs</a></li>
                                <li><a href="<?php echo BASE_PATH?>/fastqfiles"><i class="fa fa-angle-double-right"></i>Fastq Files</a></li>
                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-bar-chart-o"></i>
                                <span>Usage Reports</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?php echo BASE_PATH?>/galaxystats"><i class="fa fa-angle-double-right"></i>Galaxy Stats</a></li>
                                <li><a href="<?php echo BASE_PATH?>/dolphinstats"><i class="fa fa-angle-double-right"></i>Dolphin Stats</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa fa-laptop"></i>
                                <span>Dolphin Runs</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa fa-edit"></i> <span>Forum</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                        </li>
                    </ul>
                </section>
                <!-- /.sidebar -->
