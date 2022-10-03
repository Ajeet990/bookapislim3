<?php
namespace App\Model;
class BookModel
{
    protected $conn;
    
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function listAllBooks() : array
    {
        $bookListStmt = $this->conn->prepare("SELECT * from books");
        $bookListStmt->execute();
        $bookLists = $bookListStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return $bookLists;
    }

    public function addBook(string $bName, string $bookDest, string $bGenre, string $bAuthor, int $edition, string $description, string $ISBN, int $ownerId) : bool
    {
        $addBookQry = $this->conn->prepare("INSERT INTO books (book_name, image, genre, author, edition, description, isbn, owner_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $addBookQry->bind_param("ssssissi", $bName, $bookDest, $bGenre, $bAuthor, $edition, $description, $ISBN, $ownerId);
        $addBookQry->execute();
        if ($addBookQry) {
            return true;
        } else {
            return false;
        }
    }

    public function updateBook(string $bName, string $bookDest, string $bGenre, string $bAuthor, int $edition, string $description, int $bookId) : bool
    {
        $updateQry = $this->conn->prepare("update books set book_name = ?, image = ?, genre = ?, author = ?, edition = ?, description = ? where id = ?");
        $updateQry->bind_param("ssssisi", $bName, $bookDest, $bGenre, $bAuthor, $edition, $description, $bookId);
        $updateQry->execute();
        if ($updateQry) {         
            return true;
        } else {
            return false;
        }
    }

    public function deleteBook(int $bookId) : bool
    {
        $checkQry = $this->conn->prepare("select * from books where id = ?");
        $checkQry->bind_param("i", $bookId);
        $checkQry->execute();
        if ($checkQry->get_result()->num_rows > 0) {
            $dltQry = $this->conn->prepare("DELETE from BOOKS where id = ?");
            $dltQry->bind_param("i", $bookId);
            $dltQry->execute();
            return true;
        } else {
            return false;
        }
    }

    public function checkBookExists(string $bookId) : bool
    {
        $existStmt = $this->conn->prepare("select * from books where id = ?");
        $existStmt->bind_param("s", $bookId);
        $existStmt->execute();
        if($existStmt->get_result()->num_rows > 0)
        {
            return true;
        } else {
            return false;
        }
    }


    public function bookFeedback(string $userId, string $userName, string $message, string $bookId) : bool
    {
        $insrtBookFeedback = $this->conn->prepare("insert into feedback(commenter_name, feedback, user_id, book_id) VALUES (?, ?, ?, ?)");
        $insrtBookFeedback->bind_param("ssss", $userName, $message, $userId, $bookId);
        $insrtBookFeedback->execute();
        if ($insrtBookFeedback) {
            return true;
        } else {
            return false;
        }
    }

    public function getPersonalBooks(int $userId)
    {
        $personalBooks = $this->conn->prepare("select * from books where owner_id = ?");
        $personalBooks->bind_param("s", $userId);
        $personalBooks->execute();
        $myBooks = $personalBooks->get_result()->fetch_all(MYSQLI_ASSOC);
        return $myBooks;
    }

    public function searchBook($search)
    {
        $bookName = "%$search%";
        $searchBooks = array();
        $searchQry = $this->conn->prepare("select * from books where book_name LIKE ?");
        $searchQry->bind_param("s", $bookName);
        $searchQry->execute();
        $searchBooks = $searchQry->get_result()->fetch_all(MYSQLI_ASSOC);
        return $searchBooks;
    }

    public function getBookDetail(int $bookId)
    {
        $getBookStmt = $this->conn->prepare("select * from books where id = ?");
        $getBookStmt->bind_param("i", $bookId);
        $getBookStmt->execute();
        $getRst = $getBookStmt->get_result();
        if ($getRst->num_rows > 0) {
            $book = $getRst->fetch_assoc();
            return $book;
        } else {
            return false;
        }
    }
}