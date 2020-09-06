<?php
declare(strict_types=1);

namespace Api\Controller;

use Api\Gateway\PersonGateway;
use Api\System\Database\Connection;
use Api\System\Http\Response;

class PersonController
{
    /**
     * @var Connection
     */
    private Connection $connection;

    private string $requestMethod;

    private ?int $personId;
    /**
     * @var PersonGateway
     */
    private PersonGateway $gateway;
    /**
     * @var Response
     */
    private Response $response;

    public function __construct(Connection $connection, string $requestMethod, ?int $personId) {
        $this->connection = $connection;
        $this->requestMethod = $requestMethod;
        $this->personId = $personId;
        $this->response = new Response();
        $this->gateway = new PersonGateway($connection);
    }

    public function processRequest(): void {
        switch ($this->requestMethod) {
            case 'GET':
                if (null !== $this->personId) {
                    $this->getUser($this->personId);
                } else {
                    $this->getAllUsers();
                }
                break;
            case 'POST':
                $this->createUser();;
                break;
            case 'PUT':
                if (null === $this->personId) {
                    $this->getResponse()->setStatusCode(400);
                } else {
                    $this->updateUser($this->personId);
                }
                break;
            case 'DELETE':
                if (null === $this->personId) {
                    $this->getResponse()->setStatusCode(400);
                } else {
                    $this->deleteUser($this->personId);
                }
                break;
            default:
                $this->methodNotAllowed();
        }
        header($this->getResponse()->getStatusCode());
        if (null !== $this->getResponse()->getBodyContent()) {
            echo $this->getResponse()->getBodyContent();
        }
    }

    private function getUser(int $id): void {
        $result = $this->gateway->find($id);
        if (null === $result) {
            $this->notFoundResponse();
            return;
        }

        $this->getResponse()->setBody(json_encode($result));
    }

    private function getAllUsers(): void {
        $result = $this->gateway->findAll();
        $this->getResponse()->setBody(
            json_encode($result)
        );
    }

    private function createUser(): void {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$this->validatePerson($data)) {
            $this->getResponse()->setStatusCode(422);
            return;
        }
        $result = $this->gateway->create($data);
        $this->getResponse()
            ->setStatusCode(201)
            ->setBody(json_encode($result));
    }

    /**
     * @param int $id
     */
    private function updateUser(int $id): void {
        if (null === $this->gateway->find($id)) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$this->validKeys($data)) {
            $this->getResponse()->setStatusCode(422);
            return;
        }
        $result = $this->gateway->update($id, $data);
        $this->getResponse()->setStatusCode(200)->setBody(json_encode($result));
    }

    private function deleteUser(int $id): void {
        $this->gateway->delete($id);
        $this->getResponse()->setStatusCode(204);
    }

    private function notFoundResponse(): void {
        $this->getResponse()->setStatusCode(404);
    }

    private function validatePerson(array $data): bool {
        if (!$this->validKeys($data)) {
            return false;
        }
        if (null === ($data['firstname'] ?? null)) {
            return false;
        }
        if (null === ($data['lastname'] ?? null)) {
            return false;
        }

        return true;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function validKeys(array $data): bool
    {
        return [] === array_diff(
                array_keys($data),
                [
                    'firstname',
                    'lastname',
                    'first_person_id',
                    'second_person_id'
                ]
            );
    }

    private function methodNotAllowed(): void {
        $this->getResponse()->setStatusCode(405);
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
}