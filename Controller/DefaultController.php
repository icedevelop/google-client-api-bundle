<?php

namespace retItalia\GoogleClientApiBundle\Controller;

use retItalia\GoogleClientApiBundle\GSuiteClient;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    public function indexAction()
    {

        $email = 'userrit1@test.ice.it';

        $gSuite=$this->get(GSuiteClient::class);

        //get All Users
        $users=$gSuite->getUsers();

        //Set Email
        $gSuite->setEmail($email);

        //How to update Relations
        $gSuite->setRelationType('manager');
        $gSuite->setRelationValue('Giangymongy@gmail.com');
       # $gSuite->setRelationCutomType('custom Type');
        $gSuite->updateRelations();

        //how to update Organization

        $gSuite->setOrganizationDepartment('UFFICI VARI');
        $gSuite->setOrganizationPrimary(true);
        $gSuite->setOrganizationCustomType('custom Type');
        $gSuite->setOrganizationName('Nome Organizzazione');
        $gSuite->updateOrganization();

        // how to update personale information

        $gSuite->setFullName('Gianluca Mongelli');
        $gSuite->setFamilyName('Mongelli');
        $gSuite->setGivenName('Giangy');
        $gSuite->setTelephonPrimary(true);
        $gSuite->setTelephonType('work');
        $gSuite->setTelephonNumber('3240537258');
        $gSuite->updatePersonalInformation();


        $user = $gSuite->checkEmailsExist();

        $pictures = $gSuite->getUserPicture();

        $alias =  $gSuite->getAliasFromEmail();

        return $this->render('@retItaliaGoogleClientApi/Default/index.html.twig', ['data'=>$email, 'dato' => $user,'picture'=>$pictures,'alias'=>$alias,'user'=>$users]);




    }


}
