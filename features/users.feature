Feature: Users
  In order to use the Notepads
  As a website user
  I need to be able to register and login

  @jenkins_remote
  Scenario: Register a new User
    Given I don't have the user "testuser1" with password "testpassword1" registered in the database
    When I register a new user with name "testuser1" and password "testpassword1"
    Then I should have the user with username "testuser1" and password "testpassword1" in the database

  @jenkins_remote
  Scenario: Register a new User with wrong data
    Given There is the User class
    When I register a new user with name ")@(#&$)@#" and password "testpassword1"
    Then I should receive the error that the username given is wrong
    And I should not have the user with username ")@(#&$)@#" and password "testpassword1" in the database

  @jenkins_remote
  Scenario: Login with an existing User
    Given I have the user "testuser2" with password "testpassword2" registered in the database
    When I login with username "testuser2" and password "testpassword2"
    Then I should receive a result for successfull login

  @jenkins_remote
  Scenario: Try to Login with a NOT existing User
    Given I don't have the user "nonexistinguser" with password "testpassword1" registered in the database
    When I login with username "nonexistinguser" and password "testpassword1"
    Then I should reveive a result for not successfull login
