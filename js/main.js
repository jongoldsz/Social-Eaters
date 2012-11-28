jQuery(document).ready(function(){
    jQuery('#save_changes_button').click(function(){
	var event_id = jQuery("#e_event_id").val();
        var event_name = jQuery("#e_event_name").val();
        var event_location = jQuery("#e_event_location").val();
	var address1 = jQuery("#e_event_address1").val();
	var address2 = jQuery("#e_event_address2").val();
        var event_time = jQuery("#e_event_time").val();
        var event_date = jQuery("#e_event_date").val();
	var params = '{"event_id": "'+event_id+'", "event_name": "'+event_name+'", "location": "'+event_location+'", "address1": "'+address1+'", "address2": "'+address2+'", "date": "'+event_date+'", "time": "'+event_time+'"}';
	jQuery.post(window.apiURL,{'fb_id': localStorage.me_id, 'name': localStorage.me_name, 'command_type': 'Event', 'function': 'Update', 'parameters': params},function(data)
	{
	    getMyEvents();
	    window.location='#organize';
	});
    });

    jQuery('#delete_button').click(function(){
        var event_id = jQuery("#e_event_id").val();
        var params = '{"event_id": "'+event_id+'"}';
        jQuery.post(window.apiURL,{'fb_id': localStorage.me_id, 'name': localStorage.me_name, 'command_type': 'Event', 'function': 'Delete', 'parameters': params},function(data)
	{
	    getMyEvents();
	    window.location='#organize';
	});
    });

    jQuery('#create_button').click(function(){
        var event_name = jQuery("#c_event_name").val();
        var event_location = jQuery("#c_event_location").val();
        var event_time = jQuery("#c_event_time").val();
        var event_date = jQuery("#c_event_date").val();
	var address1 = jQuery("#c_event_address1").val();
        var address2 = jQuery("#c_event_address2").val();
        var params = '{"event_name": "'+event_name+'", "location": "'+event_location+'", "address1": "'+address1+'", "address2": "'+address2+'", "date": "'+event_date+'", "time": "'+event_time+'"}';
        jQuery.post(window.apiURL,{'fb_id': localStorage.me_id, 'name': localStorage.me_name, 'command_type': 'Event', 'function': 'Create', 'parameters': params},function(data)
	{
	    getMyEvents();
	    window.location='#organize';
	});
    });

    jQuery('#view_attendees_button').click(function(){
	console.log("this message");
    });

    jQuery('#c_event_location').focus(function() {
	window.prev = 'create_event';
	window.location='#yelp_search';
    });

    jQuery('#e_event_location').focus(function() {
        window.prev = 'edit_event';
	window.location='#yelp_search';
    });

});

jQuery(document).bind("pagechange", function(event, obj) // create content
{
    if(localStorage.getItem('me_id') == 'undefined' && obj.toPage[0].id != 'home')
    {
	window.location="#home";
    }
    if(obj.toPage[0].id == 'organize')
    {
	getMyEvents();
    }
    if(obj.toPage[0].id == 'home')
    {
	if(localStorage.getItem('me_id') != 'undefined' && localStorage.getItem('me_id') != undefined)
	{
	    jQuery('#home').children('.ui-content').html('Welcome back: '+localStorage.me_name);
	}
    }
    else if(obj.toPage[0].id == 'upcoming')
    {
        getUpcomingEvents();
    }
    else if(obj.toPage[0].id == 'attending')
    {
	getAttendingEvents();
    }
    else if(obj.toPage[0].id == 'edit_event')
    {
	if(jQuery("#e_event_id").val() == '') // tried to access edit page without selecting an event
	{
	    window.location="#organize";
	}
    }
    else if(obj.toPage[0].id == 'yelp_search')
    {
	jQuery("#entries").html("");
	jQuery('#search').val("");
	jQuery('#search').keyup(function() {
            var params = '{"term": "'+jQuery('#search').val()+'"}';
            jQuery.post(window.apiURL,{'fb_id': localStorage.me_id, 'name': localStorage.me_name, 'command_type': 'Yelp', 'function': 'Request', 'parameters': params},function(data)
	    {
		console.log(data);
		var businesses = jQuery.parseJSON(data);
		businesses = businesses.businesses;
		var output = '';
		for(var i = 0; i < businesses.length; i++)
		{
                    output += "<div data-role=\"collapsible\"><h3>"+businesses[i].name+"</h3><div><a href=\"tel:"+businesses[i].display_phone+"\">"+businesses[i].display_phone+"</a><br/>"+businesses[i].location.display_address[0]+"<br/>"+businesses[i].location.display_address[1]+"<a class=\"loc_selector\" href=\"#"+window.prev+"\" data-role=\"button\" data-icon=\"arrow-r\">Select</a></div></div></div>";
		}
		jQuery("#entries").html(output).trigger('create');
		jQuery(".loc_selector").unbind('click').click(function(){
		    var loc = jQuery(this).parent().html().split('<br>');
		    var address1 = loc[1];
		    var address2 = loc[2].split('<');
		    address2 = address2[0];
		    loc = jQuery(this).parent().parent().parent().html().split('<span class="ui-btn-text">');
		    loc = loc[1].split('<span class="ui-collapsible-heading-status">');
		    loc = loc[0];

		    if(window.prev == 'edit_event')
		    {
			jQuery('#e_event_location').val(loc);
			jQuery('#e_event_address1').val(address1);
			jQuery('#e_event_address2').val(address2);
		    }
		    else if(window.prev == 'create_event')
		    {
			jQuery('#c_event_location').val(loc);
                        jQuery('#c_event_address1').val(address1);
                        jQuery('#c_event_address2').val(address2);
		    }
		    jQuery("#entries").html("");
		    jQuery('#search').val("");
		});
	    });
	});
    }
});