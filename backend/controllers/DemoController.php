<?php
require_once '../models/Demo.php';

class DemoController
{
    private $db;
    private $demo;

    public function __construct($db)
    {
        $this->db = $db;
        $this->demo = new Demo($db);
    }

    public function handleRequest($method, $path, $id = null)
    {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $this->getDemo($id);
                } else {
                    $this->getDemos();
                }
                break;
            case 'POST':
                if ($path === '/upload') {
                    $this->uploadFile();
                } else {
                    $this->createDemo();
                }
                break;
            case 'PUT':
                if ($id) {
                    $this->updateDemo($id);
                }
                break;
            case 'DELETE':
                if ($id) {
                    $this->deleteDemo($id);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(['message' => 'Method Not Allowed']);
                break;
        }
    }

    private function getDemos()
    {
        $filters = [
            'type' => $_GET['type'] ?? null,
            'category' => $_GET['category'] ?? null,
            'search' => $_GET['search'] ?? null
        ];
        $demos = $this->demo->getAll($filters);
        echo json_encode(['success' => true, 'data' => $demos]);
    }

    private function getDemo($id)
    {
        $demo = $this->demo->getById($id);
        if ($demo) {
            echo json_encode(['success' => true, 'data' => $demo]);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Demo not found']);
        }
    }

    private function createDemo()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['title']) || !isset($data['type']) || !isset($data['file_url'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Missing required fields']);
            return;
        }

        $id = $this->demo->create($data);
        if ($id) {
            http_response_code(201);
            echo json_encode(['success' => true, 'id' => $id, 'message' => 'Demo created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error creating demo']);
        }
    }

    private function updateDemo($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if ($this->demo->update($id, $data)) {
            echo json_encode(['success' => true, 'message' => 'Demo updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error updating demo']);
        }
    }

    private function deleteDemo($id)
    {
        if ($this->demo->delete($id)) {
            echo json_encode(['success' => true, 'message' => 'Demo deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error deleting demo']);
        }
    }

    private function uploadFile()
    {
        if (!isset($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(['message' => 'No file uploaded']);
            return;
        }

        $file = $_FILES['file'];
        $type = $_POST['type'] ?? 'other'; // video, audio, thumbnail

        $uploadDir = UPLOAD_PATH . $type . '/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $extension;
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            $fileUrl = 'backend/uploads/' . $type . '/' . $fileName;
            echo json_encode(['success' => true, 'url' => $fileUrl]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error uploading file']);
        }
    }
}
?>