Feature: Users - Mink
  In order to use the Notepads
  As a website user
  I need to be able to register and login

  @jenkins_remote
  Scenario: Register a new User
    Given I am on the homepage
    And User "testuser1" does not exist
    When I follow "Register"
    And I fill in "username" with "testuser1"
    And I fill in "password" with "testpassword1"
    And I fill in "passwordconfirm" with "testpassword1"
    And I press "Register"
    Then I should be on "/register"
    And I should see "User successfully registered!"

  @jenkins_remote
  Scenario: Register a new User with wrong username
    Given I am on the homepage
    When I follow "Register"
    And I fill in "username" with ")@(#&$)@#"
    And I fill in "password" with "testpassword1"
    And I fill in "passwordconfirm" with "testpassword1"
    And I press "Register"
    Then I should see "Invalid username or password!"

  @jenkins_remote
  Scenario: Register a new User with wrong password
    Given I am on the homepage
    When I follow "Register"
    And I fill in "username" with "testusername1"
    And I fill in "password" with "1"
    And I fill in "passwordconfirm" with "1"
    And I press "Register"
    Then I should see "Invalid username or password!"

  @jenkins_remote
  Scenario: Login with an existing User
    Given I am on the homepage
    And User "testuser1" with password "testpassword1" exists
    When I follow "Login"
    And I fill in "username" with "testuser1"
    And I fill in "password" with "testpassword1"
    And I press "Login"
    Then I should be on "/dashboard"
    And I should see "Welcome to the Members Area!"

  @jenkins_remote
  Scenario: Try to Login with a NOT existing User
    Given I am on the homepage
    And User "notexistinguser" does not exist
    When I follow "Login"
    And I fill in "username" with "notexistinguser"
    And I fill in "password" with "password1"
    And I press "Login"
    Then I should see "Wrong user!"

  @jenkins_remote
  Scenario: Try to Login with a bad username
    Given I am on the homepage
    When I follow "Login"
    And I fill in "username" with "#($%*)#"
    And I fill in "password" with "password1"
    And I press "Login"
    Then I should see "Invalid username '#($%*)#' given!"

  @jenkins_remote
  Scenario: Try to Login with a bad password
    Given I am on the homepage
    When I follow "Login"
    And I fill in "username" with "testusername1"
    And I fill in "password" with "1"
    And I press "Login"
    Then I should see "Invalid password '1' given!"
