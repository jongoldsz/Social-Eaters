<?php

	class YelpQuery extends Query
	{
		public function runCommand($Command)
		{
			if(parent::verifyCommandType($Command)) // Verify if command has matching command type
			{
				switch($Command->getFunction()) // select function and pass parameters
				{
					case "Request":
					     return $this->requestData($Command->getParameters());
					break;
					default:
					     return new Response(0,'Specified function does not exist.','{"message": "invalid function"}');
					break;
				}
			}
			else
			{
				return new Response(0,'Command passed to Query has a non-matching CommandType.','{"message": "CommandType mismatch"}');
			}
		}
		private function requestData($parameters)
                {
			// build query
			$args = '';
                        if(isset($parameters->results))
			{
				$args .= ' -r '.$parameters->results;
			}
			if(isset($parameters->location))
			{
				$args .= ' -l '.$parameters->location;
                        }
			if(isset($parameters->term))
			{
				$args .= ' -t '.$parameters->term;
			}
			if(isset($parameters->latitude) && isset($parameters->longitude))
			{
				$args .= ' -lat '.$parameters->latitude . ' -lon '.$parameters->longitude.' -c';
			}
			if(isset($parameters->accuracy))
			{
				$args .= ' -a '.$parameters->accuracy;
			}
			exec('python ../bin/yelprequest.py'.$args,$output,$ret);
			$out = implode(' ',$output);
			if($ret == 0)
			{
				return new Response(1,'Request successful.',$out);
			}
			else
			{
				return new Response(1,'Request successful.','{"message": "'.$out.'"}');
			}
		}
	}

?>