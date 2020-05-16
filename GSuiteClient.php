<?php
/**
 * Created by PhpStorm.
 * User: PENTA-EDYNET
 * Date: 08/10/2018
 * Time: 17:13
 */

namespace retItalia\GoogleClientApiBundle;


use Google_Client;
use Google_Service_Directory;
use Google_Service_Directory_User;
use Google_Service_Directory_UserRelation;
use http\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;


class GSuiteClient
{
    private $service;
    private $accessToken;
    private $email;

    /**
     * PERSONAL INFORMATIONS
    */
    private $familyName;
    private $fullName;
    private $givenName;

    private $telephonNumber;
    private $telephonType;
    private $telephonPrimary = true;


    /********************
    RELATIONS
     ********************/
    private $relationValue;
    /**
     * Accept only value into #this->relationTypeList()
     */
    private $relationType;
    private $relationCutomType;

    /********************
    ORGANIZATIONS
     ********************/

    private $organizationPrimary;
    private $organizationCustomType;
    private $organizationDepartment;
    private $organizationName;
    private $organizationTitle;
    /**
     * Accept only value into #this->organizationTypeList()
     */
    private $organizationType;
    private $organizationLocation;
    private $organizationDescription;

    private $container;


    public function __construct(ContainerInterface $container)
    {
        /*
        $session=$container->get('session');
        $accessToken=$session->get('googleTokenF');
        $this->accessToken=$accessToken->getToken();
        $this->service=new Google_Service_Directory($this->getClient());
        */

        $this->container = $container;
        $this->service=new Google_Service_Directory($this->getClient());
    }



    public function updatePersonalInformation(){
        $users=$this->checkEmailsExist();
        $user=new Google_Service_Directory_User();
        $userName = new \Google_Service_Directory_UserName();
        $phone=new \Google_Service_Directory_UserPhone();

        if($users){
            if(isset($this->telephonType)){
                $phone->setType($this->telephonType);
            }

            if(isset($this->telephonNumber)){
                $phone->setValue($this->telephonNumber);
            }

            if(isset($this->telephonPrimary)){
                $phone->setPrimary($this->telephonPrimary);
            }

            if(isset($this->familyName)){
                $userName->setFamilyName($this->familyName);
            }

            if(isset($this->fullName)){
                $userName->setFullName($this->fullName);
            }

            if(isset($this->givenName)){
                $userName->setGivenName($this->givenName);
            }

            $user->setPhones(array($phone));
            if(isset($this->familyName) && isset($this->givenName) && isset($this->fullName)){
                $user->setName($userName);
            }

            return $this->service->users->update($users->getPrimaryEmail(), $user);

        }else{
            return false;
        }
    }



    public function updateRelations()
    {
        $users=$this->checkEmailsExist();

        if($users){
            $relation=new Google_Service_Directory_UserRelation();
            $user=new Google_Service_Directory_User();

            if (isset($this->relationType)) {
                $relation->setType($this->relationType);
            }
            if (isset($this->relationValue)) {
                $relation->setValue($this->relationValue);
            }
            if (isset($this->relationCutomType)) {
                $relation->setCustomType($this->relationCutomType);
            }

            
            $user->setRelations(array($relation));

            return $this->service->users->update($users->getPrimaryEmail(), $user);
        }else{
            return false;
        }

    }

    public function updateOrganization()
    {
        $users=$this->checkEmailsExist();
        if($users){
            $organization=new \Google_Service_Directory_UserOrganization();

            if (isset($this->organizationPrimary)) {
                $organization->setPrimary($this->organizationPrimary);
            }
            if (isset($this->organizationDepartment)) {
                $organization->setDepartment($this->organizationDepartment);
            }
            if (isset($this->relationCutomType)) {
                $organization->setCustomType($this->organizationCustomType);
            }
            if(isset($this->organizationName)){
                $organization->setName($this->organizationName);
            }
            if(isset($this->organizationTitle)){
                $organization->setTitle($this->organizationTitle);
            }
            if(isset($this->organizationDescription)){
                $organization->setDescription($this->organizationDescription);
            }
            


            $user=new Google_Service_Directory_User();
            $user->setOrganizations(array($organization));
            return $this->service->users->update($users->getPrimaryEmail(), $user);
        }else{
            return false;
        }

    }

    public function getUsers()
    {
        $optParams=array(
            'customer'=>'my_customer',
            'orderBy'=>'email'
        );
        $result=$this->service->users->listUsers($optParams);
        return $result->getUsers();
    }

    /**
     * Method to retrieve data from email, you can set more email it will return an array with user matched
     * is not matched will return false
     *
     * @return array|bool|mixed|string
     */
    public function checkEmailsExist()
    {
        $users=$this->getUsers();
        $userExists=[];
        if (isset($this->email)) {
            foreach ($users as $user) {
                $emailUser=$user->getPrimaryEmail();

                if ($emailUser == $this->email) {
                    $userExists=$user;
                }
            }

            if (count($userExists) <= 0) {
                return false;
            } else {
                return $userExists;
            }
        } else {
            return false;
        }
    }

    public function getAliasFromEmail()
    {
        $alias=[];
        if ($this->checkEmailsExist()) {
            $users=$this->checkEmailsExist();
            $count=count($users);
            if ($count > 0) {
                $alias[]=[
                    'alias'=>$users->getAliases(),
                    'email'=>$users->getPrimaryEmail()
                ];
            }
            return $alias;
        } else {
            return false;
        }
    }

    public function getUserPicture()
    {
        $pictures=[];
        if ($this->checkEmailsExist()) {
            $users=$this->checkEmailsExist();
            $count=count($users);
            if ($count === 2) {
                $pictures=[
                    'avatar'=>$this->getImage($users->getId()),
                    'url'=>$users->getThumbnailPhotoUrl(),
                    'email'=>$users->getPrimaryEmail()
                ];
            } elseif($count === 1) {
                $pictures=[
                    'avatar'=>$this->getImage($users->getId()),
                    'url'=>$users->getThumbnailPhotoUrl(),
                    'email'=>$users->getPrimaryEmail()

                ];
            }else{
                $pictures = null;
            }
            return $pictures;
        } else {
            return null;
        }
    }

    private function getImage($id)
    {
        $data=@file_get_contents('http://picasaweb.google.com/data/entry/api/user/' . $id . '?alt=json');
        if ($data) {
            $d=json_decode($data);
            return $d->{'entry'}->{'gphoto$thumbnail'}->{'$t'};
        } else {
            return null;
        }
    }

    /**
     * @return Google_Client
     * @throws \Google_Exception
     */
    private function getClient()
    {
        try {

            /*
            $client=new Google_Client();
            $client->setAccessToken($this->accessToken);
            return $client;
            */

            /*
            $fileLocator = new FileLocator();
            $jsonCredentialsPath = $fileLocator->locate('@GoogleClientApiBundle/Resources/json/credentials.json');
            $jsonTokenPath = $fileLocator->locate('@GoogleClientApiBundle/Resources/json/token.json');
            */

            /*
            $fileLocator = new FileLocator(array(__DIR__.'/../Resources/json'));
            $jsonCredentialsPath = $fileLocator->locate('credentials.json');
            $jsonTokenPath = $fileLocator->locate('token.json');
            */

            $jsonCredentialsPath = $this->container->getParameter('google_credentials_location');
            $jsonTokenPath = $this->container->getParameter('google_token_location');

            //echo $jsonCredentialsPath;
            //exit();

            $client = new Google_Client();
            $client->setApplicationName('G Suite Directory API PHP Quickstart');
            //$client->setScopes(Google_Service_Directory::ADMIN_DIRECTORY_USER_READONLY);
            $client->setScopes([
                Google_Service_Directory::ADMIN_DIRECTORY_DOMAIN,
                Google_Service_Directory::ADMIN_DIRECTORY_GROUP,
                Google_Service_Directory::ADMIN_DIRECTORY_GROUP_MEMBER,
                Google_Service_Directory::ADMIN_DIRECTORY_ORGUNIT,
                Google_Service_Directory::ADMIN_DIRECTORY_USER,
                Google_Service_Directory::ADMIN_DIRECTORY_USER_ALIAS,
                Google_Service_Directory::ADMIN_DIRECTORY_USERSCHEMA,
            ]);
            $client->setAuthConfig($jsonCredentialsPath);
            $client->setAccessType('offline');
            $client->setPrompt('select_account consent');

            // Load previously authorized token from a file, if it exists.
            // The file token.json stores the user's access and refresh tokens, and is
            // created automatically when the authorization flow completes for the first
            // time.
            $tokenPath = $jsonTokenPath;
            if (file_exists($tokenPath)) {
                $accessToken = json_decode(file_get_contents($tokenPath), true);
                $client->setAccessToken($accessToken);
            }

            // If there is no previous token or it's expired.
            if ($client->isAccessTokenExpired()) {
                // Refresh the token if possible, else fetch a new one.
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                } else {
                    // Request authorization from the user.
                    $authUrl = $client->createAuthUrl();
                    printf("Open the following link in your browser:\n%s\n", $authUrl);
                    print 'Enter verification code: ';
                    $authCode = trim(fgets(STDIN));

                    // Exchange authorization code for an access token.
                    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                    $client->setAccessToken($accessToken);

                    // Check to see if there was an error.
                    if (array_key_exists('error', $accessToken)) {
                        throw new \Exception(join(', ', $accessToken));
                    }
                }
                // Save the token to a file.
                if (!file_exists(dirname($tokenPath))) {
                    mkdir(dirname($tokenPath), 0700, true);
                }
                file_put_contents($tokenPath, json_encode($client->getAccessToken()));
            }
            return $client;

        } catch (Exception $message) {

        }
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email=$email;
    }


    /**
     * @return mixed
     */
    public function getRelationValue()
    {
        return $this->relationValue;
    }

    /**
     * @param mixed $relationValue
     */
    public function setRelationValue($relationValue)
    {
        $this->relationValue=$relationValue;
    }

    /**
     * @return mixed
     */
    public function getRelationType()
    {
        return $this->relationType;
    }

    /**
     * @param mixed $relationType
     */
    public function setRelationType($relationType)
    {
        $this->relationType=$relationType;
    }

    /**
     * @return mixed
     */
    public function getRelationCutomType()
    {
        return $this->relationCutomType;
    }

    /**
     * @param mixed $relationCutomType
     */
    public function setRelationCutomType($relationCutomType)
    {
        $this->relationCutomType=$relationCutomType;
    }

    /**
     * @return mixed
     */
    public function getOrganizationCustomType()
    {
        return $this->organizationCustomType;
    }

    /**
     * @param mixed $organizationCustomType
     */
    public function setOrganizationCustomType($organizationCustomType)
    {
        $this->organizationCustomType=$organizationCustomType;
    }

    /**
     * @return mixed
     */
    public function getOrganizationPrimary()
    {
        return $this->organizationPrimary;
    }

    /**
     * @param mixed $organizationPrimary
     */
    public function setOrganizationPrimary($organizationPrimary)
    {
        $this->organizationPrimary=$organizationPrimary;
    }

    /**
     * @return mixed
     */
    public function getOrganizationDepartment()
    {
        return $this->organizationDepartment;
    }

    /**
     * @param mixed $organizationDepartment
     */
    public function setOrganizationDepartment($organizationDepartment)
    {
        $this->organizationDepartment=$organizationDepartment;
    }

    /**
     * @return mixed
     */
    public function getOrganizationName()
    {
        return $this->organizationName;
    }

    /**
     * @param mixed $organizationName
     */
    public function setOrganizationName($organizationName)
    {
        $this->organizationName=$organizationName;
    }

    /**
     * @return mixed
     */
    public function getOrganizationTitle()
    {
        return $this->organizationTitle;
    }

    /**
     * @param mixed $organizationTitle
     */
    public function setOrganizationTitle($organizationTitle)
    {
        $this->organizationTitle=$organizationTitle;
    }

    /**
     * @return mixed
     */
    public function getOrganizationType()
    {
        return $this->organizationType;
    }

    /**
     * @param mixed $organizationType
     */
    public function setOrganizationType($organizationType)
    {
        $this->organizationType=$organizationType;
    }

    /**
     * @return mixed
     */
    public function getOrganizationLocation()
    {
        return $this->organizationLocation;
    }

    /**
     * @param mixed $organizationLocation
     */
    public function setOrganizationLocation($organizationLocation)
    {
        $this->organizationLocation=$organizationLocation;
    }

    /**
     * @return mixed
     */
    public function getOrganizationDescription()
    {
        return $this->organizationDescription;
    }

    /**
     * @param mixed $organizationDescription
     */
    public function setOrganizationDescription($organizationDescription)
    {
        $this->organizationDescription=$organizationDescription;
    }

    /**
     * @return mixed
     */
    public function getTelephonNumber()
    {
        return $this->telephonNumber;
    }

    /**
     * @param mixed $telephonNumber
     */
    public function setTelephonNumber($telephonNumber)
    {
        $this->telephonNumber=$telephonNumber;
    }





    /**
     * @return mixed
     */
    public function getTelephonType()
    {
        return $this->telephonType;
    }

    /**
     * @param mixed $telephonType
     */
    public function setTelephonType($telephonType)
    {
        $this->telephonType=$telephonType;
    }

    /**
     * @return bool
     */
    public function isTelephonPrimary()
    {
        return $this->telephonPrimary;
    }

    /**
     * @param bool $telephonPrimary
     */
    public function setTelephonPrimary($telephonPrimary)
    {
        $this->telephonPrimary=$telephonPrimary;
    }

    /**
     * @return mixed
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * @param mixed $familyName
     */
    public function setFamilyName($familyName)
    {
        $this->familyName=$familyName;
    }

    /**
     * @return mixed
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param mixed $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName=$fullName;
    }

    /**
     * @return mixed
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * @param mixed $givenName
     */
    public function setGivenName($givenName)
    {
        $this->givenName=$givenName;
    }


    private function getScopeForGSuite()
    {
        return [
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
            'https://www.googleapis.com/auth/plus.me',
            'https://www.googleapis.com/auth/admin.directory.user.readonly',
            'https://www.googleapis.com/auth/admin.directory.orgunit',
            'https://www.googleapis.com/auth/admin.directory.group',
            'https://www.googleapis.com/auth/admin.directory.group.readonly'
        ];
    }

    public function relationTypeList()
    {
        return [
            "admin_assistant",
            "assistant",
            "brother",
            "child",
            "custom",
            "domestic_partner",
            "dotted_line_manager",
            "exec_assistant",
            "father",
            "friend",
            "manager",
            "mother",
            "parent",
            "partner",
            "referred_by",
            "relative",
            "sister",
            "spouse"
        ];
    }

    public function organizationTypeList()
    {
        return [
            "domain_only",
            "school",
            "unknown",
            "work"
        ];
    }


}