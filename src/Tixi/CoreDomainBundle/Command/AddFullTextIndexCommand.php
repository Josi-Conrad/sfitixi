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

class AddFullTextIndexCommand extends ContainerAwareCommand {
    public function configure() {
        $this->setName('project:build-fulltext');
        $this->setDescription('Inserts FulltextSearch Indexes for address table');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $connection = $this->getContainer()->get('entity_manager')->getConnection();
        $connection->query("
        ALTER TABLE `address` ADD FULLTEXT `address_fts_idx` (`name`, `street`, `postalCode`, `city`, `country`, `type`);
        ");
    }
}