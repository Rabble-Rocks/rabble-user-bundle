<?php

namespace Rabble\UserBundle\Command;

use Rabble\UserBundle\Entity\User;
use Rabble\UserBundle\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'rabble:user:create';

    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
        parent::__construct();
    }

    public function configure()
    {
        $this->addArgument('username', InputArgument::REQUIRED);
        $this->addArgument('password', InputArgument::REQUIRED);
        $this->addArgument('firstName', InputArgument::OPTIONAL);
        $this->addArgument('lastName', InputArgument::OPTIONAL);
        $this->addOption('super-admin', null, InputOption::VALUE_OPTIONAL, 'Set this option if you want the created usser to be a super admin', false);
        $this->addOption('if-not-exists', null, InputOption::VALUE_NONE, 'Skip creating the user if it already exists');
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->userRepository;
        $userClass = $repository->getClassName();
        if ($input->getOption('if-not-exists') && null !== $repository->findOneBy(['username' => $input->getArgument('username')])) {
            $output->writeln(sprintf('<info>User <comment>%s</comment> already exists. Skipped.</info>', $input->getArgument('username')));

            return 0;
        }
        /** @var User $user */
        $user = new $userClass();
        $user->setUsername($input->getArgument('username'));
        $user->setPassword($input->getArgument('password'));
        $helper = $this->getHelper('question');
        $firstName = $input->getArgument('firstName') ?? $helper->ask($input, $output, new Question('First name [John]: ', 'John'));
        $lastName = $input->getArgument('lastName') ?? $helper->ask($input, $output, new Question('Last name [Doe]: ', 'Doe'));
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $superAdmin = $input->getOption('super-admin');
        if (null === $superAdmin || '1' === $superAdmin) {
            $user->setSuperAdmin(true);
        }
        $repository->save($user);
        $output->writeln(sprintf('<info>Successfully created user <comment>%s</comment>.</info>', $input->getArgument('username')));

        return 0;
    }
}
