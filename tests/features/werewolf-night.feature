Feature: The werewolf night phase works

  Scenario: Waking the wolves up
    Given it is the wolf night phase
    And the wolves are asleep
    Then wake the wolves
    And ask the wolves to vote

  Scenario: Accept wolves voting
    Given it is the wolf night phase
    Then accept votes from wolves

  Scenario: Wolves have voted
    Given it is the wolf night phase
    And the wolves have reached a consensus
    Then put the wolves to sleep
    And move to the next game phase