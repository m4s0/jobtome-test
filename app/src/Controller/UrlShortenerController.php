<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ShortUrlRepository;
use App\Service\GetInputParameters;
use App\Service\Shortener;
use App\Service\ShortUrlSerializer;
use App\Service\VisitSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UrlShortenerController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ShortUrlRepository $shortUrlRepository;
    private Shortener $shortener;
    private string $baseName;

    public function __construct(
        EntityManagerInterface $entityManager,
        ShortUrlRepository $shortUrlRepository,
        Shortener $shortener,
        string $baseName
    )
    {
        $this->entityManager = $entityManager;
        $this->shortUrlRepository = $shortUrlRepository;
        $this->shortener = $shortener;
        $this->baseName = $baseName;
    }

    /**
     * Create a ShortUrl
     *
     * @Operation(
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="url",
     *                     type="string",
     *                     example="https://www.google.com"
     *                 ),
     *                 @OA\Property(
     *                     property="valid_since",
     *                     type="string",
     *                     format="date-time",
     *                     example="2020-01-01T00:00:00+00:00"
     *                 ),
     *                 @OA\Property(
     *                     property="valid_until",
     *                     type="string",
     *                     format="date-time",
     *                     example="2030-01-01T00:00:00+00:00"
     *                 ),
     *                 @OA\Property(
     *                     property="max_visits",
     *                     type="integer",
     *                     example="100"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="ShortUrl created",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="id",
     *                 type="integer"
     *             ),
     *             @OA\Property(
     *                 property="url",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="shortUrl",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="shortCode",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="createdAt",
     *                 type="string",
     *                 format="date-time"
     *             ),
     *             @OA\Property(
     *                 property="visits",
     *                 type="integer"
     *             ),
     *             @OA\Property(
     *                 property="validSince",
     *                 type="string",
     *                 format="date-time"
     *             ),
     *             @OA\Property(
     *                 property="validUntil",
     *                 type="string",
     *                 format="date-time"
     *             ),
     *             @OA\Property(
     *                 property="maxVisits",
     *                 type="integer"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Missing or invalid parameters"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable request"
     *     )
     * )
     */
    public function create(Request $request): Response
    {
        try {
            $inputParameters = GetInputParameters::execute($request);
        } catch (\RuntimeException $e) {
            return $this->json(
                ['message' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $shortUrl = $this->shortener->execute(
                $inputParameters->getUrl(),
                $inputParameters->getValidSince(),
                $inputParameters->getValidUntil(),
                $inputParameters->getMaxVisits(),
            );
        } catch (\InvalidArgumentException $e) {
            return $this->json(
                ['message' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\RuntimeException $e) {
            return $this->json(
                ['message' => $e->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $this->entityManager->persist($shortUrl);
        $this->entityManager->flush();

        return $this->json(
            ShortUrlSerializer::serialize($shortUrl, $this->baseName),
            Response::HTTP_CREATED,
        );
    }

    /**
     * Return the ShortUrl
     *
     * @Operation(
     *     @OA\Parameter (
     *         name="shortCode",
     *         in="path",
     *         description="The short code of the ShortUrl",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="ShortUrl",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="id",
     *                 type="integer"
     *             ),
     *             @OA\Property(
     *                 property="url",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="shortUrl",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="shortCode",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="createdAt",
     *                 type="string",
     *                 format="date-time"
     *             ),
     *             @OA\Property(
     *                 property="visits",
     *                 type="integer"
     *             ),
     *             @OA\Property(
     *                 property="validSince",
     *                 type="string",
     *                 format="date-time"
     *             ),
     *             @OA\Property(
     *                 property="validUntil",
     *                 type="string",
     *                 format="date-time"
     *             ),
     *             @OA\Property(
     *                 property="maxVisits",
     *                 type="integer"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="ShortUrl not found",
     *     )
     * )
     */
    public function read(string $shortCode): Response
    {
        $shortUrl = $this->shortUrlRepository->findByShortCode($shortCode);
        if (null === $shortUrl) {
            return $this->json(
                ['message' => 'ShortURL with code '.$shortCode.' not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json(
            ShortUrlSerializer::serialize($shortUrl, $this->baseName),
            Response::HTTP_OK
        );
    }

    /**
     * List of ShortUrls
     *
     * @Operation(
     *     @OA\Response(
     *         response="200",
     *         description="List of ShortUrls",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="count",
     *                 type="integer"
     *             ),
     *             @OA\Property(
     *                 property="shortUrls",
     *                 type="array",
     *                 @OA\Items(ref=@Model(type=\App\Entity\ShortUrl::class, groups={"full"}))
     *             )
     *         )
     *     )
     * )
     */
    public function list(): Response
    {
        $shortUrls = $this->shortUrlRepository->findAll();

        return $this->json(
            ShortUrlSerializer::serializeCollection($shortUrls, $this->baseName),
            Response::HTTP_OK
        );
    }

    /**
     * List of Visits
     *
     * @Operation(
     *     @OA\Parameter (
     *         name="shortCode",
     *         in="path",
     *         description="The short code of the ShortUrl",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="List of Visits",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="count",
     *                 type="integer"
     *             ),
     *             @OA\Property(
     *                 property="visits",
     *                 type="array",
     *                 @OA\Items(ref=@Model(type=\App\Entity\Visit::class, groups={"full"}))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="ShortUrl not found",
     *     )
     * )
     */
    public function visits(string $shortCode): Response
    {
        $shortUrl = $this->shortUrlRepository->findByShortCode($shortCode);
        if (null === $shortUrl) {
            return $this->json(
                ['message' => 'ShortURL with code '.$shortCode.' not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json(
            VisitSerializer::serializeCollection($shortUrl->getVisits()->toArray()),
            Response::HTTP_OK
        );
    }

    /**
     * Delete a ShortUrl
     *
     * @Operation(
     *     @OA\Parameter (
     *         name="shortCode",
     *         in="path",
     *         description="The short code of the ShortUrl",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="ShortUrl deleted",
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="ShortUrl not found",
     *     )
     * )
     */
    public function delete(string $shortCode): Response
    {
        $shortUrl = $this->shortUrlRepository->findByShortCode($shortCode);
        if (null === $shortUrl) {
            return $this->json(
                ['message' => 'ShortURL with code '.$shortCode.' not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $this->entityManager->remove($shortUrl);
        $this->entityManager->flush();

        return $this->json(
            ['message' => 'ShortURL with code '.$shortCode.' has been deleted'],
            Response::HTTP_OK
        );
    }
}
