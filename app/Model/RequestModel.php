<?php
namespace App\Model;

class RequestModel
{
    protected $conn;
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function RequestBook($userId, $bookId, $date)
    {
        $bookOwnerId = $this->conn->query("select owner_id from books where id = '$bookId'");
        $owner_Id = mysqli_fetch_assoc($bookOwnerId);
        $ownerId = $owner_Id['owner_id'];
        $insertRqst = $this->conn->query("insert into request(requester_id, owner_id, book_id, status, rqst_date) VALUES ('$userId', '$ownerId', '$bookId','pending', '$date')");
        return true;
    }

    public function listRequests(int $userId)
    {
        $myRequests = array();

        $listRqst = $this->conn->query("select rg.user_name as requester, bo.book_name as book, rq.isReturning as returnRequesting
        from request as rq
        inner join register as rg on rg.id = rq.requester_id
        inner join books as bo on bo.id = rq.book_id
        where rq.owner_id = '$userId' and (rq.status = 'pending' or rq.isReturning = '1')");
        while ($row = mysqli_fetch_assoc($listRqst)) {
            array_push($myRequests, $row);
        }
        return $myRequests;
    }

    public function formateReceivedRqstMessage($listRequest)
    {
        $formatedList = array();
        foreach($listRequest as $item) {
            if ($item['returnRequesting'] == 1) {
                $msg = $item['requester']." requested you to return ".$item['book'];
                array_push($formatedList, $msg);
                $msg = '';
            } else {
                $msg = $item['requester']." requested you ".$item['book'];
                array_push($formatedList, $msg);
                $msg = '';
            }
        }
        return $formatedList;
    }

    public function listSentRequest($userId)
    {
        $listSentRqst = array();
        $listRqstSent = $this->conn->query("select rg.user_name as owner, bo.book_name as book
        from request as rq
        inner join register as rg on rg.id = rq.owner_id
        inner join books as bo on bo.id = rq.book_id
        where rq.requester_id = '$userId' and rq.status = 'pending'");
        while($row = mysqli_fetch_assoc($listRqstSent)){
            array_push($listSentRqst, $row);
        }
        return $listSentRqst;
    }

    public function formateSentRqstMsg($listSentRst)
    {
        $sentRqstList = array();
        foreach($listSentRst as $item) {
            $msg = "You requested ".$item['book']." to ".$item['owner'];
            array_push($sentRqstList, $msg);
            $msg = '';
        }
        return $sentRqstList;
    }

    public function grantIssueRequest($requestingId, $Date)
    {
        $grantIssueQry = $this->conn->query("update request set status = 'issued', issued_date = '$Date' where id = '$requestingId'");
        return true;
    }

    public function cancelIssueRequest(int $requestingId, string $cancelMessage)
    {
        $cancelIssueRequest = $this->conn->query("update request set status = 'rejected' where id = '$requestingId'");
        return true;
    }
    public function returnBookRequest($requestingId)
    {
        $returnBookQry = $this->conn->query("select * from request where id = '$requestingId'");
        $row = mysqli_fetch_assoc($returnBookQry);
        if($row['status'] == 'issued') {
            $returningBook = $this->conn->query("update request set isReturning = '1' where id = '$requestingId'");
            return true;
        } else {
            return false;
        }

    }

    public function grantReturnRequest($requestingId, $Date, $userRating)
    {
        $grntRqstQry = $this->conn->query("update request set status = 'returned', return_date = '$Date' where id = '$requestingId'");
        $updateRatingQry = $this->conn->query("update register set rating = '$userRating' where id = '$requestingId'");
        return true;
    }
}