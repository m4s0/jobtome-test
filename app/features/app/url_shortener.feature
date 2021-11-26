Feature:
  In order to check that the URL Shortener works
  As a API client
  I need to be able to create a short URL

  Scenario: It create a short url
    And I send a POST request to "/api"
    """
    {
        "url": "https://www.domain.com/test-url"
    }
    """
    And I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    Then the response status code should be 201
