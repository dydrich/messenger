<?php

/**
 * 
 * User: cravenroad17@gmail.com
 * Date: 18/07/13
 * Time: 23:00
 * 
 */


class Message {

	/**
	 * 
	 * @var Thread
	 * thread the message belong to
	 */
    protected $thread;

    /**
     * 
     * @var integer
     * message id
     */
	protected $ID;

    /**
     * 
     * @var UserBean
     * sender
     */
	protected $from;
    /**
     * target
     * @var int
     */
    protected $to;

    /**
     * message'stext
     * @var string
     */
	protected $text;

    /**
     * date and time of sending
     * @var timestamp
     */
	protected $send_timestamp;
	/**
	 * date and time of reading
	 * @var timestamp
	 */
    private $read_timestamp;
    
    /**
     * source of data
     * @var MYSQLDataLoader
     */
	protected $datasource;

	protected $state;
	
	const PUBLISHED = 1;
	const REPORTED_FOR_SPAM = 2;
	const REPORTED_FOR_VULGARITY = 3;
	const DELETED_FOR_SPAM = 4;
	const DELETED_FOR_VULGARITY = 5;
	
	/**
	 * 
	 * @param integer $tid
	 * @param UserBean $sender
	 * @param int $target
	 * @param DataLoader $ds
	 */
    public function __construct($mid, $tid, UserBean $sender, $target, DataLoader $ds, $data){
		$this->thread = $tid;
		$this->from = $sender;
		$this->to = $target;
		$this->datasource = $ds;
		$this->ID = $mid;
	    $this->state = self::PUBLISHED;
		if ($data != null){
			$this->send_timestamp = $data['send_timestamp'];
			if ($data['read_timestamp'] != ""){
				$this->read_timestamp = $data['read_timestamp'];
			}
			else {
				$this->read_timestamp = null;
			}
			$this->text = $data['text'];
			$this->state = $data['state'];
		}
		if ($this->state == self::DELETED_FOR_SPAM || $this->state == self::DELETED_FOR_VULGARITY) {
			$this->hidden = true;
		}
    }

    public function getThread(){
		return $this->thread;
    }

    public function getID(){
		return $this->ID;
    }

    public function setID($id){
		$this->ID = $id;
    }

    public function getTo(){
		return $this->to;
    }

    public function getFrom(){
		return $this->from;
    }

    public function getText(){
		return stripslashes($this->text);
    }

    public function setText($txt){
		$this->text = $txt;
    }
	
	/**
	 * @return mixed
	 */
	public function getState() {
		return $this->state;
	}
	
	/**
	 * @param mixed $state
	 */
	public function setState($state) {
		$this->state = $state;
	}
    

    public function setSendTimestamp($tm){
		$this->send_timestamp = $tm;
    }

    public function getSendTimestamp(){
		return $this->send_timestamp;
    }

    public function setReadTimestamp($tm){
		$this->read_timestamp = $tm;
    }

    public function getReadTimestamp(){
		return $this->read_timestamp;
    }

    public function isRead(){
        return $this->read_timestamp != null;
    }

    public function send(){
    	$text = $this->text;
	    $this->ID = $this->datasource->executeUpdate("INSERT INTO rb_mess_messages (tid, sender, target, text) VALUES ({$this->getThread()}, {$this->from->getUniqID()}, {$this->to}, '{$text}')");
	    $this->send_timestamp = date("Y-m-d H:i:s");
    }
    
    public function read(){
    	$this->read_timestamp = date("Y-m-d H:i:s");
    	$this->datasource->executeUpdate("UPDATE rb_mess_messages SET read_timestamp = NOW() WHERE tid = {$this->getThread()} AND mid = {$this->getID()}");
    }
    
    public function delete($reason){
	    $this->report($reason);
    }
    
    public function isReported() {
    	return $this->state == self::REPORTED_FOR_SPAM || $this->state == self::REPORTED_FOR_VULGARITY;
    }
    
	public function isDeleted() {
		return $this->state == self::DELETED_FOR_SPAM || $this->state == self::DELETED_FOR_VULGARITY;
	}
    
    public function report($reason) {
    	$this->state = $reason;
	    $this->datasource->executeUpdate("UPDATE rb_mess_messages SET state = {$reason} WHERE tid = {$this->getThread()} AND mid = {$this->getID()}");
    }
    
    public function restoreDatasource($ds) {
    	$this->datasource = $ds;
    }
    
    public function restore() {
    	$this->report(1);
	    return $this->text;
    }

}
