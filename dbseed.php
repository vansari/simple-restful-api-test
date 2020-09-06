<?php
require_once __DIR__ . '/bootstrap.php';

$createTableSql = <<<SQL
CREATE TABLE IF NOT EXISTS public.person (
    person_id INT NOT NULL AUTO_INCREMENT,
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    first_parent_id INT DEFAULT NULL,
    second_parent_id INT DEFAULT NULL,
    PRIMARY KEY (person_id),
    FOREIGN KEY (first_parent_id)
        REFERENCES public.person(person_id)
        ON DELETE SET NULL,
    FOREIGN KEY (second_parent_id)
        REFERENCES public.person(person_id)
        ON DELETE SET NULL
);
SQL;

$insertSql = <<<SQL
INSERT INTO public.person(person_id, firstname, lastname, first_parent_id, second_parent_id)
VALUES
    (1, 'Mustermann', 'Max', null,  null),
    (2, 'Maustermann', 'Maria', null, null),
    (3, 'Mustermann', 'Tom', 1, 2),
    (4, 'Jane', 'Smith', null, null),
    (5, 'John', 'Smith', null, null),
    (6, 'Richard', 'Smith', 4, 5),
    (7, 'Donna', 'Smith', 4, 5),
    (8, 'Josh', 'Harrelson', null, null),
    (9, 'Anna', 'Harrelson', 7, 8);
SQL;

try {
    $tableResult = $connection->open()->exec($createTableSql);
    if (false === $tableResult) {
        throw new PDOException('CREATE fehlgeschlagen');
    }
    $insertResult = $connection->open()->exec($insertSql);
    if (false === $tableResult) {
        throw new PDOException('INSERT fehlgeschlagen');
    }
    echo "Success!" . PHP_EOL;
} catch (PDOException $exception) {
    exit($exception->getMessage());
}