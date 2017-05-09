<?php

namespace AppBundle\Security;

use AppBundle\Exception\JsonHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiTokenAuthenticator extends AbstractGuardAuthenticator
{
    public function getCredentials(Request $request)
    {
        return $request->headers->get('X-AUTH-TOKEN');
    }

    /**
     * Remove SuppressWarnings after method will fully implemented.
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $userProvider->loadUserByUsername($credentials);

        if (!$user) {
            throw new JsonHttpException(400, 'Bad credentials.');
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
        throw new JsonHttpException(400, 'Bad credentials.');
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
        throw new JsonHttpException(401, 'Authentication required.');
    }
}
