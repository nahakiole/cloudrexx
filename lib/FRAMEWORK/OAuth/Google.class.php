<?php

namespace Cx\Lib\OAuth;

global $cl;
$cl->loadFile(ASCMS_LIBRARY_PATH . '/services/Google/Google_Client.php');

/**
 * OAuth class for google authentication
 *
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Ueli Kramer <ueli.kramer@comvation.com>
 * @version     1.0.0
 * @package     contrexx
 * @subpackage  lib_oauth
 */

class Google extends OAuth
{
    /**
     * contains only email address and user id
     * @var the object of the third party library
     */
    private static $google;

    /**
     * contains the sensitive data of the user
     * @var the object of the third party library
     */
    private static $googleplus;

    /**
     * @var the user data of the logged in social media user
     */
    protected static $userdata;

    /**
     * @var array the permissions used to retrieve the needed userdata
     */
    private static $scopes = array(
        'https://www.googleapis.com/auth/plus.me',
        'https://www.googleapis.com/auth/userinfo.email'
    );

    const OAUTH_PROVIDER = 'google';

    /**
     * Login to facebook and get the associated contrexx user.
     */
    public function login()
    {
        $client = new \Google_Client();
        $client->setApplicationName('Contrexx Login');
        $client->setClientId($this->applicationData[0]);
        $client->setClientSecret($this->applicationData[1]);
        $client->setRedirectUri(\Cx\Lib\SocialLogin::getLoginUrl(self::OAUTH_PROVIDER));
        $client->setDeveloperKey($this->applicationData[2]);
        $client->setUseObjects(true);
        $client->setApprovalPrompt('auto');
        $client->setScopes(self::$scopes);
        self::$google = new \Google_Oauth2Service($client);
        self::$googleplus = new \Google_PlusService($client);


        if (isset($_GET['code'])) {
            try {
                $client->authenticate();
            } catch (\Google_AuthException $e) {
            }
        }

        if (!$client->getAccessToken()) {
            \Cx\Core\Csrf\Controller\Csrf::header('Location: ' . $client->createAuthUrl());
            exit;
        }

        self::$userdata = $this->getUserData();
        $this->getContrexxUser(self::$userdata['oauth_id']);
    }

    /**
     * Get all the user data from facebook server.
     *
     * @return array
     */
    public function getUserData()
    {
        $userdata = array();
        $userinfo = self::$google->userinfo->get();
        $userprofile = self::$googleplus->people->get('me');
        $userdata['oauth_id'] = $userinfo->id;
        $userdata['email'] = $userinfo->email;
        $userdata['last_name'] = $userprofile->name->familyName;
        $userdata['first_name'] = $userprofile->name->givenName;
        return $userdata;
    }

    /**
     * @static
     * @return array the configuration parameters as language array key
     */
    public static function configParams()
    {
        return $configParams = array(
            'TXT_ACCESS_SOCIALLOGIN_PROVIDER_APP_ID',
            'TXT_ACCESS_SOCIALLOGIN_PROVIDER_SECRET',
            'TXT_ACCESS_SOCIALLOGIN_PROVIDER_API_KEY',
        );
    }
}