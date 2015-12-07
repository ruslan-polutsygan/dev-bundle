<?php

namespace RP\DevBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserAuthenticator implements SimplePreAuthenticatorInterface
{
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $user = $userProvider->loadUserByUsername($token->getUsername());
        if (!$user) {
            throw new AuthenticationException('User not found');
        }

        return new PreAuthenticatedToken($user, $token->getUsername(), $providerKey, $user->getRoles());
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    public function createToken(Request $request, $providerKey)
    {
        if ($request->headers->has('X-Test-Auth-Username')) {
            return new PreAuthenticatedToken($request->headers->get('X-Test-Auth-Username'), 'password', $providerKey);
        }

        throw new AuthenticationException('Not allowed');
    }
}
