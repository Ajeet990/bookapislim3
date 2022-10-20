<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\GetTokenFromDb\GetToken;

use App\Token\GenToken;


class RequestController
{
    protected $conn;
    protected $requestModelObj;
    protected $valToken;
    protected $token;
    public function __construct($requestModelObj, $conn)
    {
        $this->conn = $conn;
        $this->requestModelObj = $requestModelObj;
        $this->valToken = new GetToken($conn);
        $this->token = new GenToken();

    }

    public function requestBook(Request $request, Response $response, $args)
    {  
        $bookId = (int)$args['bookId'];
        $reqst_date = date("Y-m-d");
        $params = $request->getParsedBody();
        $requesterId = (int)trim($params['requester_id'] ?? '');
        $bookOwner = (int)trim($params['book_owner'] ?? '');
        $rqstBookRst = $this->requestModelObj->RequestBook($bookId, $requesterId, $bookOwner, $reqst_date);
        if($rqstBookRst) {
            $jsonMessage = array("isSuccess" => true,
            "message" => "Requested successfully.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "failed book request.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }            
    }

    public function listReceivedRequest(Request $request, Response $response, $args)
    {
        $userId = (int)$args['userId'];
        //getting all the request done by the users.
        $listRequest = $this->requestModelObj->listRequests($userId);
        if(count($listRequest) > 0) {
            // function to formate the message in profer form to display.
            $formatedMessage = $this->requestModelObj->formateReceivedRqstMessage($listRequest);
            $jsonMessage = array("isSuccess" => true,
            "message" => "List of all requests",
            "request" => $formatedMessage);
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        } else {
            $jsonMessage = array("isSuccess" => true,
            "message" => "No new request received.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(401);
        }
    }

    public function listSentRequest(Request $request, Response $response, $args)
    {
        $userId = (int)$args['userId'];   
        $listSentRst = $this->requestModelObj->listSentRequest($userId);
        if(count($listSentRst) > 0){
            $formatedMessage = $this->requestModelObj->formateSentRqstMsg($listSentRst);
            $jsonMessage = array("isSuccess" => true,
            "message" => "List of request sent by you.",
            "requests" => $formatedMessage);
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        } else {
            $jsonMessage = array("isSuccess" => true,
            "message" => "No requests done till now.",
            "requests" => null);
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }
    }

    public function grantIssueRequest(Request $request, Response $response, $args)
    { 
        $requestingId = $args['requestingId'];
        $Date = date("Y-m-d");
        $params = $request->getParsedBody();
        $bookId = (int)trim($params['book_id'] ?? '');
        $grantRequestResult = $this->requestModelObj->grantIssueRequest($requestingId, $bookId, $Date);
        if($grantRequestResult) {
            $jsonMessage = array("isSuccess" => true,
            "message" => "Issued Book Successfully.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "Something went wrong.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }
    }

    public function cancelIssueRequest(Request $request, Response $response, $args)
    { 
        $requestingId = (int) $args['requestingId'];
        $params = $request->getParsedBody();
        $cancelReason = trim($params['cancelReason'] ?? '');
        $bookId = (int)trim($params['book_id'] ?? '');
        $cancelIssueRequest = $this->requestModelObj->cancelIssueRequest($requestingId, $bookId, $cancelReason);
        if($cancelIssueRequest) {
            $jsonMessage = array("isSuccess" => true,
            "message" => "Book cancelled successfully.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "Something went wrong.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }
    }

    public function returnBookRequest(Request $request, Response $response, $args)
    {
        $requestingId = (int) $args['requestingId'];
        $params = $request->getParsedBody();
        $bookId = (int)trim($params['book_id'] ?? '');
        $returnRst = $this->requestModelObj->returnBookRequest($requestingId, $bookId);
        if($returnRst) {
            $jsonMessage = array("isSuccess" => true,
            "message" => "Book return request sent successfully.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        } else {
            $jsonMessage = array("isSuccess" => true,
            "message" => "Something went wrong.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }
    }

    public function grantReturnRequest(Request $request, Response $response, $args)
    {
        $requestingId = (int) $args['requestingId'];
        $Date = date("Y-m-d");
        $params = $request->getParsedBody();
        $bookId = (int)trim($params['book_id'] ?? '');

        $grntRtnRqst = $this->requestModelObj->acceptReturnRequest($requestingId, $bookId, $Date);
        if($grntRtnRqst) {
            $jsonMessage = array("isSuccess" => true,
            "message" => "Accepted return request successfully.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "Something went wrong.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }
    }

    public function requestStatus(Request $request, Response $response)
    {
        $params = $request->getParsedBody();
        $bookId = (int)trim($params['book_id'] ?? '');
        $requesterId = (int)trim($params['requester_id'] ?? '');
        $requested = $this->requestModelObj->getRequests($bookId, $requesterId);
        if ($requested) {
            $jsonMessage = array("isSuccess" => true,
            "message" => "List of request.",
            "request" => $requested);
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "No request.",
            "request" => null);
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }

    }
}
