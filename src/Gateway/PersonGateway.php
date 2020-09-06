<?php
declare(strict_types=1);

namespace Api\Gateway;

use Api\System\Database\Connection;
use PDO;
use PDOException;

class PersonGateway
{
    /**
     * @var Connection
     */
    private Connection $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function findAll(): array {
        $statement = <<<SQL
            SELECT 
                person_id,
                firstname,
                lastname,
                first_parent_id,
                second_parent_id
            FROM public.person
            SQL;
        try {
            $statement = $this->connection->open()->query($statement);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    public function find(int $id): ?array {
        $statement = <<<SQL
            SELECT 
                person_id,
                firstname,
                lastname,
                first_parent_id,
                second_parent_id
            FROM public.person
            WHERE person_id = :personId
            SQL;
        try {
            $statement = $this->connection->open()->prepare($statement);
            $statement->bindParam('personId', $id);
            if (!$statement->execute()) {
                return null;
            }
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            return !$result ? null : $result;
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    public function create(array $data): array {
        $statement = sprintf(
            'INSERT INTO public.person(%s) VALUES (%s) RETURNING *',
            implode(', ', array_keys($data)),
            implode(
                ', ',
                array_map(
                    function ($key): string {
                        return ':' . $key;
                    },
                    array_keys($data)
                )
            )
        );
        try {
            $statement = $this->getConnection()->open()->prepare($statement);
            foreach ($data as $col => &$value) {
                $statement->bindParam($col, $value);
            }
            if (!$statement->execute()) {
                throw new \RuntimeException('Person not found');
            }

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    public function update(int $id, array $data): array {
        $statement = sprintf(
            'UPDATE public.person SET %s WHERE person_id = :personId',
            implode(
                ', ',
                array_map(
                    function (string $key): string {
                        return $key . ' = :' . $key;
                    },
                    array_keys($data)
                )
            )
        );
        try {
            $statement = $this->connection->open()->prepare($statement);
            $statement->bindParam('personId', $id);
            foreach ($data as $col => &$value) {
                $statement->bindParam($col, $value);
            }

            if (false === $statement->execute()) {
                throw new PDOException('Update was not successful');
            }

            return $this->find((int)$id);
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    public function delete(int $id): bool {
        try {
            $statement = $this->connection->open()
                ->prepare('DELETE FROM public.person WHERE person_id = :personId');
            $statement->bindParam('personId', $id);
            if (false === $statement->execute()) {
                throw new PDOException('Could not delete');
            }

            return true;
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    /**
     * @return Connection
     */
    private function getConnection(): Connection
    {
        return $this->connection;
    }
}