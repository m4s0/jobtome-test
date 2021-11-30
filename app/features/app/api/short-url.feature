Feature:
  In order to check that the URL Shortener works
  As a API client
  I need to be able to create, read and delete a short URL

  Background:
    Given the database is clean

  Scenario: It create a short url
    Given I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a POST request to "/api/short-url"
    """
    {
        "url": "https://www.domain.com/test-url",
        "valid_since": "2020-01-01T00:00:00+01:00",
        "valid_until": "2021-01-01T00:00:00+01:00",
        "max_visits": "10"
    }
    """
    Then the response status code should be 201
    And the JSON response should match:
    """
    {
        "id": "@integer@",
        "short_code": "@string@",
        "url": "https://www.domain.com/test-url",
        "short_url": "http://domain.test/@string@",
        "created_at": "@datetime@",
        "visits": 0,
        "valid_since": "2020-01-01T00:00:00+01:00",
        "valid_until": "2021-01-01T00:00:00+01:00",
        "max_visits": 10
    }
    """

  Scenario: It read a short url
    Given There are ShortURL
      | url                             | shortCode | validSince                | validUntil                | maxVisits |
      | https://www.domain.com/test-url | 123abc    | 2021-01-01T00:00:00+01:00 | 2030-01-01T00:00:00+01:00 | 10        |
    And I add "Accept" header equal to "application/json"
    And I send a GET request to "/api/short-url/123abc"
    Then the response status code should be 200
    And the JSON response should match:
    """
    {
        "id": "@integer@",
        "short_code": "@string@",
        "url": "https://www.domain.com/test-url",
        "short_url": "http://domain.test/@string@",
        "created_at": "@datetime@",
        "visits": 0,
        "valid_since": "2021-01-01T00:00:00+01:00",
        "valid_until": "2030-01-01T00:00:00+01:00",
        "max_visits": 10
    }
    """

  Scenario: It get list of short url
    Given There are ShortURL
      | url                               | shortCode | validSince                | validUntil                | maxVisits |
      | https://www.domain.com/test-url-1 | 123abc    | 2021-01-01T00:00:00+01:00 | 2030-01-01T00:00:00+01:00 | 10        |
      | https://www.domain.com/test-url-2 | 456DEF    | 2021-01-01T00:00:00+01:00 | 2030-01-01T00:00:00+01:00 | 5         |
    And I add "Accept" header equal to "application/json"
    And I send a GET request to "/api/short-url"
    Then the response status code should be 200
    And the JSON response should match:
    """
    {
      "count": 2,
      "short_urls": [
        {
          "id": "@integer@",
          "short_code": "@string@",
          "url": "https://www.domain.com/test-url-1",
          "short_url": "http://domain.test/@string@",
          "created_at": "@datetime@",
          "visits": 0,
          "valid_since": "2021-01-01T00:00:00+01:00",
          "valid_until": "2030-01-01T00:00:00+01:00",
          "max_visits": 10
        },
        {
          "id": "@integer@",
          "short_code": "@string@",
          "url": "https://www.domain.com/test-url-2",
          "short_url": "http://domain.test/@string@",
          "created_at": "@datetime@",
          "visits": 0,
          "valid_since": "2021-01-01T00:00:00+01:00",
          "valid_until": "2030-01-01T00:00:00+01:00",
          "max_visits": 5
        }
      ]
    }
    """

  Scenario: It delete a short url
    Given There are ShortURL
      | url                             | shortCode | validSince                | validUntil                | maxVisits |
      | https://www.domain.com/test-url | 123abc    | 2021-01-01T00:00:00+01:00 | 2030-01-01T00:00:00+01:00 | 1         |
    And I add "Accept" header equal to "application/json"
    And I send a DELETE request to "/api/short-url/123abc"
    Then the response status code should be 200
    And the JSON response should match:
    """
    {
      "message": "ShortURL with code 123abc has been deleted"
    }
    """