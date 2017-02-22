<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiTokenAuthenticator extends AbstractGuardAuthenticator
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getCredentials(Request $request)
    {
        return $request->headers->get('X-TOKEN');
    }

    /**
     * Remove SuppressWarnings after method will fully implemented.
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var User $user */
        $user = $this->em->getRepository('AppBundle:User')
            ->findOneBy(array('apiToken' => $credentials));
        if (!$user) {
            throw new AuthenticationCredentialsNotFoundException();
        }

        return $user;
    }

    /**
     * Remove SuppressWarnings after method will fully implemented.
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * Remove SuppressWarnings after method will fully implemented.
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(
            array('message' => $exception->getMessageKey()),
            403
        );
    }

    /**
     * Remove SuppressWarnings after method will fully implemented.
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return;
    }

    public function supportsRememberMe()
    {
        return false;
    }

    /**
     * Remove SuppressWarnings after method will fully implemented.
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse(
            array('message' => 'Authentication required'),
            401
        );
    }
}
