<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Visit;
use App\Repository\ShortUrlRepository;
use App\Service\CheckShortUrlValidity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ShortUrlRepository $shortUrlRepository;

    public function __construct(EntityManagerInterface $entityManager, ShortUrlRepository $shortUrlRepository)
    {
        $this->entityManager = $entityManager;
        $this->shortUrlRepository = $shortUrlRepository;
    }

    public function try(string $shortCode, Request $request): Response
    {
        $shortUrl = $this->shortUrlRepository->findByShortCode($shortCode);
        if (null === $shortUrl) {
            return new Response('Short URL not found', Response::HTTP_NOT_FOUND);
        }

        try {
            CheckShortUrlValidity::execute($shortUrl);
        } catch (\RuntimeException $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        $visit = new Visit(
            $shortUrl,
            $request->headers->get('Referer'),
            $request->getClientIp(),
            $request->headers->get('User-Agent')
        );

        $this->entityManager->persist($visit);
        $this->entityManager->flush();

        return new RedirectResponse(
            $shortUrl->getOriginalUrl(),
            Response::HTTP_FOUND,
            $request->headers->all()
        );
    }
}
