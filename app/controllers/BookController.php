<?php
namespace App\controllers;
// session_start();
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\ValidateToken\ValidateToken;
use App\Token\Token;

class BookController
{
    public function __construct($bookModelObj, $conn)
    {
        $this->conn = $conn;
        $this->bookModelObj = $bookModelObj;
        $this->valToken = new ValidateToken($conn);
        $this->token = new Token();
    }

    public function addBook(Request $request, Response $response)
    {
        // $this->bookModelObj->addBook();
        
        // $isTokenValid = $this->valToken->getAndValidateToken();
        // echo $this->valToken->getAndValidateToken($_SESSION['userLoggedInMobile']);
        // die();
        if( isset($_SESSION['userId']) && $_SESSION['userId'] != '') {
            $userTokenDb = $this->valToken->getTokenFromDb($_SESSION['userId']);
            if($userTokenDb == $_SESSION['userLoggedInToken']) {

                
                    $params = $request->getParsedBody();
                
                    $bImage = $_FILES['bImage'];
                    $img_name = $bImage['name'];
                    $img_path = $bImage['tmp_name'];
                    $bookDest = __DIR__."/../img/books/".$img_name;
                    move_uploaded_file($img_path, $bookDest);

                    $bName = trim($params['bName'] ?? '');
                    $bGenre = trim($params['bGenre'] ?? '');
                    $bAuthor = trim($params['bAuthor'] ?? '');
                    $edition = trim($params['edition'] ?? '');
                    $description = trim($params['description'] ?? '');

                    $addBookRst = $this->bookModelObj->addBook($bName, $bookDest, $bGenre, $bAuthor, $edition, $description, $_SESSION['userId']);
                    
                    if($addBookRst){
                        $jsonMessage = array("isSuccess" => true,
                        "message" => "Book added Successfully",
                    );
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(200);
                } else {
                    $jsonMessage = array("isSuccess" => false,
                    "message" => "Book not added");
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(200);
                    
                }
            } else {
                $jsonMessage = array("isSuccess" => false,
                "message" => "Invalid request, Token not matching");
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(401);
            }
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "User not logged in, please login first.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }

    }

    public function editBook(Request $request, Response $response, $args)
    {
        
        if( isset($_SESSION['userId']) && $_SESSION['userId'] != '') {  
        $userTokenDb = $this->valToken->getTokenFromDb($_SESSION['userId']);
        if($userTokenDb == $_SESSION['userLoggedInToken']) {

        $bookId = (int)$args['bookId'];

        $checkBookExists = $this->bookModelObj->checkBookExists($bookId);
        if($checkBookExists) {
            $params = $request->getParsedBody();
            
            $bImage = $_FILES['bImage'];
            $img_name = $bImage['name'];
            $img_path = $bImage['tmp_name'];
            $bookDest = __DIR__."/../img/books/".$img_name;
            move_uploaded_file($img_path, $bookDest);

            $bName = trim($params['bName'] ?? '');
            $bGenre = trim($params['bGenre'] ?? '');
            $bAuthor = trim($params['bAuthor'] ?? '');
            $edition = (int)trim($params['bEdition'] ?? '');
            $description = trim($params['description'] ?? '');

            $editRst = $this->bookModelObj->editBook($bName, $bookDest, $bGenre, $bAuthor, $edition, $description, $bookId);

            if($editRst) {
                $jsonMessage = array("isSuccess" => true,
                                    "message" => "Book Updated");
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
            }


        } else {
            $jsonMessage = array("isSuccess" => false,
                                "message" => "Book Not avalable");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "Invalid request, Token not matching");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(401);

        }
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "User not logged in, please login first.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }

    }

    public function deleteBook(Request $request, Response $response, $args)
    {
                
        if( isset($_SESSION['userId']) && $_SESSION['userId'] != '') {  
            $userTokenDb = $this->valToken->getTokenFromDb($_SESSION['userId']);
            if($userTokenDb == $_SESSION['userLoggedInToken']) {
                    $bookId = (int)$args['bookId'];
                    $dltBookRst = $this->bookModelObj->deleteBook($bookId);
                    if($dltBookRst) {
                        $jsonMessage = array("isSuccess" => true,
                        "message" => "Book deleted successfully");
                        $response->getBody()->write(json_encode($jsonMessage));
                        return $response
                        ->withHeader("content-type", "application/json")
                        ->withStatus(200);
                    } else {
                        $jsonMessage = array("isSuccess" => false,
                        "message" => "No book available with that id.");
                        $response->getBody()->write(json_encode($jsonMessage));
                        return $response
                        ->withHeader("content-type", "application/json")
                        ->withStatus(200);
                    }
                } else {
                    $jsonMessage = array("isSuccess" => false,
                    "message" => "Invalid request, Token not matching");
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(401);

                }
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "User not logged in, please login first.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }
    }

    public function bookFeedback(Request $request, Response $response, $args)
    {
        
        if( isset($_SESSION['userId']) && $_SESSION['userId'] != '') {  
        $userTokenDb = $this->valToken->getTokenFromDb($_SESSION['userId']);
        if($userTokenDb == $_SESSION['userLoggedInToken']) {

            $bookId = $args['bookId'];
            $bookExistsRst = $this->bookModelObj->checkBookExists($bookId);
            
            if($bookExistsRst) {
                $params = $request->getParsedBody();
                $feedMessage = trim($params['message'] ?? '');

                $bookFeedRst = $this->bookModelObj->bookFeedback($_SESSION['userId'], $_SESSION['userName'], $feedMessage, $bookId);
                if($bookFeedRst) {
                    $jsonMessage = array("isSuccess" => true,
                    "message" => "Feedback submitted.");
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(200);
                }
            } else {
                $jsonMessage = array("isSuccess" => false,
                "message" => "Book Doesn't exist.");
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
            }

        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "Invalid request, Token not matching");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(401);

        }
        } else {
        $jsonMessage = array("isSuccess" => false,
        "message" => "User not logged in, please login first.");
        $response->getBody()->write(json_encode($jsonMessage));
        return $response
        ->withHeader("content-type", "application/json")
        ->withStatus(200);
        }
    }

    public function personalBooks(Request $request, Response $response)
    {
        if( isset($_SESSION['userId']) && $_SESSION['userId'] != '') {  
            $userTokenDb = $this->valToken->getTokenFromDb($_SESSION['userId']);
            if($userTokenDb == $_SESSION['userLoggedInToken']) {
    
                $personalBooks = $this->bookModelObj->getPersonalBooks($_SESSION['userId']);
                if(count($personalBooks) > 0) {
                    $jsonMessage = array("isSuccess" => true,
                                            "message" => "My books",
                                            "books" => $personalBooks);
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(401);
                } else {
                    $jsonMessage = array("isSuccess" => false,
                    "message" => "No personal books");
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(401);
                }
            } else {
                $jsonMessage = array("isSuccess" => false,
                "message" => "Invalid request, Token not matching");
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(401);
    
            }
            } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "User not logged in, please login first.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
            }
    }


}