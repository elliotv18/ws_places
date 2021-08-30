<?php
namespace App\Controller;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class AuthController extends ApiController
{
    /**
* @Route("/register",name="api_register",methods="POST")
*/

 public function register(Request $request, UserPasswordEncoderInterface $encoder)
 {
 $em = $this->getDoctrine()->getManager();
 $request = $this->transformJsonBody($request);
 $username = $request->get('username');
 $password = $request->get('password');
 if (empty($username) || empty($password)) {
 return $this->respondValidationError("Invalid Username or Password");}
 $user = new User($username);
 $user->setPassword($encoder->encodePassword($user, $password));
 $user->setUsername($username);
 $em->persist($user);
 $em->flush();
 return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getUsername()));
 }
 /**
 * @Route("/api/login_check",name="api_login_check",methods="POST")
 * @param UserInterface $user
 * @param JWTTokenManagerInterface $JWTManager
 * @return JsonResponse
 */
 public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager){
    return new JsonResponse(['token' => $JWTManager->create($user)]);
    }
   }
   