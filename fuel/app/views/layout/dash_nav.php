<div style="height: 20px;"></div>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#">Gitploy</a>
                    </div>

                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <!--                        <ul class="nav navbar-nav">
                                                    <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
                                                    <li><a href="#">Link</a></li>
                                                    <li class="dropdown">
                                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
                                                        <ul class="dropdown-menu" role="menu">
                                                            <li><a href="#">Action</a></li>
                                                            <li><a href="#">Another action</a></li>
                                                            <li><a href="#">Something else here</a></li>
                                                            <li class="divider"></li>
                                                            <li><a href="#">Separated link</a></li>
                                                            <li class="divider"></li>
                                                            <li><a href="#">One more separated link</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>-->
                        <ul class="nav navbar-nav navbar-left">
                            <li><a href="<?php echo Uri::base(false); ?>dashboard">Dashboard</a></li>
                            <li><a href="<?php echo Uri::base(false); ?>dashboard/ftp">FTP servers</a></li>
                            <li><a href="<?php echo Uri::base(false); ?>dashboard/git">GIT repos</a></li>
                        </ul>
                    </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
            </nav>
        </div>
    </div>
</div>
