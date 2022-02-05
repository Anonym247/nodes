<?php

namespace Src\Controller;

use PDO;
use Src\Gateway\NodeGateway;

class NodeController
{
    /**
     * @var string
     */
    private string $requestMethod;
    /**
     * @var int|null
     */
    private ?int $nodeId;

    /**
     * @var NodeGateway
     */
    private NodeGateway $nodeGateway;

    /**
     * @param PDO $database
     * @param string $requestMethod
     * @param int|null $nodeId
     */
    public function __construct(PDO $database, string $requestMethod, ?int $nodeId = null)
    {
        $this->requestMethod = $requestMethod;
        $this->nodeId = $nodeId;

        $this->nodeGateway = new NodeGateway($database);
    }

    /**
     * @return void
     */
    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                $response = $this->getAllNodes();
                break;
            case 'POST':
                $response = $this->createNode();
                break;
            case 'PUT':
                $response = $this->updateNode();
                break;
            case 'DELETE':
                $response = $this->deleteNode();
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }

        header($response['status_code_header']);

        if ($response['body']) {
            echo $response['body'];
        }
    }

    /**
     * @return array
     */
    private function getAllNodes(): array
    {
        $result = $this->nodeGateway->findAll();
        $tree = tree($result);
        $nodes = $tree['children'][0] ?? [];

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($nodes);

        return $response;
    }

    /**
     * @return array
     */
    private function createNode(): array
    {
        $requestData = json_decode(file_get_contents("php://input"));

        if (!isset($requestData->title) || !$requestData->title) {
            $response['status_code_header'] = 'HTTP/1.1 422';
            $response['body'] = json_encode([
                'status' => 0,
                'message' => 'title field is required'
            ]);

            return $response;
        }

        if (isset($requestData->parent_id) && $requestData->parent_id) {
            $parentNode = $this->nodeGateway->find($requestData->parent_id);

            if (!count($parentNode)) {
                $response['status_code_header'] = 'HTTP/1.1 404';
                $response['body'] = json_encode([
                    'status' => 0,
                    'message' => 'Parent node not found'
                ]);

                return $response;
            }
        }

         $this->nodeGateway->create($requestData->title, $requestData->parent_id ?? 0);

        $response['status_code_header'] = 'HTTP/1.1 200';
        $response['body'] = json_encode([]);

        return $response;
    }

    /**
     * @return array
     */
    private function updateNode(): array
    {
        $node = $this->nodeGateway->find($this->nodeId);

        if (!count($node)) {
            $response['status_code_header'] = 'HTTP/1.1 404';
            $response['body'] = json_encode([
                'status' => 0,
                'message' => 'Node with given id not found!'
            ]);

            return $response;
        }

        $requestData = json_decode(file_get_contents("php://input"));

        if (!isset($requestData->title) || !$requestData->title) {
            $response['status_code_header'] = 'HTTP/1.1 422';
            $response['body'] = json_encode([
                'status' => 0,
                'message' => 'title field is required'
            ]);

            return $response;
        }

        $this->nodeGateway->updateTitle($requestData->title, $this->nodeId);

        $response['status_code_header'] = 'HTTP/1.1 201';
        $response['body'] = json_encode([]);

        return $response;
    }

    /**
     * @return array
     */
    private function deleteNode(): array
    {
        $this->nodeGateway->delete($this->nodeId);

        $response['status_code_header'] = 'HTTP/1.1 201';
        $response['body'] = json_encode([]);

        return $response;
    }

    /**
     * @return array
     */
    private function notFoundResponse(): array
    {
        $response['status_code_header'] = 'HTTP/1.1 404';
        $response['body'] = json_encode([
            'status' => 0,
            'message' => 'The requested method not found on resource!'
        ]);

        return $response;
    }

}