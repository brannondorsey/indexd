<?php

class Database {

	protected $user     = "root";
	protected $password = "root";
	protected $db       = "linelocker";
	protected $host     = "localhost";
	protected $result;
	protected $tmp_result;
	protected $connection;
	
	public function execute_sql($query) {
	
		$this->connection = new mysqli($this->host, $this->user, $this->password, $this->db);
		$this->connection->query($query);
		$this->connection->close();
	}
	
	public function retrieve_sql($query) {
	
		$this->connection = new mysqli($this->host, $this->user, $this->password, $this->db);
		$this->result = $this->connection->query($query)->fetch_assoc();
		$this->connection->close();
		
	}
	
	public function get_result($key=NULL) {
		
		return $this->result[$key];
		
	}
	
	public function get_all_results($query) {
	
		$this->connection = new mysqli($this->host, $this->user, $this->password, $this->db);
		
		if ($result = $this->connection->query($query)) {
		
			$i=0;
			while ($row = $result->fetch_assoc()) {
				$this->result[$i] = $row;
				$i++;	
			}
			
		$this->connection->close();
		
			if (count($this->result) > 1) {
				return $this->result;
			} else {
				return $this->result[0];
			}
		
		}
		
	}

}

?>