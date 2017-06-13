<?php

namespace AppBundle\Command;

use AppBundle\Database\Magento\CustomerSchemaReader;
use AppBundle\Database\Magento\Field;
use AppBundle\Database\Magento\SchemaReader;
use Doctrine\DBAL\Connection;
use function foo\func;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class AppSchemaReaderCommand extends ContainerAwareCommand
{

    /**
     * @var Connection
     */
    protected $conn;

    /**
     * @var CustomerSchemaReader
     */
    protected $schemaReader;

    protected function configure()
    {
        $this
            ->setName('app:schema:reader')
            ->setDescription('...');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $config = new Configuration();
        $connectionParams = [
            'dbname' => $this->getContainer()->getParameter('database_name'),
            'user' => $this->getContainer()->getParameter('database_user'),
            'password' => $this->getContainer()->getParameter('database_password'),
            'host' => $this->getContainer()->getParameter('database_host'),
            'port' => $this->getContainer()->getParameter('database_port'),
            'driver' => 'pdo_mysql',
        ];
        $this->conn = DriverManager::getConnection($connectionParams, $config);
        $this->schemaReader = new CustomerSchemaReader($this->conn, null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->userFilterFields($input, $output);
        $this->userAddFields($input, $output);
        $this->userMarkTags($input, $output);

        $output->writeln($this->schemaReader->getSelectFields());

//        dump($this->schemaReader->getFields());
    }

    protected function userFilterFields(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $this->schemaReader->filterFields(function (Field $field) use ($input, $output, $helper) {
            $question = new ConfirmationQuestion(
                "Keep field [<info>{$field->getName()}</info>] [<comment>yes</comment>] ? ",
                true
            );

            return $helper->ask($input, $output, $question);
        });
    }

    protected function userAddFields(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $addNewFieldQuestion = new ConfirmationQuestion(
            'Add a new field [<comment>no</comment>] ? ',
            false
        );
        while ($helper->ask($input, $output, $addNewFieldQuestion)) {
            $field = new Field(
                $helper->ask($input, $output, new Question('Field name ? ', '')),
                $helper->ask($input, $output, new Question('Select statement [value, name] (with @entityId) ? ', '')),
                $helper->ask($input, $output, new ChoiceQuestion('Type ? ', Field::TYPES, 0))
            );
            $this->schemaReader->addField($field);
        }
    }

    protected function userMarkTags(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $this->schemaReader->getFields()->each(function (Field $field) use ($input, $output, $helper) {
            $question = new ConfirmationQuestion(
                "<info>{$field->getName()}</info> is a tag [<comment>no</comment>] ? ",
                false
            );
            $field->setIsTag($helper->ask($input, $output, $question));
        });
    }

}
