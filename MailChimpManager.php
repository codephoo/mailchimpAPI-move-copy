<?php 
require 'MailChimp.php';
require 'Batch.php';
use \DrewM\MailChimp\MailChimp;
use \DrewM\MailChimp\Batch;

/**
*A Simple class to copy Mailchimp Subscribers from a list
*Uses Mailchimp API wrapper by Drew McLellan
*@author Shola Abaogun <shawlar.001@gmail.com 
*@version 1.0
*/
class MailChimpManager
{
  private $emailID;
  private $batch_id;

 public function __construct($apiKey) {
    $this->mc = new MailChimp($apiKey);
  }
  public function moveSubscribers($sourceListID, $targetListID) {
    $Subscribers = $this->getSubscribers($sourceListID);
   $this->addMembers($Subscribers,$targetListID);
   return $this->unsubscribe($this->emailID, $sourceListID);
  }

  public function copySubscribers($sourceListID, $targetListID) {
    $Subscribers = $this->getSubscribers($sourceListID);
    return $this->addMembers($Subscribers, $targetListID);

  }

  public function getSubscribers($sourceListID){
    $resource = $this->getMembers($sourceListID);
    return $this->formatResource($resource); 
  }
  private function getMembers($listID){
    $result = $this->mc->get("lists/$listID/members?status=subscribed&");
    if ($this->mc->getLastError()) {
      throw new \Exception($this->mc->getLastError());
    }
    return $result;
  }

  private function unsubscribe($emailIDs, $source) {
    $Batch = $this->mc->new_batch();
    foreach ($emailIDs as $key => $value){
      $Batch->patch("$key", "lists/$source/members/$value", array('status' => 'unsubscribed'));
    }
    $result = $Batch->execute();
    return $result['id'];
  }

  private function addMembers($data, $targetList) {
    $Batch = $this->mc->new_batch();
    foreach ($data as $key => $value) {
      $Batch->post("$key", "lists/$targetList/members/", $value);
    }
    $result = $Batch->execute();
    if ($this->mc->getLastError()) {
      throw new \Exception($this->mc->getLastError());
    }
    return $result['id'];
  }

  private function formatResource($result) {
    foreach ($result as $key => $value) {
      if($key == 'members'){
        foreach ($value as $member) {
          $data[] = array('email_address' => $member['email_address'],
                          'status' => $member['status'],
                          'merge_fields' => $member['merge_fields']
                          );
          $this->emailID[]  = $member['id'];
        }
      }
    }
    return $data;
  }
}
?>