Feature:
  In order to count redirections of shortened URLs
  As a API client
  I need to be able to read the number of redirections of a shortened URL

  Background:
    Given the database is clean

  Scenario: It read the number of redirections of a shortened URL
    Given There are ShortURL
      | url                             | shortCode | validSince                | validUntil                | maxVisits |
      | https://www.domain.com/test-url | 123abc    | 2021-01-01T00:00:00+01:00 | 2030-01-01T00:00:00+01:00 | 10        |
    And I send a GET request to "/123abc"
    Then the response status code should be 302
    And I send a GET request to "/123abc"
    Then the response status code should be 302
    And I add "Accept" header equal to "application/json"
    And I send a GET request to "/api/short-url/123abc/visits"
    Then the response status code should be 200
    And the JSON response should match:
    """
    {
      "count": 2,
      "visits": [
        {
          "id": "@integer@",
          "date": "@datetime@",
          "referer": "@string@",
          "ip": "127.0.0.1",
          "userAgent": "Symfony BrowserKit"
        },
        {
          "id": "@integer@",
          "date": "@datetime@",
          "referer": "@string@",
          "ip": "127.0.0.1",
          "userAgent": "Symfony BrowserKit"
        }
      ]
    }
    """
