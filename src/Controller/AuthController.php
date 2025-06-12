<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface      $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface    $jwtManager,
        private ValidatorInterface          $validator
    ) {
        // constructeur vide
    }

    #[Route(path: '/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setEmail($data['email'] ?? '');
        $user->setCodename($data['codename'] ?? '');
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $data['password'] ?? '')
        );

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            return $this->json(
                ['errors' => (string) $errors],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'Utilisateur créé'], JsonResponse::HTTP_CREATED);
    }

    #[Route(path: '/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
    $data = json_decode($request->getContent(), true);
    /** @var UserRepository $repo */
    $repo = $this->entityManager->getRepository(User::class);
    $user = $repo->findOneBy(['email' => $data['email'] ?? '']);
    if (!$user || !password_verify($data['password'] ?? '', $user->getPassword())) {
        return $this->json(['error'=>'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
    }
    $token = $this->jwtManager->create($user);
    return $this->json(['token'=>$token]);
    }

}
