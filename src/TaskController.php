<?php

namespace App;

use PDO;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TaskController
{
    protected $db;

    public function __construct($container)
    {
        $this->db = $container->get(PDO::class);
    }

    public function list(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();

        $page = isset($params['page']) && is_numeric($params['page']) && $params['page'] > 0
            ? (int) $params['page']
            : 1;

        $limit = isset($params['limit']) && is_numeric($params['limit']) && $params['limit'] > 0
            ? (int) $params['limit']
            : 10;

        $offset = ($page - 1) * $limit;

        $countStmt = $this->db->query("SELECT COUNT(*) FROM tasks");
        $total = (int) $countStmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT * FROM tasks ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $tasks = $stmt->fetchAll();

        $response->getBody()->write(json_encode([
            'meta' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ],
            'data' => $tasks
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }


    public function get(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        $task = $stmt->fetch();
        if (!$task) {
            $response->getBody()->write(json_encode(['error' => 'Not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode($task));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        if (!isset($data['title']) || strlen(trim($data['title'])) < 3) {
            $response->getBody()->write(json_encode([
                'error' => 'Title is required and must be at least 3 characters.'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $title = trim($data['title']);
        $description = isset($data['description']) ? trim($data['description']) : null;

        $completed = 'false';
        if (isset($data['completed'])) {
            if (is_bool($data['completed'])) {
                $completed = $data['completed'] ? 'true' : 'false';
            } else if (is_string($data['completed'])) {
                $completed = filter_var($data['completed'], FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
            }
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO tasks (title, description, completed) VALUES (:title, :description, :completed::boolean)");
            $stmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':completed' => $completed
            ]);

            $response->getBody()->write(json_encode(['status' => 'Task created']));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Database error',
                'message' => $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }



    public function update(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        $id = $args['id'];

        try {
            $updates = [];
            $params = ['id' => $id];

            if (isset($data['title'])) {
                $updates[] = "title = :title";
                $params['title'] = trim($data['title']);
            }

            if (isset($data['description'])) {
                $updates[] = "description = :description";
                $params['description'] = trim($data['description']);
            }

            if (isset($data['completed'])) {
                $updates[] = "completed = :completed::boolean";
                if (is_bool($data['completed'])) {
                    $params['completed'] = $data['completed'] ? 'true' : 'false';
                } else if (is_string($data['completed'])) {
                    $params['completed'] = filter_var($data['completed'], FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
                }
            }

            if (empty($updates)) {
                $response->getBody()->write(json_encode(['error' => 'No valid fields to update']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $sql = "UPDATE tasks SET " . implode(', ', $updates) . " WHERE id = :id RETURNING *";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            $task = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$task) {
                $response->getBody()->write(json_encode(['error' => 'Task not found']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode($task));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Database error',
                'message' => $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }



    public function delete(Request $request, Response $response, array $args): Response
    {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([$args['id']]);
        $response->getBody()->write(json_encode(['status' => 'deleted']));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
