<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }

    /**
     * @Given /^it is day phase$/
     */
    public function itIsDayPhase()
    {
        throw new PendingException();
    }

    /**
     * @Given /^people are asleep$/
     */
    public function peopleAreAsleep()
    {
        throw new PendingException();
    }

    /**
     * @Then /^wake people up$/
     */
    public function wakePeopleUp()
    {
        throw new PendingException();
    }

    /**
     * @Given /^ask people to discuss$/
     */
    public function askPeopleToDiscuss()
    {
        throw new PendingException();
    }

    /**
     * @Given /^accept nominations$/
     */
    public function acceptNominations()
    {
        throw new PendingException();
    }

    /**
     * @Given /^we have received a nomination$/
     */
    public function weHaveReceivedANomination()
    {
        throw new PendingException();
    }

    /**
     * @Then /^ask for silence$/
     */
    public function askForSilence()
    {
        throw new PendingException();
    }

    /**
     * @Given /^ask for a seconder$/
     */
    public function askForASeconder()
    {
        throw new PendingException();
    }

    /**
     * @Given /^accept seconders$/
     */
    public function acceptSeconders()
    {
        throw new PendingException();
    }

    /**
     * @Given /^we have a nomination$/
     */
    public function weHaveANomination()
    {
        throw new PendingException();
    }

    /**
     * @Given /^we have a second$/
     */
    public function weHaveASecond()
    {
        throw new PendingException();
    }

    /**
     * @Then /^ask the nominator to give their reasoning$/
     */
    public function askTheNominatorToGiveTheirReasoning()
    {
        throw new PendingException();
    }

    /**
     * @Given /^the nominator has finished their argument$/
     */
    public function theNominatorHasFinishedTheirArgument()
    {
        throw new PendingException();
    }

    /**
     * @Then /^ask the seconder to give their reasoning$/
     */
    public function askTheSeconderToGiveTheirReasoning()
    {
        throw new PendingException();
    }

    /**
     * @Given /^the seconder has finished their argument$/
     */
    public function theSeconderHasFinishedTheirArgument()
    {
        throw new PendingException();
    }

    /**
     * @Then /^ask the defender to give their defense$/
     */
    public function askTheDefenderToGiveTheirDefense()
    {
        throw new PendingException();
    }

    /**
     * @Given /^the defender has finished their defense$/
     */
    public function theDefenderHasFinishedTheirDefense()
    {
        throw new PendingException();
    }

    /**
     * @Then /^Ask people to vote$/
     */
    public function askPeopleToVote()
    {
        throw new PendingException();
    }

    /**
     * @Given /^Accept votes$/
     */
    public function acceptVotes()
    {
        throw new PendingException();
    }

    /**
     * @Given /^the village has reached a consensus to lynch$/
     */
    public function theVillageHasReachedAConsensusToLynch()
    {
        throw new PendingException();
    }

    /**
     * @Then /^kill the accused$/
     */
    public function killTheAccused()
    {
        throw new PendingException();
    }

    /**
     * @Given /^put everyone to sleep$/
     */
    public function putEveryoneToSleep()
    {
        throw new PendingException();
    }

    /**
     * @Given /^go to night phase$/
     */
    public function goToNightPhase()
    {
        throw new PendingException();
    }

    /**
     * @Given /^the village has reached a consensus to not lynch$/
     */
    public function theVillageHasReachedAConsensusToNotLynch()
    {
        throw new PendingException();
    }

    /**
     * @Then /^clear nomination and second$/
     */
    public function clearNominationAndSecond()
    {
        throw new PendingException();
    }

    /**
     * @Given /^it is the wolf night phase$/
     */
    public function itIsTheWolfNightPhase()
    {
        throw new PendingException();
    }

    /**
     * @Given /^the wolves are asleep$/
     */
    public function theWolvesAreAsleep()
    {
        throw new PendingException();
    }

    /**
     * @Then /^wake the wolves$/
     */
    public function wakeTheWolves()
    {
        throw new PendingException();
    }

    /**
     * @Given /^ask the wolves to vote$/
     */
    public function askTheWolvesToVote()
    {
        throw new PendingException();
    }

    /**
     * @Then /^accept votes from wolves$/
     */
    public function acceptVotesFromWolves()
    {
        throw new PendingException();
    }

    /**
     * @Given /^the wolves have reached a consensus$/
     */
    public function theWolvesHaveReachedAConsensus()
    {
        throw new PendingException();
    }

    /**
     * @Then /^put the wolves to sleep$/
     */
    public function putTheWolvesToSleep()
    {
        throw new PendingException();
    }

    /**
     * @Given /^move to the next game phase$/
     */
    public function moveToTheNextGamePhase()
    {
        throw new PendingException();
    }

    /**
     * @Given /^the live demo does actually work$/
     */
    public function theLiveDemoDoesActuallyWork()
    {
        echo "Of course we work!";
        return true;
    }

    /**
     * @Then /^we should get cheers$/
     */
    public function weShouldGetCheers()
    {
        echo "WOOOOOOOOO!!!!!! *Clap*";
        return true;
    }

}
