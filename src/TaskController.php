<?php
namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;

class TaskController {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // private function applyOverduePenalties(): void {
    //     $stmt = $this->db->prepare("UPDATE fines SET fine_amount = fine_amount * 1.2, status = 'overdue' WHERE status = 'unpaid' AND due_date <= DATE('now', '-30 day')");
    //     $stmt->execute();
    // }

    // private function countUnpaidByOffender(string $name): int {
    //     $stmt = $this->db->prepare("SELECT COUNT(*) FROM fines WHERE task_name = :task_name AND status = 'unpaid'");
    //     $stmt->execute([':task_name' => $name]);
    //     return (int) $stmt->fetchColumn();
    // }

    public function create(Request $request, Response $response): Response {

        $data = $request->getParsedBody();

        // $unpaidCount = $this->countUnpaidByOffender($data['task_name']);
        // if ($unpaidCount >= 3) {
        //     $data['fine_amount'] += 50;
        // }

        $stmt = $this->db->prepare("INSERT INTO tasks (task_name, task_description, task_type, due_date, status) VALUES (:task_name, :task_description, :task_type, :due_date, :status)");
        $stmt->execute([
            ':task_name' => $data['task_name'],
            ':task_description' => $data['task_description'],
            ':task_type' => $data['task_type'],
            ':due_date' => $data['due_date'],
            ':status' => $data['status'] ?? 'pending',
        ]);
        $response->getBody()->write(json_encode(['message' => 'Task created']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getAll(Request $request, Response $response): Response {

        $status = $request->getQueryParams()['status'] ?? null;
        error_log("Status: " . $status);
        $sql = "SELECT * FROM tasks";

        if ($status) {
            $sql .= " WHERE status = :status";
        }

        $stmt = $this->db->prepare($sql);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }

        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($tasks));
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function getOne(Request $request, Response $response, $args): Response {

        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute([':id' => $args['id']]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($task) {
            $response->getBody()->write(json_encode($task));
        } else {
            $response->getBody()->write(json_encode(['error' => 'Task not found']));
            return $response->withStatus(404);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, $args): Response {

        $data = $request->getParsedBody();
        $stmt = $this->db->prepare("UPDATE tasks SET task_name = :task_name, task_description = :task_description, task_type = :task_type, due_date = :due_date, status = :status WHERE id = :id");
        $stmt->execute([
            ':task_name' => $data['task_name'],
            ':task_description' => $data['task_description'],
            ':task_type' => $data['task_type'],
            ':due_date' => $data['due_date'],
            ':status' => $data['status'],
            ':id' => $args['id'],
        ]);
        $response->getBody()->write(json_encode(['message' => 'Task updated']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, $args): Response {
        $response = $response->withHeader('Access-Control-Allow-Origin', '*')
                         ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                         ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute([':id' => $args['id']]);
        $response->getBody()->write(json_encode(['message' => 'Task deleted']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function markTaskAsCompleted(Request $request, Response $response, $args): Response {


        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute([':id' => $args['id']]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$task) {
            $response->getBody()->write(json_encode(['error' => 'Task not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        // $issueDate = new \DateTime($task['due_date']);
        // $now = new \DateTime();
        // $diffDays = $issueDate->diff($now)->days;

        // if ($diffDays <= 14 && $fine['status'] === 'unpaid') {
        //     $discounted = $fine['fine_amount'] * 0.9;
        //     $updateStmt = $this->db->prepare("UPDATE fines SET fine_amount = :fine_amount WHERE id = :id");
        //     $updateStmt->execute([':fine_amount' => $discounted, ':id' => $args['id']]);
        // }

        $stmt = $this->db->prepare("UPDATE tasks SET status = 'completed' WHERE id = :id");
        $stmt->execute([':id' => $args['id']]);

        $response->getBody()->write(json_encode(['message' => 'Task has been marked as completed']));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
