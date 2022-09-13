<?php
namespace App\Controllers;
// session_start();
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\GetTokenFromDb\GetToken;
use App\Token\GenToken;
use App\Config\Db;


class BookController
{
    protected $conn;
    protected $bookModelObj;
    protected $valToken;
    protected $token;
    public function __construct($bookModelObj, $conn)
    {
        $this->conn = $conn;
        $this->bookModelObj = $bookModelObj;
        $this->valToken = new GetToken($conn);
        $this->token = new GenToken();
    }

    public function listAllBooks(Request $request, Response $response)
    {
        $bookLists = $this->bookModelObj->listAllBooks();
        if ($bookLists) {
            $jsonMessage = array("isSuccess" => true,
                                    "message" => "List of books",
                                    "list" => $bookLists);
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
            ->withStatus(500);
        }
    }

    public function addBook(Request $request, Response $response)
    {   
        $params = $request->getParsedBody();   
        $bName = trim($params['bName'] ?? '');
        $bGenre = trim($params['bGenre'] ?? '');
        $bAuthor = trim($params['bAuthor'] ?? '');
        $edition = (int) trim($params['edition'] ?? '');
        $description = trim($params['description'] ?? '');
        if (isset($_FILES['bImage']) && strlen($_FILES['bImage']['name']) != 0) {
            $allowedExt = ['png', 'jpg', 'jpeg'];
            $path = $_FILES['bImage']['name'];
            $imgExt = pathinfo($path, PATHINFO_EXTENSION);
            if (in_array($imgExt, $allowedExt)) { 
                $bImage = $_FILES['bImage'];
                $img_name = $bImage['name'];
                $img_path = $bImage['tmp_name'];
                $bookDest = __DIR__."/../img/books/".$img_name;
                $bookImgLink = "app/img/users/".$img_name;
                move_uploaded_file($img_path, $bookDest);

                $addBookRst = $this->bookModelObj->addBook($bName, $bookImgLink, $bGenre, $bAuthor, $edition, $description, $_SESSION['userId']);
        
                if ($addBookRst) {
                    $jsonMessage = array("isSuccess" => true,
                    "message" => "Book added Successfully");
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                        ->withHeader("content-type", "application/json")
                        ->withStatus(200);
                } else {
                    $jsonMessage = array("isSuccess" => false,
                    "message" => "Failed.... something error occured.");
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                        ->withHeader("content-type", "application/json")
                        ->withStatus(200);
                }

            } else {
                $jsonMessage = array("isSuccess" => false,
                "message" => "Only images are allowed.");
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
            }
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "Please upload image for book.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }       
    }

    public function editBook(Request $request, Response $response, $args)
    {
        $params = $request->getParsedBody();
        $bName = trim($params['bName'] ?? '');
        $bGenre = trim($params['bGenre'] ?? '');
        $bAuthor = trim($params['bAuthor'] ?? '');
        $edition = (int)trim($params['bEdition'] ?? '');
        $description = trim($params['description'] ?? '');
        $bookId = (int)$args['bookId'];

        if (isset($_FILES['bImage']) && strlen($_FILES['bImage']['name']) != 0) {
            $allowedExt = ['png', 'jpg', 'jpeg'];
            $path = $_FILES['bImage']['name'];
            $imgExt = pathinfo($path, PATHINFO_EXTENSION);
            if (in_array($imgExt, $allowedExt)) {    
                $bImage = $_FILES['bImage'];
                $img_name = $bImage['name'];
                $img_path = $bImage['tmp_name'];
                $bookDest = __DIR__."/../img/books/".$img_name;
                $bookImgLink = "app/img/users/".$img_name;
                move_uploaded_file($img_path, $bookDest);

                $editRst = $this->bookModelObj->editBook($bName, $bookImgLink, $bGenre, $bAuthor, $edition, $description, $bookId);
                if($editRst) {
                    $jsonMessage = array("isSuccess" => true,
                                        "message" => "Book Updated");
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(200);
                } else {
                    $jsonMessage = array("isSuccess" => false,
                    "message" => " Failed.... Something error occured.");
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(500);
                }
            } else {
                $jsonMessage = array("isSuccess" => false,
                "message" => "Only images are allowed.");
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
            }
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "Please upload image for book.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }      

    }

    public function deleteBook(Request $request, Response $response, $args)
    {                 
        $bookId = (int) $args['bookId'];
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

    }

    public function bookFeedback(Request $request, Response $response, $args)
    {

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

    }

    public function personalBooks(Request $request, Response $response)
    {
        $personalBooks = $this->bookModelObj->getPersonalBooks($_SESSION['userId']);
        if (count($personalBooks) > 0) {
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
    }

    public function searchBook(Request $request, Response $response, $args)
    {

    
        $searchQry = $args['searchString'];
        $searchRst = $this->bookModelObj->searchBook($searchQry);
        if(count($searchRst) > 0) {
            $jsonMessage = array("isSuccess" => true,
            "message" => "List of searching books.",
            "books" => $searchRst);
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        } else {
            $jsonMessage = array("isSuccess" => true,
            "message" => "No books found.");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);

        }

    }


}
