<?php

namespace Rabble\UserBundle\Builder;

use Rabble\AdminBundle\Builder\AdminBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class RabbleUserBuilder implements AdminBuilderInterface
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function build(string $env, InputInterface $input, OutputInterface $output): void
    {
        if ('prod' === $env) {
            return;
        }
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $output->writeln('Creating default user admin:admin');
        $application->run(new ArrayInput([
            'command' => 'rabble:user:create',
            'username' => 'admin',
            'password' => 'admin',
            'firstName' => 'Admin',
            'lastName' => 'Istrator',
            '--super-admin' => true,
            '--if-not-exists' => true,
            '--env' => $input->getOption('env'),
        ]), $output);
    }
}
