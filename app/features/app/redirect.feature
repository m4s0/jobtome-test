Feature:
  In order to check that the redirect URL Shortener works
  As an HTTP client
  I need to be able to send an http request and be redirected to the original URL if it exists and is valid

  Background:
    Given the database is clean

  Scenario: It redirects to the original url if the maximum number of visits has not been reached
    Given There are ShortURL
      | url                             | shortCode | validSince                | validUntil                | maxVisits |
      | https://www.domain.com/test-url | 123abc    | 2021-01-01T00:00:00+01:00 | 2030-01-01T00:00:00+01:00 | 1         |
    And I send a GET request to "/123abc"
    Then the response status code should be 302
    Then I should be redirected to "https://www.domain.com/test-url"
    And I send a GET request to "/123abc"
    Then the response status code should be 404
