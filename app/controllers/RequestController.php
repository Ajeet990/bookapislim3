<?php
namespace App\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\GetTokenFromDb\GetToken;

use App\Token\GenToken;

class RequestController
{
    public function __construct($requestModelObj, $conn)
    {
        $this->conn = $conn;
        $this->requestModelObj = $requestModelObj;
        $this->valToken = new GetToken($conn);
        $this->token = new GenToken();

    }

    public function requestBook(Request $request, Response $response, $args)
    {
    
        $bookId = $args['bookId'];
        $Date = date("Y-m-d");
        $rqstBookRst = $this->requestModelObj->RequestBook( $_SESSION['userId'],$bookId, $Date);
        if($rqstBookRst) {
            $jsonMessage = array("isSuccess" => true,
            "message" => "Requested successfully.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(401);
        }
                

    }

    public function listReceivedRequest(Request $request, Response $response)
    {

                //getting all the request done by the users.
                $listRequest = $this->requestModelObj->listRequests($_SESSION['userId']);
                // die();

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
                    $jsonMessage = array("isSuccess" => false,
                    "message" => "No new requests.");
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(401);
                }
    }

    public function listSentRequest(Request $request, Response $response)
    {
    
                $listSentRst = $this->requestModelObj->listSentRequest($_SESSION['userId']);
                if(count($listSentRst) > 0){
                    $formatedMessage = $this->requestModelObj->formateSentRqstMsg($listSentRst);
                    $jsonMessage = array("isSuccess" => true,
                    "message" => "List of request sent by you.",
                    "request" => $formatedMessage);
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(401);
                } else {
                    $jsonMessage = array("isSuccess" => false,
                    "message" => "No requests done till now.");
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(401);
                }


    }

    public function grantIssueRequest(Request $request, Response $response, $args)
    {

    
                $requestingId = $args['requestingId'];
                $Date = date("Y-m-d");
                $grantRequestResult = $this->requestModelObj->grantIssueRequest($requestingId, $Date);
                if($grantRequestResult) {
                    $jsonMessage = array("isSuccess" => true,
                    "message" => "Issued Book Successfully.");
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(401);
                }

    }

    public function cancelIssueRequest(Request $request, Response $response, $args)
    {

    
                $requestingId = (int) $args['requestingId'];
                $params = $request->getParsedBody();
                $cancelMessage = trim($params['message'] ?? '');
                $cancelIssueRequest = $this->requestModelObj->cancelIssueRequest($requestingId, $cancelMessage);
                if($cancelIssueRequest) {
                    $jsonMessage = array("isSuccess" => true,
                    "message" => "Book cancelled successfully.");
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(401);
                }

    }

    public function returnBookRequest(Request $request, Response $response, $args)
    {

                $requestingId = (int) $args['requestingId'];
                $returnRst = $this->requestModelObj->returnBookRequest($requestingId);
                if($returnRst) {
                    $jsonMessage = array("isSuccess" => true,
                    "message" => "Book return request sent successfully.");
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(200);
                } else {
                    $jsonMessage = array("isSuccess" => true,
                    "message" => "Book not issued.");
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
        $userRating = trim($params['userRating'] ?? '');

        $grntRtnRqst = $this->requestModelObj->grantReturnRequest($requestingId, $Date, $userRating);
        if($grntRtnRqst) {
            $jsonMessage = array("isSuccess" => true,
            "message" => "Accepted return request successfully.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }


    }


}