<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception as DoctrineException;
use Doctrine\DBAL\Tools\DsnParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:database:drop-if-exists',
    description: 'Drops the configured database if it exists without requiring the database to exist beforehand.',
)]
final class DropDatabaseIfExistsCommand extends Command
{
    public function __construct(private readonly Connection $connection)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force dropping the configured database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$input->getOption('force')) {
            $io->error('Dropping the database requires passing the --force option.');

            return Command::FAILURE;
        }

        $params = $this->connection->getParams();
        $databaseName = $this->connection->getDatabase();

        if ((null === $databaseName || '' === $databaseName) && isset($params['path'])) {
            $databaseName = (string) $params['path'];
        }

        if (null === $databaseName || '' === $databaseName) {
            $io->success('No database configured, skipping drop.');

            return Command::SUCCESS;
        }

        if ($this->isSqliteConnection($params)) {
            return $this->dropSqliteDatabase($databaseName, $io);
        }

        try {
            $dropConnection = $this->createServerLevelConnection($params);
            $platform = $dropConnection->getDatabasePlatform();
            $sql = sprintf('DROP DATABASE IF EXISTS %s', $platform->quoteIdentifier($databaseName));
            $dropConnection->executeStatement($sql);
            $dropConnection->close();
        } catch (DoctrineException $exception) {
            $io->error(sprintf('Failed to drop database "%s": %s', $databaseName, $exception->getMessage()));

            return Command::FAILURE;
        }

        $io->success(sprintf('Database "%s" dropped (if it existed).', $databaseName));

        return Command::SUCCESS;
    }

    /**
     * @param array<string, mixed> $params
     */
    private function createServerLevelConnection(array $params): Connection
    {
        if (isset($params['url'])) {
            $dsnParser = new DsnParser();
            $parsedUrlParams = $dsnParser->parse($params['url'], $params['driver'] ?? null);
            $params = array_merge($parsedUrlParams, $params);
            unset($params['url']);
        }

        unset($params['dbname'], $params['path'], $params['memory']);
        $params['dbname'] = null;

        return DriverManager::getConnection($params);
    }

    /**
     * @param array<string, mixed> $params
     */
    private function isSqliteConnection(array $params): bool
    {
        $driver = $params['driver'] ?? '';

        if (is_string($driver) && '' !== $driver) {
            return str_contains($driver, 'sqlite');
        }

        $url = $params['url'] ?? null;

        return is_string($url) && str_contains($url, 'sqlite');
    }

    private function dropSqliteDatabase(string $path, SymfonyStyle $io): int
    {
        if (!is_file($path)) {
            $io->success(sprintf('No SQLite database file "%s" found, nothing to drop.', $path));

            return Command::SUCCESS;
        }

        if (!@unlink($path)) {
            $io->error(sprintf('Failed to remove SQLite database file "%s".', $path));

            return Command::FAILURE;
        }

        $io->success(sprintf('SQLite database file "%s" removed.', $path));

        return Command::SUCCESS;
    }
}
