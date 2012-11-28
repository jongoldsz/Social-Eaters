<?php

	class Friend // Class that encapsulates Friend data
	{
		public function __construct($table_id,$local_id,$fb_id,$name)
		{
			$this->table_id = $table_id;
			$this->local_id = $local_id;
			$this->fb_id = $fb_id;
			$this->name = $name;
		}
		public function toJson()
		{
			$output = 
				'{'.
				'       "table_id": "'.$this->table_id.'",'.
				'       "local_id": "'.$this->local_id.'",'.
				'	"name": "'.$this->name.'",'.
				'	"fb_id": "'.$this->fb_id.'"'.
				'}';
			return $output;
		}
	}

?>