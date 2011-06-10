<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sensio\Bundle\GeneratorBundle\Generator;

use Symfony\Component\HttpKernel\Util\Filesystem;

/**
 * Generates a bundle.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CrudGenerator extends Generator
{
    private $filesystem;
    private $skeletonDir;

    public function __construct(Filesystem $filesystem, $skeletonDir)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
        $this->skeletonDir = $skeletonDir;
    }

    public function generate($bundle, $entity, $format, $vars)
    {
        $parts = explode('/', $entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $dir = $bundle->getPath();

        $target = $dir.'/Controller/'.str_replace('\\', '/', $entity).'Controller.php';
        if (file_exists($target)) {
            throw new \RuntimeException('Unable to generate the controller as it already exists.');
        }
        $this->filesystem->copy($this->skeletonDir.'/controller.php', $target);

//print $bundle->getPath().'/Resources/views/'.str_replace('\\', '/', $entity).'/'."\n";

        $this->renderFile($target, array_merge(array(
            'dir'              => $this->skeletonDir,
            'bundle'           => $bundle->getName(),
            'entity'           => $entity,
            'namespace'        => $bundle->getNamespace(),
            'entity_class'     => $entityClass,
            'entity_namespace' => $entityNamespace,
            'format'           => $format,
        ), $vars));
    }
}
