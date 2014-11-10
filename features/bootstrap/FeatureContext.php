<?php

use Behat\MinkExtension\Context\MinkContext;

use Notepads\Models\UserMapper;
use Notepads\Models\ServiceFactory;
use Notepads\Models\User;
use Behat\MinkExtension\Context\MinkAwareInterface;

define("BASE_PATH", dirname(dirname(__DIR__)) . "/src/app/");
define("VENDORS_PATH", dirname(dirname(__DIR__)) . "/vendor/");
require_once BASE_PATH . "/autoload.php";

require_once VENDORS_PATH . '/autoload.php';
require_once VENDORS_PATH . '/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{
    private $connection;
    private $userMapper;
    private $userService;
    private $registerError;
    private $loginResult;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @internal param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct()
    {
        $config = (object) parse_ini_file(BASE_PATH . '/config/config.ini');
        //TODO: config class to populate default values
        if (!isset($config->dbname) || $config->dbname == '') {
            $config->dbname = 'test';
        }
        $this->connection = new PDO("sqlite:" . BASE_PATH . "/../data/".$config->dbname.".sq3");
        $this->userMapper = new UserMapper($this->connection);
        $serviceFactory = new ServiceFactory($this->connection);
        $this->userService = $serviceFactory->getService("User");
    }

    /**
     * @Given /^There is the User class$/
     */
    public function thereIsTheUserClass()
    {
        return class_exists("User", false);
    }

    /**
     * @When /^I register a new user with name "([^"]*)" and password "([^"]*)"$/
     */
    public function iRegisterANewUserWithNameAndPassword($username, $password)
    {
        $result = $this->userService->register($username, $password, $password);
        if (!$result) {
            $errors = $this->userService->getErrors();
            $this->registerError = $errors[0];
            //echo "iRegisterANewUserWithNameAndPassword(): result is FALSE, error = {$this->registerError}\n";
            return false;
        }
        return true;
    }

    /**
     * @Then /^I should receive the error that the username given is wrong$/
     */
    public function iShouldReceiveTheErrorThatTheUsernameGivenIsWrong()
    {
        //echo "this->registerError = " . $this->registerError . "\n";
        return $this->registerError == "Invalid username or password!";
    }

    /**
     * @Given /^I should not have the user with username "([^"]*)" and password "([^"]*)" in the database$/
     */
    public function iShouldNotHaveTheUserWithUsernameAndPasswordInTheDatabase($username, $password)
    {
        return $this->connection->query(
            "SELECT * FROM `users` WHERE `username` = '$username' AND `password` = '$password'"
        );
    }

    /**
     * @Given /^I have the user "([^"]*)" with password "([^"]*)" registered in the database$/
     */
    public function iHaveTheUserWithPasswordRegisteredInTheDatabase($username, $password)
    {
        if (!$this->userMapper->findOne(new User(array(
            "username" => $username,
            "password" => $password
        )))) {
            return $this->userService->register($username, $password, $password);
        }
        return true;
    }

    /**
     * @When /^I login with username "([^"]*)" and password "([^"]*)"$/
     */
    public function iLoginWithUsernameAndPassword($username, $password)
    {
        $this->loginResult = $this->userService->authenticate($username, $password);
        return $this->loginResult;
    }

    /**
     * @Then /^I should receive a result for successfull login$/
     */
    public function iShouldReceiveAResultForSuccessfullLogin()
    {
        assertTrue($this->loginResult);
    }

    /**
     * @Given /^I don\'t have the user "([^"]*)" with password "([^"]*)" registered in the database$/
     */
    public function iDonTHaveTheUserWithPasswordRegisteredInTheDatabase($username, $password)
    {
        $user = new User(array(
            "username" => $username,
            "password" => $password
        ));
        if ($user = $this->userMapper->findOne($user)) {
            return $this->userMapper->delete($user);
        }
        return true;
    }

    /**
     * @Then /^I should reveive a result for not successfull login$/
     */
    public function iShouldReveiveAResultForNotSuccessfullLogin()
    {
        assertFalse($this->loginResult);
    }

    /**
     * @Given /^User "([^"]*)" does not exist$/
     */
    public function userDoesNotExist($username)
    {
        $user = new User(array("username" => $username));
        if ($user = $this->userMapper->findOne($user)) {
            $this->userMapper->delete($user);
            return true;
        } else {
            return true;
        }
    }

    /**
     * @Then /^I should have the user with username "([^"]*)" and password "([^"]*)" in the database$/
     */
    public function iShouldHaveTheUserWithUsernameAndPasswordInTheDatabase($username, $password)
    {
        return $this->userMapper->findOne(new User(array(
            "username" => $username,
            "password" => $password,
        )));
    }

    /**
     * @AfterScenario
     */
    public function cleanDB()
    {
        if ($user = $this->userMapper->findOne(new User(array(
            "username" => "testuser1",
        )))) {
            $this->userMapper->delete($user);
        }
        //
        if ($user = $this->userMapper->findOne(new User(array(
            "username" => "testuser2",
        )))) {
            $this->userMapper->delete($user);
        }
    }

    /**
     * @Given /^User "([^"]*)" with password "([^"]*)" exists$/
     */
    public function userWithPasswordExists($username, $password)
    {
        $user = new User(array(
            "username" => $username,
            "password" => $password,
        ));
        if (!$this->userMapper->findOne($user)) {
            return $this->userMapper->save($user);
        }
        return true;
    }

    /**
     * @Given /^the confirmation message should contain "([^"]*)"$/
     */
    public function theConfirmationMessageShouldContain($text)
    {
        return (false !== strpos($this->getSession()->getDriver()->getWebDriverSession()->getAlert_text(), $text));
    }

    /**
     * @Given /^I press ok on confirmation$/
     */
    public function iPressOkOnConfirmation()
    {
        $this->getSession()->getDriver()->getWebDriverSession()->accept_alert();
    }
}
