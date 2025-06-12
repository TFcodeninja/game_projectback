<?php

namespace App\Controller;

use App\Entity\Score;
use App\Repository\ScoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ScoreController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ScoreRepository        $scores,
        private ValidatorInterface     $validator
    ) {}

    /**
     * @Route("/api/scores", name="score_list", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user   = $this->getUser();
        $result = $this->scores->findBy(['user' => $user], ['createdAt' => 'DESC']);

        $data = array_map(fn(Score $s) => [
            'id'        => $s->getId(),
            'value'     => $s->getValue(),
            'kills'     => $s->getKills(),
            'createdAt' => $s->getCreatedAt()->format(\DateTime::ATOM),
        ], $result);

        return $this->json($data);
    }

    /**
     * @Route("/api/scores", name="score_create", methods={"POST"})
     */
    public function create(Request $req): JsonResponse
    {
        $payload = json_decode($req->getContent(), true);
        /** @var \App\Entity\User $user */
        $user  = $this->getUser();
        
        $score = new Score();
        $score->setUser($user)
              ->setValue((int) ($payload['value'] ?? 0))
              ->setKills((int) ($payload['kills'] ?? 0));
        // createdAt est initialisé dans le constructeur

        $errors = $this->validator->validate($score);
        if (count($errors) > 0) {
            return $this->json(
                ['errors' => (string) $errors],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $this->em->persist($score);
        $this->em->flush();

        return $this->json(
            ['message' => 'Score enregistré', 'id' => $score->getId()],
            JsonResponse::HTTP_CREATED
        );
    }
}
