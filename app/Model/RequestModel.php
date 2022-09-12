<?php
namespace App\Model;

class RequestModel
{
    public const STATUS = "pending";
    protected $conn;
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function RequestBook(string $requesterId, string $bookId, string $rqstBookReason, string $date) : bool
    {
        $bookOwnerStmt = $this->conn->prepare("select owner_id from books where id = ?");
        $bookOwnerStmt->bind_param("s",$bookId);
        $bookOwnerStmt->execute();
        $owner = $bookOwnerStmt->get_result()->fetch_assoc();
        $ownerId = $owner['owner_id'];
        $status = RequestModel::STATUS;
        $insertRqst = $this->conn->prepare("INSERT INTO request(requester_id, owner_id, book_id, reason, status, rqst_date) VALUES (?, ?, ?, ?, ?, ?)");
        $insertRqst->bind_param("ssssss", $requesterId, $ownerId, $bookId, $rqstBookReason, $status, $date);
        $insertRqst->execute();
        return true;

    }

    public function listRequests(int $userId) : array
    {

        $pendingStatus = "0";
        $returningStatus = "2";
        $listRqst = $this->conn->prepare("select rg.user_name as requester, bo.book_name as book, rq.status
        from request as rq
        inner join register as rg on rg.id = rq.requester_id
        inner join books as bo on bo.id = rq.book_id
        where rq.owner_id = ? and (rq.status = ? or rq.status = ?)");
        $listRqst->bind_param("iss", $userId, $pendingStatus, $returningStatus);
        $listRqst->execute();
        $myRequests = $listRqst->get_result()->fetch_all(MYSQLI_ASSOC);
        return $myRequests;
    }

    public function formateReceivedRqstMessage($listRequest)
    {
        $formatedList = [];
        foreach($listRequest as $item) {
            if ($item['status'] == 2) {
                $msg = $item['requester']." wants to return your ".$item['book']." book";
                array_push($formatedList, $msg);
                $msg = '';
            } else if ($item['status'] == 0) {
                $msg = $item['requester']." requested you ".$item['book'];
                array_push($formatedList, $msg);
                $msg = '';
            }
        }
        return $formatedList;
    }

    public function listSentRequest(int $userId) : array
    {
        $pedingStatus = "0";
        $returningStatus = "2";
        $listSentRqst = [];
        $listRqstSent = $this->conn->prepare("select rg.user_name as owner, bo.book_name as book, rq.status
        from request as rq
        inner join register as rg on rg.id = rq.owner_id
        inner join books as bo on bo.id = rq.book_id
        where rq.requester_id = ? and (rq.status = ? or rq.status = ?)");
        $listRqstSent->bind_param("iss", $userId, $pedingStatus, $returningStatus);
        $listRqstSent->execute();
        $listSentRqst = $listRqstSent->get_result()->fetch_all(MYSQLI_ASSOC);
        return $listSentRqst;
    }

    public function formateSentRqstMsg($listSentRst) : array
    {
        $sentRqstList = [];
        foreach($listSentRst as $item) {
            if ($item['status'] == 0) {
                $msg = "You requested ".$item['book']." to ".$item['owner'];
                array_push($sentRqstList, $msg);
                $msg = '';
            } else if ($item['status'] == 2) {
                $msg = "You are returning ".$item['book']." to ".$item['owner'];
                array_push($sentRqstList, $msg);
                $msg = '';
            }
        }
        return $sentRqstList;
    }

    public function grantIssueRequest(int $requestingId, string $Date)
    {
        $approveRequest = "1";
        $grantIssueQry = $this->conn->prepare("update request set status = ?, issued_date = ? where id = ?");
        $grantIssueQry->bind_param("ssi", $approveRequest, $Date, $requestingId);
        $grantIssueQry->execute();
        return true;
    }

    public function cancelIssueRequest(int $requestingId, string $cancelMessage)
    {
        $rejectRequest = "4";
        $cancelIssueRequest = $this->conn->prepare("update request set status = ? where id = ?");
        $cancelIssueRequest->bind_param("si", $rejectRequest, $requestingId);
        $cancelIssueRequest->execute();
        return true;
    }
    public function returnBookRequest(int $requestingId) : bool
    {
        $returnValue = '2';
        $returnBookQry = $this->conn->prepare("select * from request where id = ?");
        $returnBookQry->bind_param("i", $requestingId);
        $returnBookQry->execute();
        $getStatus = $returnBookQry->get_result()->fetch_assoc();
        $returningStatus = $getStatus['status'];

        if($returningStatus == '1') {
            $returningBook = $this->conn->prepare("update request set status = ? where id = ?");
            $returningBook->bind_param("si", $returnValue, $requestingId);
            $returningBook->execute();
            return true;
        } else {
            return false;
        }

    }

    public function acceptReturnRequest(int $requestingId, string $Date)
    {
        $acceptStatus = "3";
        $grntRqstQry = $this->conn->prepare("update request set status = ?, return_date = ? where id = ?");
        $grntRqstQry->bind_param("ssi", $acceptStatus, $Date, $requestingId);
        $grntRqstQry->execute();
        return true;
    }
}