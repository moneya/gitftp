<?php

class Controller_Api_User extends Controller {

    public function action_oauth($provider = NULL) {
        if (is_null($provider)) {
            // please provider provider.
            \Response::redirect_back();
        }

        // load Opauth, it will load the provider strategy and redirect to the provider
        \Auth_Opauth::forge();
    }

    public function action_callback() {
        try {
            $opauth = \Auth_Opauth::forge(FALSE);
            $status = $opauth->login_or_register();
            $provider = $opauth->get('auth.provider', '?');
            echo '<pre>';
            print_r($opauth);
            switch ($status) {
                // a local user was logged-in, the provider has been linked to this user
                case 'linked':
                    // inform the user the link was succesfully made
                    echo sprintf(__('login.provider-linked'), ucfirst($provider));
                    // and set the redirect url for this status
                    $url = dash_url;
                    break;

                // the provider was known and linked, the linked account as logged-in
                case 'logged_in':
                    // inform the user the login using the provider was succesful
                    echo sprintf(__('login.logged_in_using_provider'), ucfirst($provider));
                    // and set the redirect url for this status
                    $url = dash_url;
                    break;

                // we don't know this provider login, ask the user to create a local account first
                case 'register':
                    // inform the user the login using the provider was succesful, but we need a local account to continue
                    echo sprintf(__('login.register-first'), ucfirst($provider));
                    // and set the redirect url for this status
                    $url = '';
                    break;

                // we didn't know this provider login, but enough info was returned to auto-register the user
                case 'registered':
                    // inform the user the login using the provider was succesful, and we created a local account
                    echo __('login.auto-registered');
                    // and set the redirect url for this status
                    $url = dash_url;
                    break;

                default:
                    throw new \FuelException('Auth_Opauth::login_or_register() has come up with a result that we dont know how to handle.');
            }

            // redirect to the url set
            if (!empty($url))
                \Response::redirect($url);
        } // deal with Opauth exceptions
        catch (\OpauthException $e) {
            echo $e->getMessage();
//            \Response::redirect_back();
        } // catch a user cancelling the authentication attempt (some providers allow that)
        catch (\OpauthCancelException $e) {
            // you should probably do something a bit more clean here...
            exit('It looks like you canceled your authorisation.' . \Html::anchor('users/oath/' . $provider, 'Click here') . ' to try again.');
        }

    }


    public function action_login() {
        try {
            if (!\Auth::instance()->check()) {

            } else {

            }
        } catch (Exception $e) {
            $response = array(
                'status' => FALSE,
                'reason' => $e->getMessage(),
            );
        }

        echo json_encode($response);
    }
}