<div class="app">
    <div class="container" ng-app="setupApp" ng-controller="mainController" ng-cloak>
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="navbar m-t-40">
                    <div class="navbar-brand m-t-lg text-center">
                        <?php echo Asset::img('logo.png', ['width' => '150px']); ?>
                    </div>
                </div>
                <h3 class="text-center">
                    Setup
                </h3>
            </div>
            <div class="col-md-6 col-md-offset-3" ng-show="step == 1">
                <div class="p-lg panel shadow-x2 text-color m">
                    <div class="m-b text-sm">
                        <h4 class="no-margin-top">Dependency check</h4>
                        <span>
                            Welcome to Gitftp, lets check if all the required dependencies are installed.
                        </span>
                    </div>
                    <table class="table">
                        <tr>
                            <td style="width: 50px">
                                <div ng-if="step1.loading" class="loader" style="width: 19px">
                                    <svg class="circular" viewBox="25 25 50 50">
                                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5"
                                                stroke-miterlimit="10"/>
                                    </svg>
                                </div>
                                <i ng-if="step1.dep.php == 2 && !step1.loading" class="zmdi zmdi-close-circle red"
                                   style="font-size: 21px;text-align: center;"></i>
                                <i ng-if="step1.dep.php == 1 && !step1.loading" class="zmdi zmdi-check-circle green"
                                   style="font-size: 21px;text-align: center;"></i>
                                <i ng-if="step1.dep.git == false && !step1.loading" class="zmdi zmdi-dot-circle gray"
                                   style="font-size: 21px;text-align: center;"></i>
                            </td>
                            <td>
                                PHP
                                <span ng-if="step1.dep.phpVersion" class="text-muted" style="font-size: 12px">
                                    ({{step1.dep.phpVersion}})
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div ng-if="step1.loading" class="loader" style="width: 19px">
                                    <svg class="circular" viewBox="25 25 50 50">
                                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5"
                                                stroke-miterlimit="10"/>
                                    </svg>
                                </div>
                                <i ng-if="step1.dep.git == 2 && !step1.loading" class="zmdi zmdi-close-circle red"
                                   style="font-size: 21px;text-align: center;"></i>
                                <i ng-if="step1.dep.git == 1 && !step1.loading" class="zmdi zmdi-check-circle green"
                                   style="font-size: 21px;text-align: center;"></i>
                                <i ng-if="step1.dep.git == false && !step1.loading" class="zmdi zmdi-dot-circle gray"
                                   style="font-size: 21px;text-align: center;"></i>
                            </td>
                            <td>
                                Git
                                <span ng-if="step1.dep.gitVersion" class="text-muted" style="font-size: 12px">
                                   ({{step1.dep.gitVersion}})
                                </span>
                            </td>
                        </tr>
                    </table>
                    <div>
                        <div class="space20"></div>
                        <p ng-if="step1.os_name && !step1.loading" class="text-right">
                            Operating System: {{step1.os_name}} <br>
                            Looks good.
                        </p>
                        <div class="text-right">
                            <button type="button" class="btn btn-primary btn-stroke btn-rounded white blue-bg p-h-md waves-effect"
                                    ng-if="!step1.go"
                                    ng-disabled="step1.loading"
                                    ng-click="step1.start()">{{step1.loading ? 'CHECKING' : 'START'}}
                            </button>
                            <button type="button" class="btn btn-primary btn-stroke btn-rounded white blue-bg p-h-md waves-effect"
                                    ng-if="step1.go && !step1.loading"
                                    ng-click="gotoStep(2)">NEXT <i class="zmdi zmdi-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-md-offset-3" ng-show="step == 2">
                <div class="p-lg panel shadow-x2 text-color m">
                    <div class="m-b text-sm">
                        <h4 class="no-margin-top">
                            Your mysql database</h4>
                        <span>
                            We need information on your database, where Gitftp will be installed. <br>
                            Pfff, developers know what this is.
                        </span>
                    </div>
                    <form name="form" class="no-margin" ng-submit="test_database()">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="md-form-group float-label">
                                            <input type="text" class="md-input" required ng-model="db.dbname">
                                            <label>Database Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="md-form-group float-label">
                                            <input type="text" class="md-input" ng-model="db.host" required>
                                            <label>Database host</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="md-form-group float-label">
                                            <input type="text" class="md-input" required ng-model="db.password">
                                            <label>Password</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="md-form-group float-label">
                                            <input type="text" class="md-input" required ng-model="db.username">
                                            <label>User Name</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-danger" ng-if="step2.error">
                            Could not connect: <br>
                            {{step2.error}}
                        </div>
                        <span ng-if="step2.loading">
                            <div class="loader" style="width: 32px;display: inline-block">
                                <svg class="circular" viewBox="25 25 50 50">
                                    <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2"
                                            stroke-miterlimit="10"/>
                                </svg>
                            </div>
                        </span>
                        <button type="submit" ng-disabled="form.$invalid || step2.loading"
                                class="btn btn-primary btn-stroke btn-rounded pull-right">
                            <span ng-if="!step2.loading">INSTALL</span>
                            <span ng-if="step2.loading">{{step2.status}}</span>
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
            <div class="col-md-6 col-md-offset-3" ng-show="step == 3">
                <div class="p-lg panel shadow-x2 text-color m">
                    <div class="m-b text-sm">
                        <h4 class="no-margin-top">
                            <a ng-click="gotoStep(2)" ng-if="!step3.loading" class="">
                                <i class="zmdi zmdi-arrow-left zmdi-hc-fw" style="font-size: 19px;"></i>
                            </a>
                            3 of {{tSteps}}: create OAuth application</h4>
                        <span>As a standalone software, gitftp will require an app to connect to your repositories</span>
                    </div>
                    <p>
                        Please select the provider you want to setup, <br>you can add/edit providers in your site
                        settings
                        later.
                    </p>
                    <form name="form3" class="no-margin" ng-submit="save_oauth_config()">
                        <div class="radio">
                            <label class="ui-checks ui-checks-md">
                                <input type="radio" name="step3provider" value="github" ng-model="step3.provider">
                                <i></i>
                                Github
                            </label>
                        </div>
                        <div class="radio">
                            <label class="ui-checks ui-checks-md">
                                <input type="radio" name="step3provider" value="bitbucket" ng-model="step3.provider">
                                <i></i>
                                Bitbucket
                            </label>
                        </div>

                        <div class="row" ng-if="step3.provider == 'github'">
                            <div class="col-md-12">
                                <p>
                                    <strong>Github:</strong> Register a new OAuth provider. <br>
                                    Goto settings > OAuth applications > Register a new application

                                    <br>
                                    <strong>Application name: </strong> Anything you like <br>
                                    <strong>Homepage URL: </strong> {{base}} <br>
                                    <strong>Authorization callback URL: </strong> {{githubCallback}}
                                </p>
                            </div>
                            <div class="col-md-12">
                                <div class="md-form-group float-label">
                                    <input type="text" class="md-input" required ng-model="step3.github.clientId">
                                    <label>Client ID</label>
                                </div>
                                <div class="md-form-group float-label">
                                    <input type="text" class="md-input" required ng-model="step3.github.clientSecret">
                                    <label>Client Secret</label>
                                </div>
                            </div>
                        </div>
                        <p class="red" ng-if="step3.error">
                            Could not connect: <br>
                            <code>{{step3.error}}</code>
                        </p>
                        <span ng-if="step3.loading">
                            <div class="loader" style="width: 32px;display: inline-block">
                                <svg class="circular" viewBox="25 25 50 50">
                                    <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2"
                                            stroke-miterlimit="10"/>
                                </svg>
                            </div>
                        </span>
                        <button type="submit" ng-disabled="form3.$invalid || step3.loading"
                                class="md-btn md-raised white blue-bg p-h-md waves-effect pull-right">
                            <span ng-if="!step3.loading">next <i class="zmdi zmdi-arrow-right"></i></span>
                            <span ng-if="step3.loading">Saving credentials</span>
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
            <div class="col-md-6 col-md-offset-3">
                <div class="p-lg panel shadow-x2 text-color m" ng-show="step == 4">
                    <div class="m-b text-sm">
                        <h4 class="no-margin-top">
                            <a ng-click="gotoStep(3)" ng-if="!step4.loading" class="">
                                <i class="zmdi zmdi-arrow-left zmdi-hc-fw" style="font-size: 19px;"></i>
                            </a>
                            4 of {{tSteps}}: Your admin account
                        </h4>
                        <span>Primary account for the site.</span>
                    </div>

                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
                            <form name="form4" class="no-margin" ng-submit="create_user()">
                                <div class="md-form-group float-label">
                                    <input type="email" class="md-input" ng-model="user.email" required>
                                    <label>E-mail</label>
                                </div>
                                <div class="md-form-group float-label">
                                    <input type="password" class="md-input" ng-model="user.password" required>
                                    <label>Create password</label>
                                </div>
                                <p class="red" ng-if="step4.error">
                                    {{step4.error}}
                                </p>
                                <div ng-if="step4.loading" class="loader" style="width: 50px">
                                    <svg class="circular" viewBox="25 25 50 50">
                                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2"
                                                stroke-miterlimit="10"/>
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <button type="submit" ng-disabled="form4.$invalid"
                                            ng-if="!step4.loading"
                                            class="md-btn md-raised white blue-bg p-h-md waves-effect btn-block">
                                        Create &amp; Goto dashboard
                                    </button>
                                </div>
                                <div class="clearfix"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    angular.module('setupApp', [])
        .controller('mainController', [
            '$scope',
            '$http',
            function ($scope, $http) {
                $scope.step = 1;
                $scope.tSteps = 4;
                var current = '<?php echo \Fuel\Core\Uri::base() ?>setup/api';

                $scope.base = '<?php echo $baseUrl; ?>';
                $scope.githubCallback = '<?php echo $githubCallbackUrl ?>';
                $scope.bitbucketCallback = '<?php echo $bitbucketCallbackUrl ?>';

                // step1
                var s1 = $scope.step1 = {};
                s1.loading = false;
                s1.go = false;
                s1.dep = {
                    php: false,
                    phpVersion: false,
                    git: false,
                    gitVersion: false,
                };

                s1.start = function () {
                    s1.loading = true;
                    $http.post(current + '/dep_test.json', {}).then(function (res) {
                        if (res.data.status) {
                            s1.dep.php = res.data.data.php[0];
                            s1.dep.phpVersion = res.data.data.php[1];
                            s1.dep.git = res.data.data.git[0];
                            s1.dep.gitVersion = res.data.data.git[1];
                            s1.go = res.data.data.allOk;
                            s1.os_name = res.data.data.os;
                        }
                        else {
                            n(res.data.reason, 'error');
                        }
                        s1.loading = false;
                    }, function () {
                        s1.loading = false;
                        n('Could not connect', 'error');
                    })
                };
                $scope.gotoStep = function (step) {
                    $scope.step = step;
                };

                // step2
                $scope.db = {};
                var s2 = $scope.step2 = {};
                s2.loading = false;
                s2.error = '';
                s2.status = '';
                $scope.test_database = function () {
                    s2.loading = true;
                    s2.error = '';
                    s2.status = 'CONNECTING';
                    $http.post(current + '/db_save', {
                        db: $scope.db
                    }).then(function (res) {
                        if (res.data.status) {
                            s2.status = 'INSTALLING SCHEMA';
                            $http.post(current + '/db_install', {
                                db: $scope.db
                            }).then(function (res) {
                                s2.loading = false;
                                if (res.data.status) {
                                    $scope.step = 3;
                                } else {
                                    s2.error = res.data.reason;
                                }
                            }, function (reason) {
                                s2.loading = false;
                                s2.error = 'Could not connect';
                                s2.status = '';
                            })
                        } else {
                            s2.loading = false;
                            s2.error = res.data.reason;
                            s2.status = '';
                        }
                    }, function (reason) {
                        s2.loading = false;
                        s2.error = 'Could not connect';
                        s2.status = '';
                    });
                };


                var s3 = $scope.step3 = {
                    github: {},
                    bitbucket: {}
                };
                s3.error = '';

                $scope.save_oauth_config = function () {
                    s3.loading = true;
                    s3.error = '';
                    var provider = {};
                    if (s3.provider == 'github') {
                        provider = s3.github;
                    } else if (s3.provider == 'bitbucket') {
                        provider = s3.bitbucket;
                    } else {
                        return;
                    }
                    $http.post(current + '/save_oauth_config', {
                        provider: s3.provider,
                        config: provider,
                    }).then(function (res) {
                        if (res.data.status) {
                            $scope.gotoStep(4);
                        } else {
                            s3.error = res.data.reason;
                        }
                        s3.loading = false;
                    }, function () {
                        s3.loading = false;
                        s3.error = 'Could not connect';
                    })
                };

                $scope.user = {};
                var s4 = $scope.step4 = {};
                s4.error = '';

                $scope.create_user = function () {
                    s4.loading = true;
                    s4.error = '';
                    $http.post(current + '/create_user', {
                        user: $scope.user
                    }).then(function (res) {
                        if (res.data.status) {
                            location.href = $scope.base;
                        } else {
                            s4.error = res.data.reason;
                        }
                        s4.loading = false;
                    }, function () {
                        s4.loading = false;
                        s4.error = 'Could not connect';
                    })
                }
            }
        ])
</script>