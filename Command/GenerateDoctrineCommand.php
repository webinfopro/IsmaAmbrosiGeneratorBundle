<?php

namespace IsmaAmbrosi\Bundle\GeneratorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;

/**
 * Class GenerateDoctrineCommand
 *
 * @author Ismael Ambrosi<ismaambrosi@gmail.com>
 */
abstract class GenerateDoctrineCommand extends ContainerAwareCommand
{

    /**
     * @param string $shortcut
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function parseShortcutNotation($shortcut)
    {
        $document = str_replace('/', '\\', $shortcut);

        if (false === $pos = strpos($document, ':')) {
            throw new \InvalidArgumentException(sprintf('The document name must contain a : ("%s" given, expecting something like AcmeBlogBundle:Blog/Post)', $document));
        }

        return array(substr($document, 0, $pos), substr($document, $pos + 1));
    }

    /**
     * @param string $name
     *
     * @return \Symfony\Component\HttpKernel\Bundle\BundleInterface
     */
    protected function getBundle($name)
    {
        return $this->getKernel()->getBundle($name);
    }

    /**
     * @return \Symfony\Component\HttpKernel\KernelInterface
     */
    protected function getKernel()
    {
        return $this->getContainer()->get('kernel');
    }

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function getDocumentManager()
    {
        return $this->getContainer()->get('doctrine.odm.mongodb.document_manager');
    }

    /**
     * @param string $bundle
     *
     * @return string
     */
    protected function getDocumentNamespace($bundle)
    {
        return $this->getBundle($bundle)->getNamespace().'\\Document';
    }

    /**
     * @param $documentClass
     *
     * @return \Doctrine\ODM\MongoDB\Mapping\ClassMetadata
     */
    protected function getDocumentMetadata($documentClass)
    {
        $factory = new \Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory();
        $factory->setDocumentManager($this->getDocumentManager());
        $factory->setConfiguration($this->getDocumentManager()->getConfiguration());

        $metadata = $factory->getMetadataFor($documentClass);

        return $metadata;
    }

    /**
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    protected function getFilesystem()
    {
        return $this->getContainer()->get('filesystem');
    }

    /**
     * @return \Symfony\Component\Console\Helper\FormatterHelper
     */
    protected function getFormatter()
    {
        return $this->getHelper('formatter');
    }

    /**
     * @return DialogHelper
     */
    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }
}
