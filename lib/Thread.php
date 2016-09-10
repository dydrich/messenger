<?php

require_once 'Message.php';
require_once __DIR__."/../../../lib/RBUtilities.php";

class Thread {

    /**
     * @var integer
     * thread ID
     */
    protected $tid;

	/**
	 * @var array<UserBean>
	 * thread users
	 */
	private $users;

	/**
	 * @var string
	 * thread name
	 */
	private $name;

	/**
	 * @var boolean
	 * flag for system thread
	 */
	private $systemThread;

	/**
	 * @var UserBean
	 * thread owner
	 */
	private $owner;

	/**
	 * @var int
	 * thread type
	 */
	private $type;

	/**
	 * @var date
	 * thread creation date
	 */
	private $creationDate;

	/**
	 * @var array<timestamp>
	 * thread last access for every user
	 */
	private $lastAccesses;

	/**
	 * @var boolean
	 * thread active flag
	 */
	private $active;

	/**
	 * @var boolean
	 * thread blocked flag
	 */
	private $blocked;

	/**
	 * @var array<UserBean>
	 * thread administrators
	 */
	private $admins;

    /**
     * @var array
     * list of messages in thread
     */
	protected $messages;

    /**
     * @var Message
     * newer message in thread
     */
	protected $last_message;
    
    /**
     * source of data
     * @var MySQLDataLoader
     */
	protected $datasource;

	/**
	 * @var array
	 * thread permission for type
	 */
	private $perms = array("admin" => -1, "teachers" => -1, "students" => -1, "parents" => -1, "class" => -1);

	/**
	 * constants: group types
	 */
	const CONVERSATION                  = 1;
	const ADMIN_GROUP                   = 2;
	const TEACHERS_GROUP                = 3;
	const TEACHERS_AND_PARENTS_GROUP    = 4;
	const STUDENTS_GROUP                = 5;
	const TEACHERS_AND_STUDENTS_GROUP   = 6;
	const PARENTS_GROUP                 = 7;
	const WORK_GROUP                    = 8;
	const USER_GROUP                    = 9;

	/**
	 * 
	 * @param integer $tid
	 * @param DataLoader $ds
	 * @param Date $cd
	 * @param string $name
	 * @param integer $type
	 * @param array<UserBean> $users
	 */
    public function __construct($tid, DataLoader $ds, $cd, $name = null, $type = self::CONVERSATION, $users = null){
	    $this->tid = $tid;
        $this->datasource = $ds;
	    $this->lastAccesses = array();
	     if ($users == null) {
		    $this->loadUsers();
	    }
	    else {
		   $this->users = $users;
	    }
        $this->last_message = null;
	    $this->systemThread = 0;
	    $this->creationDate = $cd;
	    $this->type = $type;
	    if ($type != self::CONVERSATION && $type != self::USER_GROUP) {
		    $this->setSystemThread(true);
	    }
	    $this->name = $name;
	    $this->admins = [];
	    if ($tid != 0) {
		    $this->loadMessages();
		    $this->loadPermission();
		    $this->checkActivity();
		    $this->checkBlock();
		    $this->loadAdmins();
	    }
	    else {
		    $this->messages = [];
		    $this->admins = [];
	    }
    }

	/**
	 * load permission from rb_system_thread
	 */
	private function loadPermission() {
		$perms = $this->datasource->executeQuery("SELECT * FROM rb_com_system_threads WHERE tid = {$this->tid}");
		if ($perms[0]['classe'] != "") {
			$this->perms['class'] = $perms[0]['classe'];
		}
		if ($perms[0]['docenti'] != "") {
			$this->perms['teachers'] = $perms[0]['docenti'];
		}
		if ($perms[0]['alunni'] != "") {
			$this->perms['students'] = $perms[0]['alunni'];
		}
		if ($perms[0]['genitori'] != "") {
			$this->perms['parents'] = $perms[0]['genitori'];
		}
		if ($perms[0]['ata'] != "") {
			$this->perms['admin'] = $perms[0]['ata'];
		}
	}
    
    public function restoreThread(DataLoader $dl){
    	$this->datasource = $dl;
	    $active = $this->checkActivity();
	    $blocked = $this->checkBlock();
	    return array($active, $blocked);
    }

	public function loadUsers() {
		$res = $this->datasource->executeQuery("SELECT utente, last_access FROM rb_com_utenti_thread WHERE thread = {$this->tid}");
		if ($res) {
			$rb = \RBUtilities::getInstance($this->datasource->getSource());
			foreach ($res as $row) {
				$this->users[$row['utente']] = $rb->loadUserFromUniqID($row['utente']);
				$this->lastAccesses[$row['utente']] = $row['last_access'];
			}
		}
	}

	private function checkActivity() {
		$active = $this->datasource->executeCount("SELECT active FROM rb_com_threads WHERE tid = {$this->tid}");
		if ($active != null) {
			$this->activate($active);
		}
		else {
			$active = -999;
		}
		return $active;
	}

	private function checkBlock() {
		$block = $this->datasource->executeCount("SELECT blocked FROM rb_com_threads WHERE tid = {$this->tid}");
		if ($block != null) {
			$this->setBlock($block);
		}
		else {
			$block = -999;
		}
		return $block;
	}

	private function loadAdmins() {
		$admins = $this->datasource->executeQuery("SELECT utente FROM rb_com_utenti_thread WHERE thread = {$this->tid} AND admin = 1");
		if ($admins) {
			$rb = \RBUtilities::getInstance($this->datasource->getSource());
			foreach ($admins as $admin) {
				$this->admins[$admin] = $rb->loadUserFromUniqID($admin);
			}
		}
	}
    
    protected function loadMessages(){
	    $rb = \RBUtilities::getInstance($this->datasource->getSource());
	    $messages = $this->datasource->executeQuery("SELECT * FROM rb_com_messages WHERE tid = {$this->tid} ORDER BY mid DESC");
	    if ($messages != null){
		    foreach ($messages as $message){
			    $sender = $rb->loadUserFromUniqID($message['sender']);
			    $target = $this->tid;

			    $msg = new Message($message['mid'], $this->tid, $sender, $target, $this->datasource, $message);
			    $this->messages[$message['mid']] = $msg;
		    }
	    }
    }
    
    public function getLastMessage(){
    	$msg = $this->messages;
	    if (count($msg) < 1) {
		    return null;
	    }
    	return array_shift($msg);
    }
    
    public function getMessagesCount(){
    	return count ($this->messages);
    }

    public function getUnreadMessages(UserBean $user){
    	$unread = array();
	    if ($this->getMessagesCount() > 0) {
			foreach ($this->messages as $msg){
				if ($user->getUniqID() == $msg->getFrom()->getUniqID()){
					if ($this->type == 1) {
						if ($msg->getReadTimestamp() == "" || $msg->getReadTimestamp() == null){
							$unread[$msg->getID()] = $msg;
						}
						else if ($msg->getSendTimestamp() > $this->lastAccesses[$user->getUniqID()]) {
							$unread[$msg->getID()] = $msg;
						}
					}
				}
			}
	    }
		if (count($unread) > 0){
			return $unread;
		}
		
		return null;
    }

    public function getUnreadMessagesCount($user){
		
    }
    
    public function addMessage(Message $msg){
    	$this->messages[$msg->getID()] = $msg;
    	$this->last_message = $msg;
    	krsort($this->messages);
    	$this->datasource->executeUpdate("UPDATE rb_com_threads SET last_message = {$msg->getID()} WHERE tid = {$this->tid}");
    }
    
    public function readAll($user){
	    $ts = null;
	    if ($this->type == self::CONVERSATION) {
	        $this->datasource->executeUpdate("UPDATE rb_com_messages SET read_timestamp = NOW() WHERE read_timestamp IS NULL AND tid = {$this->tid} AND sender != {$user->getUniqID()}");
	        foreach ($this->messages as $msg){
	            if (($msg->getFrom()->getUniqID() != $user->getUniqID()) && ($msg->getReadTimestamp() == "" || $msg->getReadTimestamp() == null)){
	                $ts = date("Y-m-d H:i:s");
	                $msg->setReadTimestamp($ts);
	            }
	        }
	    }
    	return $ts;
    }
    
    public function isRead($user){
	    if (count($this->messages) == 0) {
		    return null;
	    }
	    foreach ($this->messages as $msg){
		    if ($this->type == self::CONVERSATION) {
	            if (($msg->getFrom()->getUniqID() != $user->getUniqID()) && ($msg->getReadTimestamp() == "" || $msg->getReadTimestamp() == null)){
	                return false;
	            }
		    }
		    else {
			    if ($msg->getSendTimestamp() > $this->lastAccesses[$user->getUniqID()] && $msg->getFrom()->getUniqID() != $user->getUniqID()){
				    return false;
			    }
		    }
    	}
    	return true;
    }

	public function checkForUpdates(){
		$news = array();
		$last_message = 0;
		if (count($this->messages) > 0) {
			$last_message = $this->getLastMessage()->getID();
		}
		$new_messages = $this->datasource->executeQuery("SELECT * FROM rb_com_messages WHERE tid = {$this->getTid()} AND sender <> {$_SESSION['__user__']->getUniqID()} AND mid > {$last_message} ORDER BY send_timestamp ASC");
		if ($new_messages){
			foreach ($new_messages as $msg){
				$rb = \RBUtilities::getInstance($this->datasource->getSource());
				$sender = $rb->loadUserFromUniqID($msg['sender']);
				$message = new Message($msg['mid'], $this->getTid(), $sender, $this->tid, $this->datasource, $msg);
				$message->read();
				$this->addMessage($message);
				list($data, $time) = explode(" ", $message->getSendTimestamp());
				if (date("Y-m-d") == $data){
					$data = "Inviato oggi alle";
				}
				else {
					$data = "Inviato il ".format_date($data, SQL_DATE_STYLE, IT_DATE_STYLE, "/")." alle ";
				}
				$data .= " ".substr($time, 0, 5);

				$rdate = $rtime = "";
				if ($message->getReadTimestamp() != "") {
					list($rdate, $rtime) = explode(" ", $message->getReadTimestamp());
					if (date("Y-m-d") == $rdate){
						$rdate = "oggi alle ";
						$rtime = substr($rtime, 0, 5);
					}
					else {
						$rdate = "il ". format_date($rdate, SQL_DATE_STYLE, IT_DATE_STYLE, "/")." alle ";
						$rtime = substr($rtime, 0, 5);
					}
				}
				$target_name = $this->getTargetName($_SESSION['__user__']->getUniqID());
				array_unshift($news, array("type" => "new", "mid" => $msg['mid'], "t_t" => $this->type, "sender" =>
					$sender->getFullName(), "target_name" => $target_name, "send" => $data, "read" => $rdate.$rtime, "text" => $msg['text']));
			}
		}
		/*
		 * read timestamp
		 */
		$unread = $this->getUnreadMessages($_SESSION['__user__']);
		//echo count($unread);
		if ($unread != null){
			if ($this->type == self::CONVERSATION) {
				foreach ($unread as $k => $row){
					$reads = $this->datasource->executeCount("SELECT read_timestamp FROM rb_com_messages WHERE mid = {$row->getID()}");
					if ($reads != null && $reads != false){
						list($rdate, $rtime) = explode(" ", $reads);
						$date = "oggi alle ".substr($rtime, 0, 5);
						array_unshift($news, array("type" => "read", "mid" => $row->getID(), "read" => $date));
						$message = $this->getMessage($k);
						$message->setReadTimestamp($reads);
						$this->addMessage($message);
					}
				}
			}
		}
		if (count($news) > 0){
			return $news;
		}

		return null;
	}

	public function save(){
		$ownerID = null;
		if ($this->owner != null) {
			$ownerID = $this->owner->getUniqID();
		}
		$this->tid = $this->datasource->executeUpdate("INSERT INTO rb_com_threads (owner, last_message, system, name, type, creation) VALUES (".field_null($ownerID, false).", NULL, {$this->systemThread}, ".field_null($this->name, true).", '{$this->type}', '{$this->creationDate}')");
		// set block
		if (count($this->users) > 0) {
			foreach ($this->users as $k => $user) {
				$this->datasource->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$this->tid}, {$k})");
			}
		}
	}

	public function deleteAll() {
		$this->deleteMessages();
		$this->deleteUsers();
		$this->datasource->executeUpdate("DELETE FROM rb_com_system_threads WHERE tid = {$this->tid}");
		$this->datasource->executeUpdate("DELETE FROM rb_com_threads WHERE tid = {$this->tid}");
	}

	private function deleteMessages() {
		$this->messages = [];
		$this->datasource->executeUpdate("DELETE FROM rb_com_messages WHERE tid = {$this->tid}");
	}

	private function deleteUsers() {
		$this->users = [];
		$this->datasource->executeUpdate("DELETE FROM rb_com_utenti_thread WHERE thread = {$this->tid}");
	}

	public function getOtherUser($uid){
		if (count($this->users) == 2){
			$rb = \RBUtilities::getInstance($this->datasource->getSource());
			$id = 0;
			foreach ($this->users as $u) {
				if ($u != $uid) {
					$id = $u;
				}
			}
			return $rb->loadUserFromUniqID($id);
		}
		return $this;
	}

	public function getTargetName($uid) {
		if ($this->type != self::CONVERSATION) {
			return $this->name;
		}
		$oth = $this->getOtherUser($uid);
		if ($oth instanceof Thread) {
			return $oth->getName();
		}
		else {
			return $oth->getFullName();
		}
	}

	public function updateLastAccess($uid) {
		$this->lastAccesses[$uid] = date("Y-m-d H:i:s");
		$this->datasource->executeUpdate("UPDATE rb_com_utenti_thread SET last_access = NOW() WHERE thread = ".$this->tid." AND utente = ".$uid);
	}

	public function deleteUser($uid) {
		$this->datasource->executeUpdate("DELETE FROM rb_com_utenti_thread WHERE thread = {$this->tid} AND utente = {$uid}");
		unset($this->users[$uid]);
	}

	public function addUser($uid) {
		$rb = \RBUtilities::getInstance($this->datasource->getSource());
		$this->users[$uid] = $rb->loadUserFromUniqID($uid);
		$this->datasource->executeUpdate("INSERT INTO rb_com_utenti_thread (thread, utente) VALUES ({$this->tid}, {$uid})");
	}

	public function addAdministrator($val) {
		$uid = 0;
		if (is_numeric($val)) {
			$uid = $val;
			$rb = \RBUtilities::getInstance($this->datasource->getSource());
			$this->admins[$uid] = $rb->loadUserFromUniqID($uid);
		}
		else if ($val instanceof UserBean){
			$uid = $val->getUniqID();
			$this->admins[$uid] = $val;
		}

		$this->datasource->executeUpdate("UPDATE rb_com_utenti_thread SET admin = 1 WHERE utente = {$uid} AND thread = {$this->tid}");
	}

	public function removeAdministrator($uid) {
		unset($this->admins[$uid]);
		$this->datasource->executeUpdate("UPDATE rb_com_utenti_thread SET admin = 0 WHERE utente = {$uid} AND thread = {$this->tid}");
	}

	public function getAdmins() {
		return $this->admins;
	}

	public function getAdminIDs() {
		$ids = [];
		if (count($this->admins) > 0) {
			foreach ($this->admins as $admin) {
				$ids[] = $admin->getUniqID();
			}
		}
		return $ids;
	}

	public function isAdministrator($uid) {
		$ids = array_keys($this->admins);
		return in_array($uid, $ids);
	}

	/**
	 * @param UserBean $user
	 */
	public function isOwner($user) {
		if ($this->owner instanceof UserBean) {
			return $user->getUniqID() == $this->owner->getUniqID();
		}
		else {
			return $user->getUniqID() == $this->owner;
		}
	}

	/**
	 * @param mixed $blocked
	 */
	public function setBlock($blocked) {
		$this->blocked = $blocked;
		$this->datasource->executeUpdate("UPDATE rb_com_threads SET blocked = {$blocked} WHERE tid = {$this->tid}");
	}

	public function isBlocked() {
		return $this->blocked;
	}

	public function getMessage($mid){
		return $this->messages[$mid];
	}

	public function getMessages(){
		return $this->messages;
	}

	public function setMessages($messages){
		$this->messages = $messages;
	}

	/**
	 * @param mixed $creationDate
	 */
	public function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
	}

	/**
	 * @return mixed
	 */
	public function getCreationDate() {
		return $this->creationDate;
	}

	/**
	 * @param UserBean $owner
	 */
	public function setOwner($owner) {
		$this->owner = $owner;
	}

	/**
	 * @return UserBean
	 */
	public function getOwner() {
		return $this->owner;
	}

	/**
	 * @param boolean $systemThread
	 */
	public function setSystemThread($systemThread) {
		$this->systemThread = $systemThread;
	}

	/**
	 * @return boolean
	 */
	public function isSystemThread() {
		return $this->systemThread;
	}

	/**
	 * @param null $lastAccesses
	 */
	public function setLastAccesses($lastAccesses) {
		$this->lastAccesses = $lastAccesses;
	}

	/**
	 * @return null
	 */
	public function getLastAccesses() {
		return $this->lastAccesses;
	}

	public function getTid(){
		return $this->tid;
	}

	public function getUsers(){
		return $this->users;
	}

	public function getUserIDs() {
		$ids = [];
		if (count($this->users) > 0) {
			foreach ($this->users as $user) {
				$ids[] = $user->getUniqID();
			}
		}
		return $ids;
	}

	/**
	 * @param null $users
	 */
	public function setUsers($users) {
		$this->users = $users;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
		$this->datasource->executeUpdate("UPDATE rb_com_threads SET name = '{$name}' WHERE tid = {$this->tid}");
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
		$this->datasource->executeUpdate("UPDATE rb_com_threads SET type = '{$type}' WHERE tid = {$this->tid}");
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return mixed
	 */
	public function isActive() {
		return $this->active;
	}

	/**
	 * @param mixed $active
	 */
	public function activate($active) {
		$this->active = $active;
		$this->datasource->executeUpdate("UPDATE rb_com_threads SET active = {$active} WHERE tid = {$this->tid}");
	}

	public function getPerms() {
		return $this->perms;
	}
	
	public function deleteMessage($mid, $reason) {
		$msg = $this->messages[$mid];
		$msg->restoreDatasource($this->datasource);
		$msg->delete($reason);
		$this->messages[$mid] = $msg;
	}
	
	public function reportMessage($mid, $reason) {
		$msg = $this->messages[$mid];
		$msg->restoreDatasource($this->datasource);
		$msg->report($reason);
		$this->messages[$mid] = $msg;
	}
	
	public function restoreMessage($mid) {
		$msg = $this->messages[$mid];
		$msg->restoreDatasource($this->datasource);
		$txt = $msg->restore();
		$this->messages[$mid] = $msg;
		return $txt;
	}
	
	public function hasReportedMessages() {
		if ($this->getMessagesCount() > 0) {
			foreach ($this->messages as $msg){
				if ($msg->getState() == Message::REPORTED_FOR_SPAM || $msg->getState() == Message::REPORTED_FOR_VULGARITY) {
					return true;
				}
			}
		}
		return false;
	}
	
	public function getReportedMessages() {
		$reported = array();
		if ($this->getMessagesCount() > 0) {
			foreach ($this->messages as $msg){
				if ($msg->getState() == Message::REPORTED_FOR_SPAM || $msg->getState() == Message::REPORTED_FOR_VULGARITY) {
					$reported[$msg->getID()] = $msg;
				}
			}
		}
		if (count($reported) > 0){
			return $reported;
		}
		
		return null;
	}
	
	public function hasDeletedMessages() {
		if ($this->getMessagesCount() > 0) {
			foreach ($this->messages as $msg){
				if ($msg->getState() == Message::DELETED_FOR_SPAM || $msg->getState() == Message::DELETED_FOR_VULGARITY) {
					return true;
				}
			}
		}
		return false;
	}
	
	public function getDeletedMessages() {
		$deleted = array();
		if ($this->getMessagesCount() > 0) {
			foreach ($this->messages as $msg){
				if ($msg->getState() == Message::DELETED_FOR_SPAM || $msg->getState() == Message::DELETED_FOR_VULGARITY) {
					$deleted[$msg->getID()] = $msg;
				}
			}
		}
		if (count($deleted) > 0){
			return $deleted;
		}
		
		return null;
	}
}
