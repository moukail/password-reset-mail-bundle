<?php

namespace App\Tests\api;

use App\Entity\User;
use App\Tests\ApiTester;
use Codeception\Scenario;
use Codeception\Util\HttpCode;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordResetCest
{
    /** @var \Faker\Generator */
    private $faker;

    /** @var UserPasswordEncoderInterface $encoder */
    private $encoder;

    public function beforeSuite(ApiTester $I)
    {
        $this->faker = \Faker\Factory::create('nl_NL');
        $this->encoder = $I->grabService('security.password_encoder');
    }

    public function _before(ApiTester $I)
    {

    }

    public function _after(ApiTester $I)
    {

    }


    /**
     * @param ApiTester $I
     * @param Scenario $scenario
     * @throws \Exception
     * @group student
     */
    public function testRequestResetPassword(ApiTester $I, Scenario $scenario)
    {
        //$scenario->skip('not yet');

        /** @var \App\Entity\User $user */
        $user = $I->have(User::class, ['active' => true]);

        $I->sendPOST('/v1/password/reset-request', ['email' => $user->getEmail()]);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContains('{"status":"success"}');
        //$I->seeEmailIsSent(1);

        //$I->dontSeeQueueIsEmpty('messages');
        //$I->seeNumberOfMessagesInQueue('messages', 1);
        //$message = $I->grabMessageFromQueue('messages');

        // Query MailHog and fetch all available emails
        $I->fetchEmails();

        // This is optional, but will filter the emails in case you're sending multiple emails or use the BCC field
        $I->accessInboxFor($user->getEmail());

        // A new email should be available and it should be unread
        $I->haveEmails();
        //$I->haveUnreadEmails();
        $I->haveNumberOfUnreadEmails(1);

        // Set the next unread email as the email to perform operations on
        $I->openNextUnreadEmail();

        // Validate the content of the opened email, all of these operations are performed on the same email
        $I->seeInOpenedEmailSubject('Reset your password');
        //$I->seeInOpenedEmailBody('Thank you for registering as a partner of Jules! We have received your registration and will review your details shortly.');
        $I->seeInOpenedEmailRecipients($user->getEmail());

        // After opening the only available email, the unread inbox should be empty
        $I->dontHaveUnreadEmails();
    }

    /**
     * @param ApiTester $I
     * @param Scenario $scenario
     */
    public function testResetPassword(ApiTester $I, Scenario $scenario)
    {
        $scenario->skip('not yet');
    }
}
