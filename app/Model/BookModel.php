<?php
namespace App\Model;
class BookModel
{
    protected $conn;
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function addBook(string $bName,string $bookDest,string $bGenre, string $bAuthor,int $edition,string $description, int $ownerId)
    {
        $addBookQry = $this->conn->query("INSERT INTO `books` (`book_name`, `image`, `genre`, `author`, `edition`, `description`, `owner_id`) VALUES ('$bName', '$bookDest', '$bGenre', '$bAuthor', '$edition', '$description','$ownerId')");

        if($addBookQry) {
            return true;
        } else {
            return false;
        }
    }

    public function editBook(string $bName, string $bookDest, string $bGenre, string $bAuthor, int $edition, string $description, int $bookId)
    {
        $checkQry = $this->conn->query("select * from books where id = '$bookId'");
        if (mysqli_num_rows($checkQry) > 0) {
            $updateQry = $this->conn->query("update books set book_name = '$bName', image = '$bookDest', genre = '$bGenre', author = '$bAuthor', edition = '$edition', description = '$description' where id = '$bookId'");           
            return true;
        } else {
            return false;
        }
    }

    public function deleteBook(int $bookId)
    {
        $checkQry = $this->conn->query("select * from books where id = '$bookId'");
        if (mysqli_num_rows($checkQry) > 0) {
            $dltQry = $this->conn->query("DELETE from BOOKS where id = '$bookId'");
            return true;
        } else {
            return false;
        }
    }


    public function bookFeedback($userId, $userName, $message, $bookId)
    {
        $insrtBookFeedback = $this->conn->query("insert into feedback(commenter_name, feedback, user_id, book_id) VALUES ('$userName', '$message', '$userId', '$bookId')");
        if ($insrtBookFeedback) {
            return true;
        } else {
            return false;
        }
    }

    public function getPersonalBooks(int $userId)
    {
        $myBooks = array();
        $personalBooks = $this->conn->query("select * from books where owner_id = '$userId'");
        while($book = mysqli_fetch_assoc($personalBooks)) {
            array_push($myBooks, $book);
        }
        return $myBooks;
    }

    public function searchBook($search)
    {
        $searchBooks = array();
        $searchQry = $this->conn->query("select * from books where book_name LIKE '%$search%'");
        while($row = mysqli_fetch_assoc($searchQry)) {
            array_push($searchBooks, $row);
        }
        return $searchBooks;
    }
}