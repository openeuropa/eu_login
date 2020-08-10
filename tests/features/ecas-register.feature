@javascript @ecas-login
Feature: Register through OE Authentication
  In order to be able to have new users
  As an anonymous user of the system
  I need to be able to go to the registration URL

  Scenario: Register
    Given I am an anonymous user
    When I visit "the user registration page"
    # Redirected to the EU Login mockup server.
    Then I should see "Create an account"
