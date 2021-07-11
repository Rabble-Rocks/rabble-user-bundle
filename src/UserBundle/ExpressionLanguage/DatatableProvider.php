<?php

namespace Rabble\UserBundle\ExpressionLanguage;

use Rabble\DatatableBundle\ExpressionLanguage\VariableProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DatatableProvider implements ExpressionFunctionProviderInterface, VariableProviderInterface
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getVariables(): array
    {
        return [
            'TokenStorage' => $this->tokenStorage,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new ExpressionFunction('get_user', function () {
                return '(null === $TokenStorage->getToken() ? null : $TokenStorage->getToken()->getUser())';
            }, function () {
                return null === $this->tokenStorage->getToken() ? null : $this->tokenStorage->getToken()->getUser();
            }),
        ];
    }
}
