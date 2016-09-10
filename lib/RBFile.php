<?php

require_once 'start.php';
require_once 'Document.php';

class RBFile extends Document {
	
	/**
	 * 
	 * @var integer
	 */
	private $target;
	
	private $downloadDate;
	
	private $datasource;
	
	public function __construct($data, DataLoader $dl){
		$this->id = $data['id'];
		$this->owner = $_SESSION['__user__'];
		$this->target = $data['destinatario'];
		$this->file = $data['file'];
		$this->dataUpload = $data['data_invio'];
		$this->downloadDate = $data['data_download'];
		$this->datasource = $dl;
		$this->deleteOnDownload = true;
		$this->filePath = "download/files/";
	}
	
	public function save(){
		if ($this->id == 0){
			$q = "INSERT INTO rb_com_files (mittente, destinatario, file, data_invio, data_download) VALUES ({$this->owner->getUid()}, {$this->target}, '{$this->file}', NOW(), NULL)";
			$this->id = $this->datasource->executeUpdate($q);
			$this->dataUpload = date("Y-m-d H:i:s");
		}
	}
	
	public function delete(){
		$this->executeUpdate("DELETE FROM rb_com_files WHERE id = {$this->id}");
		$this->deleteFile();
	}
	
	public function download(){
		$this->datasource->executeUpdate("UPDATE rb_com_files SET data_download = NOW() WHERE id = {$this->id}");
		$this->downloadFile();
		$this->deleteFile();
	}
	
}