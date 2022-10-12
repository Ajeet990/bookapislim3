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

    public function addBook(string $bName, string $bookDest, string $bGenre, string $bAuthor, int $edition, String $publisher, string $description, string $rating, string $ISBN, int $ownerId) : bool
    {
        $addBookQry = $this->conn->prepare("INSERT INTO books (book_name, image, genre, author, edition, publisher, description, rating, isbn, owner_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $addBookQry->bind_param("ssssissssi", $bName, $bookDest, $bGenre, $bAuthor, $edition, $publisher, $description, $rating, $ISBN, $ownerId);
        $addBookQry->execute();
        if ($addBookQry) {
            return true;
        } else {
            return false;
        }
    }

    public function updateBook(string $bName, string $bookDest, string $bGenre, string $bAuthor, int $edition, string $publisher, string $ISBN, string $description, string $rating, int $bookId) : bool
    {
        $updateQry = $this->conn->prepare("update books set book_name = ?, image = ?, genre = ?, author = ?, edition = ?, publisher = ?, isbn = ?, description = ?, rating = ? where id = ?");
        $updateQry->bind_param("ssssissssi", $bName, $bookDest, $bGenre, $bAuthor, $edition, $publisher, $ISBN, $description, $rating, $bookId);
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
            $dltQry = $this->conn->prepare("delete from books where id = ?");
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


    public function bookFeedback(int $userId, string $message, int $bookId) : bool
    {
        $insrtBookFeedback = $this->conn->prepare("insert into feedback(feedback, user_id, book_id) VALUES (?, ?, ?)");
        $insrtBookFeedback->bind_param("sii", $message, $userId, $bookId);
        $insrtBookFeedback->execute();
        if ($insrtBookFeedback) {
            return true;
        } else {
            return false;
        }
    }

    public function getPersonalBooks(int $userId, string $search) : array
    {
        if ($search == '') {
            $personalBooks = $this->conn->prepare("select * from books where owner_id = ? order by book_name");
            $personalBooks->bind_param("s", $userId);
        } else {
            $search_qry = "%$search%";
            $personalBooks = $this->conn->prepare("select * from books where (book_name LIKE ? OR author LIKE ? OR isbn LIKE ?) and (owner_id = ?) order by book_name");
            $personalBooks->bind_param("sssi", $search_qry, $search_qry, $search_qry, $userId);
        }
        $personalBooks->execute();
        $myBooks = $personalBooks->get_result()->fetch_all(MYSQLI_ASSOC);
        return $myBooks;
    }

    public function searchBook($search)
    {
        $book = "%$search%";
        $searchBooks = array();
        $searchQry = $this->conn->prepare("select * from books where book_name LIKE ? OR author LIKE ? OR isbn LIKE ?");
        $searchQry->bind_param("sss", $book, $book, $book);
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