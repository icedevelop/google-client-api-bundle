Google Api Gsuite for Symfony
=============

This bundle implements an oauth2 authentication with Google.

Note
----

The bundle is under heavy development and should not be used at this time.

Documentation
-------------

This bundle is integrated with Guard and verify if the user is logged in. If is not logged call Goggle via oauth2 to make authentication. 
The authorization process is under development and is dependent from host application.


Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require retitalia/google-client-api-bundle
```

If an error is returned regarding the oauth2 library that can not be installed, this is due to a problem with the bundle paragonie. 
In this case, give this command 

```console
Google_Service_Directory
Google_Service_Directory_Alias
Google_Service_Directory_Aliases
Google_Service_Directory_AppAccessCollections
Google_Service_Directory_Asp
Google_Service_Directory_Asps
Google_Service_Directory_Asps_Resource
Google_Service_Directory_Building
Google_Service_Directory_BuildingCoordinates
Google_Service_Directory_Buildings
Google_Service_Directory_CalendarResource
Google_Service_Directory_CalendarResources
Google_Service_Directory_Channel
Google_Service_Directory_Channels_Resource
Google_Service_Directory_ChromeOsDevice
Google_Service_Directory_ChromeOsDeviceAction
Google_Service_Directory_ChromeOsDeviceActiveTimeRanges
Google_Service_Directory_ChromeOsDeviceDeviceFiles
Google_Service_Directory_ChromeOsDeviceRecentUsers
Google_Service_Directory_ChromeOsDevices
Google_Service_Directory_Chromeosdevices_Resource
Google_Service_Directory_ChromeOsDeviceTpmVersionInfo
Google_Service_Directory_ChromeOsMoveDevicesToOu
Google_Service_Directory_Customer
Google_Service_Directory_CustomerPostalAddress
Google_Service_Directory_Customers_Resource
Google_Service_Directory_DomainAlias
Google_Service_Directory_DomainAliases
Google_Service_Directory_DomainAliases_Resource
Google_Service_Directory_Domains
Google_Service_Directory_Domains2
Google_Service_Directory_Domains_Resource
Google_Service_Directory_Feature
Google_Service_Directory_FeatureInstance
Google_Service_Directory_FeatureRename
Google_Service_Directory_Features
Google_Service_Directory_Group
Google_Service_Directory_Groups
Google_Service_Directory_Groups_Resource
Google_Service_Directory_GroupsAliases_Resource
Google_Service_Directory_Member
Google_Service_Directory_Members
Google_Service_Directory_Members_Resource
Google_Service_Directory_MembersHasMember
Google_Service_Directory_MobileDevice
Google_Service_Directory_MobileDeviceAction
Google_Service_Directory_MobileDeviceApplications
Google_Service_Directory_MobileDevices
Google_Service_Directory_Mobiledevices_Resource
Google_Service_Directory_Notification
Google_Service_Directory_Notifications
Google_Service_Directory_Notifications_Resource
Google_Service_Directory_OrgUnit
Google_Service_Directory_OrgUnits
Google_Service_Directory_Orgunits_Resource
Google_Service_Directory_Privilege
Google_Service_Directory_Privileges
Google_Service_Directory_Privileges_Resource
Google_Service_Directory_ResolvedAppAccessSettings_Resource
Google_Service_Directory_Resources_Resource
Google_Service_Directory_ResourcesBuildings_Resource
Google_Service_Directory_ResourcesCalendars_Resource
Google_Service_Directory_ResourcesFeatures_Resource
Google_Service_Directory_Role
Google_Service_Directory_RoleAssignment
Google_Service_Directory_RoleAssignments
Google_Service_Directory_RoleAssignments_Resource
Google_Service_Directory_RoleRolePrivileges
Google_Service_Directory_Roles
Google_Service_Directory_Roles_Resource
Google_Service_Directory_Schema
Google_Service_Directory_SchemaFieldSpec
Google_Service_Directory_SchemaFieldSpecNumericIndexingSpec
Google_Service_Directory_Schemas
Google_Service_Directory_Schemas_Resource
Google_Service_Directory_Token
Google_Service_Directory_Tokens
Google_Service_Directory_Tokens_Resource
Google_Service_Directory_TrustedAppId
Google_Service_Directory_TrustedApps
Google_Service_Directory_User
Google_Service_Directory_UserAbout
Google_Service_Directory_UserAddress
Google_Service_Directory_UserEmail
Google_Service_Directory_UserExternalId
Google_Service_Directory_UserGender
Google_Service_Directory_UserIm
Google_Service_Directory_UserKeyword
Google_Service_Directory_UserLanguage
Google_Service_Directory_UserLocation
Google_Service_Directory_UserMakeAdmin
Google_Service_Directory_UserName
Google_Service_Directory_UserOrganization
Google_Service_Directory_UserPhone
Google_Service_Directory_UserPhoto
Google_Service_Directory_UserPosixAccount
Google_Service_Directory_UserRelation
Google_Service_Directory_Users
Google_Service_Directory_Users_Resource
Google_Service_Directory_UsersAliases_Resource
Google_Service_Directory_UsersPhotos_Resource
Google_Service_Directory_UserSshPublicKey
Google_Service_Directory_UserUndelete
Google_Service_Directory_UserWebsite
Google_Service_Directory_VerificationCode
Google_Service_Directory_VerificationCodes
Google_Service_Directory_VerificationCodes_Resource
```

and then repeat the bundle installation procedure


read the documentations:

https://developers.google.com/resources/api-libraries/documentation/admin/directory_v1/php/latest/

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
//get All Users
        $users=$gSuite->getUsers();

        //Set Email
        $gSuite->setEmail($email);

        //How to update Relations
        $gSuite->setRelationType('manager');
        $gSuite->setRelationValue('Giangymongy@gmail.com');
        $gSuite->setRelationCutomType(null);
        $gSuite->updateRelations();

        //how to update Organization

        $gSuite->setOrganizationDepartment('Dipartimento degli stefanini logorroici');
        $gSuite->setOrganizationPrimary(true);
        $gSuite->setOrganizationCustomType(null);
        $gSuite->setOrganizationName('Provola Nome');
        $gSuite->updateOrganization();

        // how to update personale information

        $gSuite->setFullName('Gianluca Mongelli');
        $gSuite->setFamilyName('Mongelli');
        $gSuite->setGivenName('Giangy');
        $gSuite->setTelephonPrimary(true);
        #$gSuite->setTelephonType('work');
        $gSuite->setTelephonNumber('3240537258');
        $gSuite->updatePersonalInformation();
```
In parameters.yaml set the scopes
```php
 scope_auth: [ 'https://www.googleapis.com/auth/userinfo.email',
                  'https://www.googleapis.com/auth/userinfo.profile',
                  'https://www.googleapis.com/auth/plus.me',
                  'https://www.googleapis.com/auth/admin.directory.user.readonly',
                  'https://www.googleapis.com/auth/admin.directory.user.alias',
                  'https://www.googleapis.com/auth/admin.directory.group',
                  'https://www.googleapis.com/auth/admin.directory.group.readonly',
                  'https://www.googleapis.com/auth/admin.directory.group.member.readonly',
                  'https://www.googleapis.com/auth/admin.directory.orgunit',
                  'https://www.googleapis.com/auth/admin.directory.orgunit.readonly',
                  'https://www.googleapis.com/auth/admin.directory.user',
                  'https://www.googleapis.com/auth/admin.directory.user.alias.readonly',
                  'https://www.googleapis.com/auth/admin.directory.userschema',
                  'https://www.googleapis.com/auth/admin.directory.domain']
```

The correct values for parameters can be get from
https://gitlab.com/retitalia/contenitore-bundle-comuni

License
-------

This bundle is under the MIT license.


Usage
============


The authentication is automatic, it calls via oauth2 google sso and perform the process.

The authorization is based on specific database. The user must be enabled in correct table and must have a role for specified application.

It test the application specified in parameters.yml via application_id parameter.

There are however others useful functions that can be called manually.

Test specified function
-------

