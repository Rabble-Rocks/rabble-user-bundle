<?php

namespace Rabble\UserBundle\Search;

use Doctrine\ORM\EntityManagerInterface;
use Rabble\AdminBundle\Search\SearchProviderInterface;
use Rabble\AdminBundle\Search\SearchResult;
use Rabble\UserBundle\Entity\User;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserSearchProvider implements SearchProviderInterface
{
    private EntityManagerInterface $entityManager;

    private RouterInterface $router;

    private AuthorizationCheckerInterface $checker;

    private TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        AuthorizationCheckerInterface $checker,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->checker = $checker;
        $this->translator = $translator;
    }

    /**
     * @param $query
     *
     * @return SearchResult[]
     */
    public function search($query): array
    {
        if (!$this->checker->isGranted('user.view')) {
            return [];
        }
        $query = strtolower($query);
        $users = $this->entityManager->createQueryBuilder()
            ->select('user')
            ->from($this->entityManager->getRepository('User')->getClassName(), 'user')
            ->where('user.username LIKE :query')
            ->orWhere("CONCAT(CONCAT(user.firstName, ' '), user.lastName) LIKE :query")
            ->setParameters([
                'query' => '%'.addcslashes($query, '%_').'%',
            ])
            ->setMaxResults(10)
            ->getQuery()->getResult();
        $matches = [];
        /** @var User $user */
        foreach ($users as $user) {
            similar_text(strtolower($user->getUsername()), $query, $score1);
            similar_text(strtolower($fullName = trim($user->getFirstName().' '.$user->getLastName())), $query, $score2);
            $score = ($score1 + $score2 * 0.99) / 2;
            $result = new SearchResult(
                $fullName,
                $this->translator->trans('search.type.user', [], 'RabbleUserBundle'),
                $this->router->generate('rabble_admin_user_view', ['user' => $user->getId()]),
                $score / 100,
                '@RabbleUser/SearchResult/user.html.twig'
            );
            $result->addExtra('user', $user);
            $matches[] = $result;
        }

        return $matches;
    }
}
