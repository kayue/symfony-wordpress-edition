<?php

namespace Acme\DemoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class WordpressCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('demo:wordpress:install')
            ->setDescription('Install WordPress')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (1 === $this->getApplication()->find('doctrine:database:create')->run(
                new ArrayInput(['command' => 'doctrine:database:create', '--connection'=>'wordpress']),
                $output
            )) {
            return 1;
        }

        $fs = new Filesystem();
        $fs->mkdir($this->getWordpressRoot(), 0755);

        if (false === $fs->exists($this->getWordpressRoot().DIRECTORY_SEPARATOR.'wp-cli.phar')) {
            $this->runCommand('curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar');
            $this->runCommand('php wp-cli.phar core download');
        }

        if ($fs->exists($this->getWordpressRoot().DIRECTORY_SEPARATOR.'wp-config.php')) {
            $fs->remove($this->getWordpressRoot().DIRECTORY_SEPARATOR.'wp-config.php');
        }

        $this->runCommand(
            sprintf('php wp-cli.phar core config --dbname=%s --dbuser=%s',
                $this->getContainer()->getParameter('wordpress_database_name'),
                $this->getContainer()->getParameter('wordpress_database_user')
            )
        );

        $this->runCommand(
            sprintf('php wp-cli.phar core install --url=%s --title=Demo --admin_user=admin --admin_password=admin --admin_email=admin@example.com',
                $this->getContainer()->getParameter('wordpress_url')
            )
        );

        $this->runCommand('php wp-cli.phar plugin install wordpress-importer --activate');

        if (false === $fs->exists($this->getWordpressRoot().DIRECTORY_SEPARATOR.'wptest.xml')) {
            $this->runCommand('curl -OL https://raw.githubusercontent.com/manovotny/wptest/master/wptest.xml');
        }

        $this->runCommand('php wp-cli.phar import wptest.xml --authors=create --skip=attachment');

        $this->runCommand('php wp-cli.phar core multisite-convert');
        $this->runCommand('php wp-cli.phar site create --slug=foo');
        $this->runCommand('php wp-cli.phar site create --slug=bar');

        return 0;
    }

    protected function runCommand($command)
    {
        (new Process($command, $this->getWordpressRoot()))->run(function ($type, $buffer) {
            echo $buffer;
        });
    }

    protected function getWordpressRoot()
    {
        return 'web/wordpress';
    }
} 
