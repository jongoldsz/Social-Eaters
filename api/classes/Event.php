<?php

	class Event // Class that encapsulates Event data
	{
		public function __construct($event_id,$event_name,$creator_table,$creator_id,$location,$address1,$address2,$time,$date)
		{
			$this->event_id = $event_id;
			$this->event_name = $event_name;
			$this->creator_table = $creator_table;
			$this->creator_id = $creator_id;
			$this->location = $location;
			$this->address1 = $address1;
			$this->address2 = $address2;
			$this->time = $time;
			$this->date = $date;
		}
		public function toJson()
		{
			$output = 
				'{'.
				'       "event_id": "'.$this->event_id.'",'.
				'       "event_name": "'.$this->event_name.'",'.
				'	"location": "'.$this->location.'",'.
				'       "address1": "'.$this->address1.'",'.
				'       "address2": "'.$this->address2.'",'.
				'	"time": "'.$this->time.'",'.
				'	"creator_table": "'.$this->creator_table.'",'.
				'       "creator_id": "'.$this->creator_id.'",'.
				'       "date": "'.$this->date.'"'.
				'}';
			return $output;
		}
	}

?>