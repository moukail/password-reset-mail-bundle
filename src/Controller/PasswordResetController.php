<?php

namespace Moukail\PasswordResetMailBundle\Controller;

use Moukail\CommonToken\Controller\ControllerTrait;
use Moukail\CommonToken\Entity\TokenInterface;
use Moukail\CommonToken\Exception\ExceptionInterface;
use Moukail\CommonToken\HelperInterface;
use Moukail\CommonToken\Repository\UserRepositoryInterface;
use Moukail\PasswordResetMailBundle\Message\PasswordResetMail;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PasswordResetController extends AbstractController
{
    use ControllerTrait;

    private $helper;
    private $passwordEncoder;
    private $userRepository;
    private $validator;
    private $bus;
    /** @var ParameterBagInterface */
    private $params;

    public function __construct(HelperInterface $helper, UserRepositoryInterface $userRepository, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator, MessageBusInterface $bus, ParameterBagInterface $params)
    {
        $this->helper = $helper;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->validator = $validator;
        $this->bus = $bus;
        $this->params = $params;
    }

    public function request(Request $request): JsonResponse
    {
        $email = $this->emailValidation($request, $this->validator);

        try {
            /** @var TokenInterface $tokenEntity */
            $tokenEntity = $this->generateTokenEntity($email);
        } catch (ExceptionInterface $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getReason()
            ], Response::HTTP_OK);
        }

        $this->bus->dispatch(new PasswordResetMail($tokenEntity->getUser()->getEmail(), [
            'name' => $tokenEntity->getUser()->getLastName(),
            'frontend_url' => $this->params->get('moukail_password_reset.email_base_url'),
            'token' => $tokenEntity->getToken(),
        ]));

        return $this->json([
            'status' => 'success',
            'message' => 'success'
        ], Response::HTTP_OK);
    }

    public function reset(Request $request, string $token = null): Response
    {
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->helper->validateTokenAndFetchUser($token);
        } catch (ExceptionInterface $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getReason()
            ], Response::HTTP_OK);
        }

        // A password reset token should be used only once, remove it.
        $this->helper->removeTokenEntity($token);

        $plainPassword = $this->getPlainPasswordFromRequest($request);
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);

        $user->setPassword($encodedPassword);
        $this->getDoctrine()->getManager()->flush();

        return $this->json([
            'status' => 'success',
            'message' => 'success'
        ], Response::HTTP_OK);
    }

    private function getPlainPasswordFromRequest(Request $request): string
    {
        $plainPassword = $request->get('plainPassword');

        if (empty($plainPassword)) {
            /** @var string $content */
            $content = $request->getContent();

            /** @var array $data */
            $data = json_decode($content, true);
            $plainPassword = $data['plainPassword'];
        }

        return $plainPassword;
    }

}
