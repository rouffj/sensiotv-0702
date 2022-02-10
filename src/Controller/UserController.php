<?php

namespace App\Controller;

//use App\Entity\User;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Event\UserRegisteredEvent;

class UserController extends AbstractController
{

    /**
     * new Route("", "", "")
     * @Route("/register", name="register")
     */
    public function register(Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $passwordHasher,
        EventDispatcherInterface $eventDispatcher): Response
    {

        $form = $this->createForm(UserType::class, null, [
            'crud_action' => 'CREATE',
        ]);
        //$form->add('save', Type\SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
            $manager->persist($user);
            $manager->flush();

            $eventDispatcher->dispatch(new UserRegisteredEvent($user), 'user_registered');
            // TODO Insert in DB.
            dump($user);
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(): Response
    {
        return $this->render('user/signin.html.twig');
    }
}
