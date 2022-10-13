<?php
namespace App\Model;

class RequestModel
{
    public const PENDING_STATUS = 0;
    public const APPROVE_STATUS = 1;
    public const RETURNING_STATUS = 2;
    public const RETURNED_STATUS = 3;
    public const REJECTED_STATUS = 4;
    public const ISSUED_DATE = "0000-00-00";
    public const RETURN_DATE = "0000-00-00";
    protected $conn;
    
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function RequestBook(int $bookId, int $requesterId, int $ownerId, string $date) : bool
    {
        $status = RequestModel::PENDING_STATUS;
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

        $pendingStatus = RequestModel::PENDING_STATUS;
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
        $pedingStatus = RequestModel::PENDING_STATUS;
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

    public function grantIssueRequest(int $requestingId, string $Date)
    {
        $approveRequest = RequestModel::APPROVE_STATUS;
        $grantIssueQry = $this->conn->prepare("update request set status = ?, issued_date = ? where id = ?");
        $grantIssueQry->bind_param("isi", $approveRequest, $Date, $requestingId);
        $grantIssueQry->execute();
        return true;
    }

    public function cancelIssueRequest(int $requestingId, string $cancelMessage)
    {
        $rejectRequest = RequestModel::REJECTED_STATUS;
        $cancelIssueRequest = $this->conn->prepare("update request set status = ? where id = ?");
        $cancelIssueRequest->bind_param("ii", $rejectRequest, $requestingId);
        $cancelIssueRequest->execute();
        return true;
    }
    public function returnBookRequest(int $requestingId) : bool
    {
        $returnValue = RequestModel::RETURNING_STATUS;
        $returnBookQry = $this->conn->prepare("select * from request where id = ?");
        $returnBookQry->bind_param("i", $requestingId);
        $returnBookQry->execute();
        $getStatus = $returnBookQry->get_result()->fetch_assoc();
        $returningStatus = $getStatus['status'];

        if($returningStatus == '1') {
            $returningBook = $this->conn->prepare("update request set status = ? where id = ?");
            $returningBook->bind_param("ii", $returnValue, $requestingId);
            $returningBook->execute();
            return true;
        } else {
            return false;
        }

    }

    public function acceptReturnRequest(int $requestingId, string $Date)
    {
        $acceptReturnRequest = RequestModel::RETURNED_STATUS;
        $grntRqstQry = $this->conn->prepare("update request set status = ?, return_date = ? where id = ?");
        $grntRqstQry->bind_param("isi", $acceptReturnRequest, $Date, $requestingId);
        $grntRqstQry->execute();
        return true;
    }
}