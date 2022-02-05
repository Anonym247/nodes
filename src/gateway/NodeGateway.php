<?php

namespace Src\Gateway;

use PDO;
use PDOException;

class NodeGateway
{
    /**
     * @var PDO
     */
    private PDO $database;

    /**
     * @param $database
     */
    public function __construct($database)
    {
        $this->database = $database;
    }

    /**
     * @return array|false|void
     */
    public function findAll()
    {
        $statement = "SELECT * FROM nodes.nodes;";

        try {
            $statement = $this->database->query($statement);

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * @param int $id
     * @return array
     */
    public function find(int $id): array
    {
        $id = htmlspecialchars(strip_tags($id));
        $statement = "SELECT * from nodes.nodes WHERE id = :id;";

        try {
            $statement = $this->database->prepare($statement);
            $statement->execute(['id' => $id]);

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    /**
     * @return bool
     */
    public function hasRoot(): bool
    {
        $statement = "SELECT * from nodes.nodes WHERE parent_id = 0;";

        try {
            $statement = $this->database->query($statement);

            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            return (bool) count($result);
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    /**
     * @param string $title
     * @param int|null $parentId
     * @return int|void
     */
    public function create(string $title, ?int $parentId = null)
    {
        if (!$parentId && $this->hasRoot()) {
            exit('Root node has already created!');
        }

        $statement = "INSERT INTO nodes (parent_id, title) VALUES (:parent_id, :title);";

        try {
            $statement = $this->database->prepare($statement);
            $statement->execute([
                'parent_id' => $parentId ?? 0,
                'title' => $title
            ]);

            return $statement->rowCount();
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    /**
     * @param string $title
     * @param int $id
     * @return int|void
     */
    public function updateTitle(string $title, int $id)
    {
        $statement = "UPDATE nodes SET title = :title WHERE id = :id;";

        try {
            $statement = $this->database->prepare($statement);
            $statement->execute([
                'title' => $title,
                'id' => $id
            ]);

            return $statement->rowCount();
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    /**
     * @param int $id
     * @return int|void
     */
    public function delete(int $id)
    {
        $statement = "DELETE FROM nodes.nodes WHERE id = :id;";

        try {
            $statement = $this->database->prepare($statement);
            $statement->execute(['id' => $id]);

            return $statement->rowCount();
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }
}
