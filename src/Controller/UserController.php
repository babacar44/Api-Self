<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;


/**
 * @Route("/api")
 */
class UserController extends AbstractController
{
 /**
 * @Route("/register", name="api_register", methods={"POST"})
 */
public function register(ObjectManager $om, UserPasswordEncoderInterface $passwordEncoder, Request $request)
{

$valeur=json_decode($request->getContent());
$user = new User();
$email=$valeur->email;
$roles=$valeur->roles;
$password=$valeur->password;
//    $email= $request->request->get("email");
//    $password= $request->get("password");
   $passwordConfirmation=$valeur->password_confirmation;
    // $request->request->get("password_confirmation");
   $errors = [];
   if($password != $passwordConfirmation)
   {
       $errors[] = "Password does not match the password confirmation.";
   }
   
   if(strlen($password) < 6)
   {
       $errors[] = "Le mot de passe doit comporter au moins 6 caractères.";
   }
   if(!$errors)
   {
       $encodedPassword = $passwordEncoder->encodePassword($user, $password);
       $user->setEmail($email);
       $user->setRoles($roles);
       $user->setPassword($encodedPassword);
       try
       {
           $om->persist($user);
           $om->flush();
           return $this->json([
               'user' => $user
           ]);
       }
       catch(UniqueConstraintViolationException $e)
       {
           $errors[] = "L'e-mail fourni a déjà un compte!";
       }
       catch(\Exception $e)
       {
           $errors[] = "Impossible de sauvegarder le nouvel utilisateur pour le moment";
       }
   }
  
   return $this->json([
       'errors' => $errors
   ], 400);
}


/**
 * @Route("/login", name="api_login", methods={"POST"})
 */
public function login()
{
    return $this->json(['result' => true]);
}
}
