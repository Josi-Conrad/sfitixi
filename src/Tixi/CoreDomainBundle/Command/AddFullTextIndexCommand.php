<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 29.03.14
 * Time: 17:53
 */

namespace Tixi\CoreDomainBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AddFullTextIndexCommand
 * @package Tixi\CoreDomainBundle\Command
 */
class AddFullTextIndexCommand extends ContainerAwareCommand {
    public function configure() {
        $this->setName('project:build-fulltext');
        $this->setDescription('Inserts FulltextSearch Indexes for address table');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        $connection = $this->getContainer()->get('entity_manager')->getConnection();
        $connection->query("
        ALTER TABLE `address` ADD FULLTEXT `address_fts_idx` (`name`, `street`, `postalCode`, `city`, `country`, `type`);
        ");

        $output->writeln('Alter address table for fulltext search executed');
    }
}