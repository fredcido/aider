Feature: Company search
	In order to find secondees
	As a corporate user
	I need to search for a company

	Rules:
		- match any occurence of the search string in the field (doesn't have to be at the start)
		- results are ordered by name alphabetically (case insensitive)
		- search is case insensitive
		- need 3 characters minimum for the search to execute
		- special characters are not replaced (searching for 'and' will not search for '&')

	Background:

		Given the following "companies" exist
        | name                    | logo_url                 |
        | Herbert Smith Freehills |                          |
        | Ashursts                |                          |
        | KWM                     |                          |
        | K\&L Gates              |                          |
        | DLA Piper               | http://dlapiper.com/logo |
        | Baker \& McKenzie       |                          |
        | Norton Rose Fulbright   |                          |
        | Minter Ellison          |                          |
        | Clayton Utz             |                          |
        | Corrs                   |                          |
        | Allens                  |                          |
        | Holman Fenwick William  |                          |
        | Jones Day               |                          |
        | HWL Ebsworth            |                          |
        | Gibert \& Tobin         |                          |
        | Clifford Chance         |                          |
        | Allen \& Overy          |                          |
        | Piper Alderman          |                          |

	Scenario: Basic match
        When I send a GET request to "/companies?name=DLA"
		Then I get 1 result
		And result 1 contains "name" "DLA Piper"

	Scenario: No match
      When I send a GET request to "/companies?name=Zaz"
		Then I get 0 result

	Scenario: Multiple results
      When I send a GET request to "/companies?name=piper"
		Then I get 2 results
		And result 1 contains "name" "DLA Piper"
		And result 2 contains "name" "Piper Alderman"

	Scenario: Special characters
        When I send a GET request to "/companies?name=Baker & McKenzie"
		Then I get 1 result
        And result 1 contains "name" "Baker \& McKenzie"

    Scenario: Test JSON Listing the collection
        When I send a GET request to "/companies"
        Then the response code should be 200
        And response should contain json:
        """
          [
            {
              "id":1,
              "name":"Herbert Smith Freehills",
              "logo_url":""
            },
            {
              "id":2,
              "name":"Ashursts",
              "logo_url":""
            },
            {
              "id":3,
              "name":"KWM",
              "logo_url":""
            },
            {
              "id":4,
              "name":"K\\&L Gates",
              "logo_url":""
            },
            {
              "id":5,
              "name":"DLA Piper",
              "logo_url":"http://dlapiper.com/logo"
            }
          ]
        """

    Scenario: Test JSON Listing by id
      When I send a GET request to "/companies/1"
      Then the response code should be 200
      And response should contain json:
      """
        {
          "id":1,
          "name":"Herbert Smith Freehills",
          "logo_url":""
        }
      """

    Scenario: Test JSON id not found
      When I send a GET request to "/companies/111"
      Then the response code should be 404
      And response should contain json:
      """
        []
      """


