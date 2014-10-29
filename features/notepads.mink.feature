Feature: Add, Remove and Delete Notepads in Members' area

  @jenkins_remote
  Scenario: Add New Notepad
    Given I am on the homepage
    And I follow "Login"
    And I fill in "username" with "testu1"
    And I fill in "password" with "testp1"
    And I press "Login"
    And I am on "/members"
    And I follow "Add new"
    And I fill in "name" with "test notepad from Behat"
    And I fill in "text" with "test text"
    And I press "Add"
    Then I should see "Notepad saved!"

  @jenkins_remote
    Scenario: Edit Existing Notepad
      Given I am on the homepage
      And I follow "Login"
      And I fill in "username" with "testu1"
      And I fill in "password" with "testp1"
      And I press "Login"
      And I am on "/members"
      And I follow "Edit 'test notepad from Behat'"
      And I fill in "name" with "test notepad edit"
      And I fill in "text" with "test notepad edit from Behat"
      And I press "Update"
      Then I should see "Notepad saved!"

  @javascript
  Scenario: Delete Existing Notepad
    Given I am on the homepage
    And I follow "Login"
    And I fill in "username" with "testu1"
    And I fill in "password" with "testp1"
    And I press "Login"
    And I am on "/members"
    And I follow "Delete 'test notepad edit'"
    And the confirmation message should contain "Are you sure you want to delete this notepad?"
    And I press ok on confirmation
    Then I should be on "/dashboard"
    And I should not see "test notepad edit"
