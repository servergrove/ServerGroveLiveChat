<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sensio\Bundle\GeneratorBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Command\Command;
use Sensio\Bundle\GeneratorBundle\Generator\CrudGenerator;

/**
 * Generates a CRUD for a Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class GenerateDoctrineCrudCommand extends Command
{
    private $container;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('entity', InputArgument::REQUIRED, 'The entity class name to initialize (shortcut notation)'),
                new InputOption('format', '', InputOption::VALUE_REQUIRED, 'Use the format for configuration files (php, xml, yml, or , annotation)', null),
            ))
            ->setDescription('Generates a CRUD based on a Doctrine entity')
            ->setHelp(<<<EOT
The <info>doctrine:generate:crud</info> command generates a CRUD based on a Doctrine entity.

<info>./app/console doctrine:generate:crud AcmeBlogBundle:Post</info>
EOT
            )
            ->setName('doctrine:generate:crud')
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container = $this->getApplication()->getKernel()->getContainer();

        $entity = str_replace('/', '\\', $input->getArgument('entity'));

        if (false === $pos = strpos($entity, ':')) {
            throw new \InvalidArgumentException(sprintf('The entity name must contain a : ("%s" given, expecting something like AcmeBlogBundle:Blog/Post)', $entity));
        }

        $bundle = substr($entity, 0, $pos);
        $entity = substr($entity, $pos + 1);
        $entityFQ = $this->container->get('doctrine')->getEntityNamespace($bundle).'\\'.$entity;
        $bundle = $this->getApplication()->getKernel()->getBundle($bundle);
        $format = $input->getOption('format');

        $filesystem = $this->container->get('filesystem');
        $generator = new CrudGenerator($filesystem, __DIR__.'/../Resources/skeleton/crud');

        $vars = array(
        );

        $generator->generate($bundle, $entity, $input->getOption('format'), $vars);
    }
}
