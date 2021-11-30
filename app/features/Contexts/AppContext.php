<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\ShortUrl;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;
use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Factory\MatcherFactory;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\Assert;
use RuntimeException;

class AppContext implements Context
{
    private Session $session;
    private EntityManagerInterface $entityManager;

    public function __construct(
        Session $session,
        EntityManagerInterface $entityManager
    ) {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    /**
     * @Given the database is clean
     */
    public function theDatabaseIsClean(): void
    {
        $this->dropAndCreateDatabase();
    }

    public function dropAndCreateDatabase(): void
    {
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        if (!empty($metadata)) {
            $tool = new SchemaTool($this->entityManager);

            $db = $this->entityManager->getConnection()->getDatabase();
            $connection = $this->entityManager->getConnection();

            $connection->createSchemaManager()->dropAndCreateDatabase($db);

            $connection->close();
            $connection->connect();

            $tool->createSchema($metadata);
        }
    }

    /**
     * @Given the JSON response should match:
     */
    public function theJSONResponseShouldMatch(PyStringNode $jsonPattern): void
    {
        $matcherFactory = new MatcherFactory();
        $matcher = $matcherFactory->createMatcher(new Backtrace\InMemoryBacktrace());

        $content = $this->session->getPage()->getContent();

        if (!$matcher->match($content, (string) $jsonPattern)) {
            throw new RuntimeException($matcher->getError());
        }
    }

    /**
     * @Given There are ShortURL
     */
    public function thereAreShortURL(TableNode $table): void
    {
        foreach ($table->getHash() as $row) {
            $url = $row['url'] ?? null;
            $shortCode = $row['shortCode'] ?? null;
            $validSince = $row['validSince'] ? \DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $row['validSince']) : null;
            $validUntil = $row['validUntil'] ? \DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $row['validUntil']) : null;
            $maxVisits = $row['maxVisits'] ? filter_var($row['maxVisits'], FILTER_VALIDATE_INT) : null;

            $shortUrl = new ShortUrl($url, $shortCode, $validSince, $validUntil, $maxVisits);
            $this->entityManager->persist($shortUrl);
        }

        $this->entityManager->flush();
    }

    /**
     * @Then I should be redirected to :url
     */
    public function iShouldBeRedirectedTo(string $url): void
    {
        $client = $this->session->getDriver()->getClient();
        $client->followRedirects(true);
        $client->followRedirect();

        Assert::assertEquals($url, $client->getInternalRequest()->getUri());
    }
}
