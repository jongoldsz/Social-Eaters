<?php

	/* Includes */
	include('config.php');
	include('classes/RESTfulAPI.php');
	include('classes/AuthenticationManager.php');
	include('classes/Authentication.php');
	include('classes/CommandManager.php');
	include('classes/QueryList.php');
	include('classes/CommandType.php');
	include('classes/Command.php');
	include('classes/Query.php');
	include('classes/Event.php');
	include('classes/Friend.php');
	include('classes/AuthQuery.php');
	include('classes/EventQuery.php');
	include('classes/FriendsQuery.php');
	include('classes/YelpQuery.php');
	include('classes/Response.php');

	/* Setup Authentication */
	$AuthenticationManager = new AuthenticationManager();

	/* Setup QueryList */
	$QueryList = new QueryList();

	/* Add all Queries to the QueryList  */
	$AuthQuery = new AuthQuery(new CommandType('Auth'));
	$EventQuery = new EventQuery(new CommandType('Event'));
	$FriendsQuery = new FriendsQuery(new CommandType('Friends'));
	$YelpQuery = new YelpQuery(new CommandType('Yelp'));
	$QueryList->addQuery($AuthQuery);
	$QueryList->addQuery($EventQuery);
	$QueryList->addQuery($FriendsQuery);
	$QueryList->addQuery($YelpQuery);
	

	/* Setup CommandManager */
	$CommandManager = new CommandManager($QueryList);

	/* Setup the RESTfulAPI */
	$RESTfulAPI = new RESTfulAPI($CommandManager,$AuthenticationManager);



	/*
	$_POST['fb_id']=100000012983023;
	$_POST['name']='Ethan Fritz';
	$_POST['command_type']='Event';
	$_POST['function']='Leave';
	$_POST['parameters']='{"event_id": "13"}';
	*/

	/* Interpert the post */
	$Response = $RESTfulAPI->interpertPost($_POST);

	echo $Response->output;

?>