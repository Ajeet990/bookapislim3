<?php
namespace App\Model;

class RequestModel
{
    private $conn;
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

        $listRqst = $this->conn->query("select rg.user_name as requester, bo.book_name as book
        from request as rq
        inner join register as rg on rg.id = rq.requester_id
        inner join books as bo on bo.id = rq.book_id
        where rq.owner_id = '$userId' and rq.status = 'pending'");
        while ($row = mysqli_fetch_assoc($listRqst)) {
            array_push($myRequests, $row);
        }
        // echo "<pre>";
        // print_r($listQry);
        // foreach($listRqst as $item) {
        //     print_r($item) ;
        // }
        return $myRequests;


    }

    public function formateReceivedRqstMessage($listRequest)
    {
        $formatedList = array();
        foreach($listRequest as $item) {
            $msg = $item['requester']." requested you ".$item['book'];
            array_push($formatedList, $msg);
            $msg = '';
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
        // echo "<pre>";
        // print_r($listSentRqst);
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
}