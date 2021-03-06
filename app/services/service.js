"use strict";

angular.module('ServiceApi', []).factory('Api', [
    '$http',
    '$q',
    'Utils',
    '$rootScope',
    '$ngConfirm',
    '$timeout',
    function ($http, $q, Utils, $rootScope, $ngConfirm, $timeout) {
        return {
            /**
             * Create or save user
             */
            saveUser: function (user) {
                var defer = $q.defer();
                $http.post(API_PATH + 'users/save_user', {
                    user: user,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });

                return defer.promise;
            },
            /**
             * Get list of users
             */
            getUsers: function (offset) {
                var defer = $q.defer();
                $http.post(API_PATH + 'users/list', {
                    offset: offset,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });

                return defer.promise;
            },
            /**
             * Get user by user id
             */
            getUser: function (id) {
                var defer = $q.defer();
                $http.post(API_PATH + 'users/user', {
                    id: id,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });

                return defer.promise;
            },
            /**
             * Test connection to server
             */
            testServerConnectionByData: function (server_data, writeTest) {
                var defer = $q.defer();
                $http.post(API_PATH + 'server/test', {
                    server: server_data,
                    writeTest: writeTest,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });

                return defer.promise;
            },
            /**
             * Get commits from project
             */
            getRevisions: function (project_id, branch) {
                var defer = $q.defer();
                $http.post(API_PATH + 'projects/revisions', {
                    project_id: project_id,
                    branch: branch,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * start cloning a project
             */
            startProjectCloning: function (project_id) {
                var defer = $q.defer();
                $http.post(API_PATH + 'projects/clone', {
                    project_id: project_id,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });

                return defer.promise;
            },
            /**
             * Get server but without password.
             * @param server_id
             */
            getServer: function (server_id) {
                var defer = $q.defer();
                $http.post(API_PATH + 'server/view', {
                    server_id: server_id,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * Get server but without password.
             * @param project_id
             */
            getServers: function (project_id) {
                var defer = $q.defer();
                $http.post(API_PATH + 'server/view', {
                    project_id: project_id,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * get records for project.
             * server_id is optional
             * project_id is required
             */
            getRecordStatus: function (record_id) {
                var defer = $q.defer();
                $http.post(API_PATH + 'projects/record_status', {
                    record_id: record_id,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * Checks if the hook exists
             */
            checkHook: function (project_id) {
                var defer = $q.defer();
                $http.post(API_PATH + 'projects/check_hook', {
                    project_id: project_id,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * Checks if the hook exists
             */
            createHook: function (project_id) {
                var defer = $q.defer();
                $http.post(API_PATH + 'projects/create_hook', {
                    project_id: project_id,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * Delete the project
             */
            deleteProject: function (project_id) {
                var defer = $q.defer();
                $http.post(API_PATH + 'projects/delete', {
                    project_id: project_id,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * Delete the project
             */
            deleteServer: function (server_id, project_id) {
                var defer = $q.defer();
                $http.post(API_PATH + 'server/delete', {
                    project_id: project_id,
                    server_id: server_id,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * Get log file
             * @param filename
             */
            updateProject: function (project_id, set) {
                var defer = $q.defer();
                $http.post(API_PATH + 'projects/update', {
                    project_id: project_id,
                    project: set,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * Get log file
             * @param filename
             */
            getRecordLog: function (filename) {
                var defer = $q.defer();
                $http.post(API_PATH + 'server/log_file', {
                    filename: filename,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * start cloning a project
             */
            getLatestRecords: function (project_id, latest_id, server_id) {
                var defer = $q.defer();
                $http.post(API_PATH + 'projects/records', {
                    project_id: project_id,
                    latest_id: latest_id,
                    server_id: server_id,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });

                return defer.promise;
            },
            /**
             * get records for project.
             * server_id is optional
             * project_id is required
             */
            getRecords: function (project_id, server_id) {
                var defer = $q.defer();
                $http.post(API_PATH + 'projects/records', {
                    project_id: project_id,
                    server_id: server_id,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * Get all projects
             */
            getProjects: function () {
                var defer = $q.defer();
                $http.post(API_PATH + 'projects/view', {}).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * Get project by id
             */
            getSingleProject: function (id) {
                var defer = $q.defer();
                $http.post(API_PATH + 'projects/view', {
                    project_id: id
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * create a server for a project
             */
            createServer: function (project_id, set) {
                var defer = $q.defer();
                $http.post(API_PATH + 'server/create', {
                    server: set,
                    project_id: project_id,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * getServerKey
             */
            getServerKey: function (cacheId) {
                var defer = $q.defer();
                $http.post(API_PATH + 'server/key', {
                    id: cacheId
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * serverDownloadKey
             */
            serverDownloadKey: function (cacheId) {
                var defer = $q.defer();
                $http.post(API_PATH + 'server/download_key', {
                    id: cacheId
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * create a project
             */
            createProject: function (set) {
                var defer = $q.defer();
                $http.post(API_PATH + 'projects/create', {
                    project: set
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * compare commits
             */
            compareCommits: function (project_id, server_id, source, target) {
                var defer = $q.defer();
                $http.post(API_PATH + 'server/compare', {
                    project_id: project_id,
                    server_id: server_id,
                    source_revision: source,
                    target_revision: target,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * Pull changes from remote
             */
            syncProject: function (project_id) {
                var defer = $q.defer();

                $http.post(API_PATH + 'projects/pull_changes', {
                    project_id: project_id,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * add a new record and start deploy
             */
            applyDeploy: function (project_id, server_id, deploy) {
                var defer = $q.defer();
                $http.post(API_PATH + 'server/deploy', {
                    project_id: project_id,
                    server_id: server_id,
                    deploy: deploy,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * get available branches for the repository by project id
             */
            getAvailableBranchesByProject: function (project_id) {
                var defer = $q.defer();
                $http.post(API_PATH + 'projects/list_available_branches', {
                    project_id: project_id
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * get available branches for the repository
             */
            getAvailableBranches: function (repository) {
                var defer = $q.defer();
                $http.post(API_PATH + 'projects/list_available_branches', {
                    repository: repository
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * get oauth connected accounts
             */
            getAvailableRepositories: function () {
                var defer = $q.defer();
                $http.post(API_PATH + 'projects/list_available_repositories', {}).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * get oauth connected accounts
             */
            getConnectedAccounts: function () {
                var defer = $q.defer();
                $http.post(API_PATH + 'accounts/list', {}).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * get oauth connected accounts
             */
            disconnectConnectedAccounts: function (provider_id) {
                var defer = $q.defer();
                $http.post(API_PATH + 'accounts/disconnect', {
                    id: provider_id,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * get oauth applications
             */
            getOAuthApplications: function () {
                var defer = $q.defer();
                $http.post(API_PATH + 'oauth/list', {}).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * get oauth applications
             */
            updatePassword: function (oldPass, newPass) {
                var defer = $q.defer();
                $http.post(API_PATH + 'auth/update_password', {
                    'old_password': oldPass,
                    'new_password': newPass
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            },
            /**
             * set oauth applications
             * @param applications
             */
            saveOAuthApplications: function (applications) {
                var defer = $q.defer();
                $http.post(API_PATH + 'oauth/save', {
                    github: applications.github || null,
                    bitbucket: applications.bitbucket || null,
                }).then(function (res) {
                    if (res.data.status) {
                        defer.resolve(res.data.data);
                    } else {
                        defer.reject(res.data.reason);
                    }
                }, function () {
                    defer.reject(API_CONNECTION_ERROR);
                });
                return defer.promise;
            }
        };
    }
]).factory('ProjectApi', [
    '$rootScope',
    'Api',
    '$interval',
    '$timeout',
    'Utils',
    '$q',
    function ($rootScope, Api, $interval, $timeout, Utils, $q) {
        return {
            isListenerActive: false,
            registerListeners: function () {
                if (this.isListenerActive)
                    return false;

                var that = this;
                $rootScope.$on('refreshProjects', function () {
                    that.refreshProjects();
                });

                var refreshProjectsLoop = function () {
                    $timeout(function () {
                        that.refreshProjects().then(function () {
                            refreshProjectsLoop();
                        });
                    }, 5000);
                };

                refreshProjectsLoop();

                this.isListenerActive = true;
            },
            refreshProjects: function () {
                var defer = $q.defer();
                Api.getProjects().then(function (data) {
                    var object = {};
                    angular.forEach(data, function (a) {
                        object[a.id] = a;
                    });
                    data = undefined;
                    if (!angular.equals($rootScope.projects, object)) {
                        $rootScope.projects = object;
                        $rootScope.$broadcast('projectsUpdated');
                    }
                    object = undefined;
                    defer.resolve();
                }, function (reason) {
                    console.error(reason);
                    defer.resolve();
                });

                return defer.promise;
            },
        };
    }
]);