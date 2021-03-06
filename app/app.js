"use strict";

angular.module('App', [
    'ngRoute',
    'AppDirectives',
    'ngAnimate',
    'ServiceUtils',
    'ServiceComponents',
    'ui.bootstrap',
    'ServiceApi',
    'cp.ngConfirm',
    'angularMoment',
    'AppFilters',
    'ngStorage',
    'AppHome',
    'AppSettings',
    'AppProjectNew',
    'AppProjectView',
    'AppProjectSettings',
    'AppProjectServerAdd',
    'AppProjectServerDeploy',
    'AppProjectServerView',
]).config([
    '$routeProvider',
    '$locationProvider',
    '$httpProvider',
    function ($routeProvider, $locationProvider, $httpProvider) {
        $routeProvider.otherwise({
            redirectTo: '/'
        });
        $locationProvider.html5Mode(true);
    }
]).run([
    '$rootScope',
    'Utils',
    '$localStorage',
    '$ngConfirmDefaults',
    'ProjectApi',
    function ($rootScope, Utils, $localStorage, $ngConfirmDefaults, ProjectApi) {
        $ngConfirmDefaults.theme = 'material,gitftp';
        $ngConfirmDefaults.animation = 'scale';
        $ngConfirmDefaults.closeAnimation = 'scale';
        $ngConfirmDefaults.animationSpeed = 300;
        $rootScope.utils = Utils;
        $rootScope.$storage = $localStorage;
        ProjectApi.registerListeners();
    }
]).constant('Const', {
    'clone_state_not_cloned': 1,
    'clone_state_cloning': 2,
    'clone_state_cloned': 3,
    'pull_state_pulled': 1,
    'pull_state_pulling': 2,
    'record_status_success': 4,
    'record_status_failed': 3,
    'record_status_in_progress': 2,
    'record_status_new': 1,
    'record_type_clone': 1,
    'record_type_update': 2,
    'record_type_fresh_upload': 3,
    'record_type_revert': 4,
    'server_type_ftp': 1,
    'server_type_sftp': 2,
    'server_type_local': 3,
}).constant('UserConst', {
    90: 'Member',
    100: 'Administrator',
    'member': 90,
    'administrator': 100,
});