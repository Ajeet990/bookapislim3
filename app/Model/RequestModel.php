<?php
namespace App\Model;

class RequestModel
{
    //for request table
    public const REQUESTED_STATUS = 0;
    public const APPROVE_STATUS = 1;
    public const RETURNING_STATUS = 2;
    public const RETURNED_STATUS = 3;
    public const REJECTED_STATUS = 4;
    public const ISSUED_DATE = "0000-00-00";
    public const RETURN_DATE = "0000-00-00";
    //for books table
    public const ISSUED_STATUS = 1;
    public const AVAILABLE_STATUS = 0;

    
    protected $conn;
    
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function RequestBook(int $bookId, int $requesterId, int $ownerId, string $date) : bool
    {
        $status = RequestModel::REQUESTED_STATUS;
        $issued_date = RequestModel::ISSUED_DATE;
        $return_date = RequestModel::RETURN_DATE;
        $insertRqst = $this->conn->prepare("insert ignore into request(requester_id, owner_id, book_id, status, rqst_date, issued_date, return_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertRqst->bind_param("iiiisss", $requesterId, $ownerId, $bookId, $status, $date, $issued_date, $return_date);
        $insertRqst->execute();
        if ($insertRqst->insert_id > 0) {
            return true;
        } else {
            return false;
        }

    }

    public function listRequests(int $userId) : array
    {

        $pendingStatus = RequestModel::REQUESTED_STATUS;
        $returningStatus = RequestModel::RETURNING_STATUS;
        $listRqst = $this->conn->prepare("select rg.user_name as requester, bo.book_name as book, rq.status
        from request as rq
        inner join register as rg on rg.id = rq.requester_id
        inner join books as bo on bo.id = rq.book_id
        where rq.owner_id = ? and (rq.status = ? or rq.status = ?)");
        $listRqst->bind_param("iii", $userId, $pendingStatus, $returningStatus);
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
        $pedingStatus = RequestModel::REQUESTED_STATUS;
        $returningStatus = RequestModel::RETURNING_STATUS;
        $listSentRqst = [];
        $listRqstSent = $this->conn->prepare("select rg.user_name as owner, bo.book_name as book, rq.status
        from request as rq
        inner join register as rg on rg.id = rq.owner_id
        inner join books as bo on bo.id = rq.book_id
        where rq.requester_id = ? and (rq.status = ? or rq.status = ?)");
        $listRqstSent->bind_param("iii", $userId, $pedingStatus, $returningStatus);
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

    public function grantIssueRequest(int $requestingId, int $bookId, string $Date)
    {
        $approveRequest = RequestModel::APPROVE_STATUS;
        $issuedStatus = RequestModel::ISSUED_STATUS;
        $updateBookStatusIssuedStmt = $this->conn->prepare("update books set book_status = ? where id = ?");
        $updateBookStatusIssuedStmt->bind_param("ii",$issuedStatus, $bookId);
        $updateBookStatusIssuedStmt->execute();
        $grantIssueQry = $this->conn->prepare("update request set status = ?, issued_date = ? where (id = ? and book_id = ?)");
        $grantIssueQry->bind_param("isii", $approveRequest, $Date, $requestingId, $bookId);
        $grantIssueQry->execute();
        if ($grantIssueQry->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function cancelIssueRequest(int $requestingId, int $bookId, string $cancelReason)
    {
        $rejectStatus = RequestModel::REJECTED_STATUS;
        $cancelIssueRequest = $this->conn->prepare("update request set status = ?, reason = ? where (id = ? and book_id = ?)");
        $cancelIssueRequest->bind_param("isii", $rejectStatus, $cancelReason, $requestingId, $bookId);
        $cancelIssueRequest->execute();
        if ($cancelIssueRequest->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function returnBookRequest(int $requestingId, int $bookId) : bool
    {
        $returnValue = RequestModel::RETURNING_STATUS;
        // $returningStatus = RequestModel::RETURNING_STATUS;
        $updateBookStatusReturningStmt = $this->conn->prepare("update books set book_status = ? where id = ?");
        $updateBookStatusReturningStmt->bind_param("ii", $returnValue, $bookId);
        $updateBookStatusReturningStmt->execute();
        $returnBookQry = $this->conn->prepare("select * from request where id = ?");
        $returnBookQry->bind_param("i", $requestingId);
        $returnBookQry->execute();
        $getStatus = $returnBookQry->get_result()->fetch_assoc();
        $returningStatus = $getStatus['status'];

        if($returningStatus == '1') {
            $returningBook = $this->conn->prepare("update request set status = ? where (id = ? and book_id = ?)");
            $returningBook->bind_param("iii", $returnValue, $requestingId, $bookId);
            $returningBook->execute();
            return true;
        } else {
            return false;
        }

    }

    public function acceptReturnRequest(int $requestingId, int $bookId, string $Date) : bool
    {
        $acceptReturnRequest = RequestModel::RETURNED_STATUS;
        $availableStatus = RequestModel::AVAILABLE_STATUS;
        $updateBookStatusReturningStmt = $this->conn->prepare("update books set book_status = ? where id = ?");
        $updateBookStatusReturningStmt->bind_param("ii", $availableStatus, $bookId);
        $updateBookStatusReturningStmt->execute();
        $grntRqstQry = $this->conn->prepare("update request set status = ?, return_date = ? where (id = ? and book_id = ?)");
        $grntRqstQry->bind_param("isii", $acceptReturnRequest, $Date, $requestingId, $bookId);
        $grntRqstQry->execute();
        if ($grntRqstQry->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getRequests(int $bookId, int $requesterId)
    {
        $getRequestStmt = $this->conn->prepare("select * from request where book_id = ? and requester_id = ?");
        $getRequestStmt->bind_param("ii", $bookId, $requesterId);
        $getRequestStmt->execute();
        $rst = $getRequestStmt->get_result();
        if ($rst->num_rows > 0) {
            $getRequestRst = $rst->fetch_assoc();
            return $getRequestRst;
        } else {
            return false;
        }


    }
}