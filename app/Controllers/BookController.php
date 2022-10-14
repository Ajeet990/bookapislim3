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

    public function bookList(Request $request, Response $response)
    {
        $params = $request->getParsedBody();
        $search = trim($params['search'] ?? '');
        if ($search == '') {
            $bookLists = $this->bookModelObj->listAllBooks();
            if ($bookLists) {
                $jsonMessage = array("isSuccess" => true,
                                        "message" => "List of books",
                                        "book" => $bookLists);
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                        ->withHeader("content-type", "application/json")
                        ->withStatus(200);
            } else {
                $jsonMessage = array("isSuccess" => false,
                "message" => "Something went wrong.",
                "book" => null);
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
            }
        } else {          
            $searchRst = $this->bookModelObj->searchBook($search);
            if(count($searchRst) > 0) {
                $jsonMessage = array("isSuccess" => true,
                "message" => "List of books.",
                "book" => $searchRst);
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
            } else {
                $jsonMessage = array("isSuccess" => false,
                "message" => "No books found.",
                "book" => null);
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
            }
        }


    }

    public function addUpdate(Request $request, Response $response, $args)
    {
        $bookId = (int)$args['bookId'];
        $params = $request->getParsedBody();   
        $bName = trim($params['bName'] ?? '');
        $bGenre = trim($params['bGenre'] ?? '');
        $bAuthor = trim($params['bAuthor'] ?? '');
        $edition = (int) trim($params['edition'] ?? '');
        $publisher = trim($params['publisher'] ?? '');
        $rating = trim($params['rating'] ?? '');
        $description = trim($params['description'] ?? '');
        $ISBN = trim($params['ISBN'] ?? '');
        $user_id = trim($params['user_id'] ?? '');
        $bookImgLink = "0";

        if ($bookId == 0) {
            if (isset($_FILES['bImage']) && strlen($_FILES['bImage']['name']) != 0) {
                $allowedExt = ['png', 'jpg', 'jpeg'];
                $path = $_FILES['bImage']['name'];
                $imgExt = pathinfo($path, PATHINFO_EXTENSION);
                if (in_array($imgExt, $allowedExt)) {
                    $bImage = $_FILES['bImage'];
                    $img_name = $bImage['name'];
                    $img_path = $bImage['tmp_name'];
                    $bookDest = __DIR__."/../img/books/".$img_name;
                    $bookImgLink = "app/img/books/".$img_name;
                    move_uploaded_file($img_path, $bookDest);
    
                    $addBookRst = $this->bookModelObj->addBook($bName, $bookImgLink, $bGenre, $bAuthor, $edition, $publisher, $description, $rating, $ISBN, $user_id);
            
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
                    "message" => "Only images are allowed  (jpg, png, jpeg).");
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
        //Updating book
        } else {
            $imgDetail = $this->bookModelObj->getBookDetail($bookId);
            $bookExists = $this->bookModelObj->checkBookExists($bookId);
            if (!$bookExists) {
                $jsonMessage = array("isSuccess" => false,
                "message" => "Book doesn't exist.");
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(500);
            }
            $bookImgLink = $imgDetail['image'];

            if (isset($_FILES['bImage']) && strlen($_FILES['bImage']['name']) != 0) {
                $allowedExt = ['png', 'jpg', 'jpeg'];
                $path = $_FILES['bImage']['name'];
                $imgExt = pathinfo($path, PATHINFO_EXTENSION);
                if (in_array($imgExt, $allowedExt)) {  
                    $bImage = $_FILES['bImage'];
                    $img_name = $bImage['name'];
                    $img_path = $bImage['tmp_name'];
                    $bookDest = __DIR__."/../img/books/".$img_name;
                    $bookImgLink = "app/img/books/".$img_name;
                    move_uploaded_file($img_path, $bookDest);
                } else {
                    $jsonMessage = array("isSuccess" => false,
                    "message" => "Only images are allowed.");
                    $response->getBody()->write(json_encode($jsonMessage));
                    return $response
                    ->withHeader("content-type", "application/json")
                    ->withStatus(200);
                }
            }
            $editRst = $this->bookModelObj->updateBook($bName, $bookImgLink, $bGenre, $bAuthor, $edition, $publisher, $ISBN, $description, $rating, $bookId);
            if($editRst) {
                $jsonMessage = array("isSuccess" => true,
                                    "message" => "Book Updated");
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
            } else {
                $jsonMessage = array("isSuccess" => false,
                "message" => "Something error occured.");
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
            } 
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
        $bookId = (int)$args['bookId'];
        $params = $request->getParsedBody();   
        $commenter_id = (int)trim($params['user_id'] ?? '');
        $feedMessage = trim($params['message'] ?? '');
        $bookExistsRst = $this->bookModelObj->checkBookExists($bookId);
        
        if($bookExistsRst) {
            $bookFeedRst = $this->bookModelObj->bookFeedback($commenter_id, $feedMessage, $bookId);
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

    public function personalBooks(Request $request, Response $response, $args)
    {
        $userId = (int)$args['userId'];
        $params = $request->getParsedBody();   
        $search = trim($params['search'] ?? '');
        $personalBooks = $this->bookModelObj->getPersonalBooks($userId, $search);
        if (count($personalBooks) > 0) {
            $jsonMessage = array("isSuccess" => true,
                                    "message" => "My books",
                                    "book" => $personalBooks);
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "No personal books",
            "book" => null);
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }
    }


    public function getBookById(Request $request, Response $response, $args)
    {
        $bookId = $args['bookId'];
        $bookExists = $this->bookModelObj->checkBookExists($bookId);
        if ($bookExists) {
            $bookDetail = $this->bookModelObj->getBookDetail($bookId);
            if ($bookDetail) {
                $jsonMessage = array("isSuccess" => true,
                "message" => "Book details",
                "book" => $bookDetail);
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);

            } else {
                $jsonMessage = array("isSuccess" => false,
                "message" => "Something went wrong.",
                "book" => null);
                $response->getBody()->write(json_encode($jsonMessage));
                return $response
                ->withHeader("content-type", "application/json")
                ->withStatus(200);
            }
        } else {
            $jsonMessage = array("isSuccess" => false,
            "message" => "No books found.",
            "book" => null);
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }
    }

    public function getFeedBackList(Request $request, Response $response, $args)
    {
        $bookId = (int)$args['bookId'];
        $feedList = $this->bookModelObj->getFeedBackList($bookId);
        if (count($feedList) > 0) {
            $jsonMessage = array("isSuccess" => true,
            "message" => "Feedback list.",
            "feedList" => $feedList);
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);

        } else {
            $jsonMessage = array("isSuccess" => true,
            "message" => "No feedback to this book.",
            "feedList" => null);
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }
    }

    public function deleteFeed(Request $request, Response $response)
    {
        $params = $request->getParsedBody();
        $feedId = (int)trim($params['feed_id'] ?? '');
        $userId = (int)trim($params['user_id'] ?? '');
        $bookId = (int)trim($params['book_id'] ?? '');

        $deleteFeedRst = $this->bookModelObj->deleteFeed($feedId, $userId, $bookId);
        if ($deleteFeedRst) {
            $jsonMessage = array("isSuccess" => true,
            "message" => "Feed deleted");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);

        } else {
            $jsonMessage = array("isSuccess" => true,
            "message" => "Sorry not able to delete");
            $response->getBody()->write(json_encode($jsonMessage));
            return $response
            ->withHeader("content-type", "application/json")
            ->withStatus(200);
        }


    }


}
