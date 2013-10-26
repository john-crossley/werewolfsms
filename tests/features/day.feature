Feature: The sun is high in the sky!

  Scenario: Discussion
    Given it is day phase
    And people are asleep
    Then wake people up
    And tell people who has died (possibly noone)
    And ask people to discuss
    And accept nominations

  Scenario: Nominations
    Given it is day phase
    And we have received a nomination
    Then ask for silence
    And ask for a seconder
    And accept seconders

  Scenario: Nominator's argument
    Given it is day phase
    And we have a nomination
    And we have a second
    Then ask the nominator to give their reasoning

  Scenario: Seconder's argument
    Given it is day phase
    And we have a nomination
    And we have a second
    And the nominator has finished their argument
    Then ask the seconder to give their reasoning

  Scenario: Accused's defense
    Given it is day phase
    And we have a nomination
    And we have a second
    And the nominator has finished their argument
    And the seconder has finished their argument
    Then ask the defender to give their defense

  Scenario: Voting
    Given it is day phase
    And we have a nomination
    And we have a second
    And the nominator has finished their argument
    And the seconder has finished their argument
    And the defender has finished their defense
    Then Ask people to vote
    And Accept votes

  Scenario: Lynch!
    Given it is day phase
    And we have a nomination
    And we have a second
    And the nominator has finished their argument
    And the seconder has finished their argument
    And the defender has finished their defense
    And the village has reached a consensus to lynch
    Then kill the accused
    And put everyone to sleep
    And go to night phase

  Scenario: No Lynch!
    Given it is day phase
    And we have a nomination
    And we have a second
    And the nominator has finished their argument
    And the seconder has finished their argument
    And the defender has finished their defense
    And the village has reached a consensus to not lynch
    Then clear nomination and second
    And ask people to discuss
    And accept nominations