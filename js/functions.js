function getUpcomingEvents()
{
    var params = '{"by": "friends", "friends_list": "'+JSON.parse(localStorage.friend_fb_ids).join(",")+'"}';
    jQuery.post(window.apiURL,{'fb_id': localStorage.me_id, 'name': localStorage.me_name, 'command_type': 'Event', 'function': 'View', 'parameters': params},function(data)
    {
	var events = jQuery.parseJSON(data);
        events = events.events;

	var friend_list = getFriendIdTranslatingArray();

        var output = '';
        var events_arr = new Array();

        for(var i = 0; i < events.length; i++)
        {
            if(events_arr[events[i].date] == undefined) // initialize new array
            {
                events_arr[events[i].date] = new Array();
            }
            events_arr[events[i].date].push(events[i]);
        }
        for(var date in events_arr)
        {
            output += '<div data-role="collapsible">';
            output += '<h2>'+formatDate(date)+'</h2>';
            output += '<ul data-role="listview">';
            for(var i = 0; i < events_arr[date].length; i++)
            {
                output += '<li data-icon="arrow-r" data-iconpos="right"><a href="#" class="my_event" id="event_id_'+events_arr[date][i].event_id+'"><h3>'+events_arr[date][i].event_name+' - '+friend_list[events_arr[date][i].creator_table+'-'+events_arr[date][i].creator_id]+'</h3><h4 class="event_details"> '+events_arr[date][i].location+'</h4><h4 class="event_details">'+events_arr[date][i].address1+'</h4><h4 class="event_details">'+events_arr[date][i].address2+'</h4><h4 class="event_details"> '+formatTime(events_arr[date][i].time)+'</h4></a></li>';
            }
            output += '</ul>';
            output += '</div>';
        }
        //var node = jQuery(output).trigger('create');
        jQuery('#upcoming_events').html(output).trigger('create');
        jQuery('.my_event').unbind("click").click(function() {
            var event_id = jQuery(this).attr('id').substring(9);
	    params = '{"event_id": "'+event_id+'"}';
	    jQuery.post(window.apiURL,{'fb_id': localStorage.me_id, 'name': localStorage.me_name, 'command_type': 'Event', 'function': 'Join', 'parameters': params},function(data)
	    {
		data = jQuery.parseJSON(data);
		if(data.message=='successfully added')
		    alert("You're now attending this event.");
		else if(data.message=='already attending')
		    alert("You're already attending this event");
		else
		    alert("Error.");
	    });
        });
    });
}

function getMyEvents()
{
    var params = '{"by": "self"}';
    jQuery.post(window.apiURL,{'fb_id': localStorage.me_id, 'name': localStorage.me_name, 'command_type': 'Event', 'function': 'View', 'parameters': params},function(data)
    {
	var events = jQuery.parseJSON(data);
	events = events.events;

	var output = '';
	var events_arr = new Array();

	for(var i = 0; i < events.length; i++)
	{
	    if(events_arr[events[i].date] == undefined) // initialize new array 
	    {
		events_arr[events[i].date] = new Array();
	    }
	    events_arr[events[i].date].push(events[i]);
	}
	for(var date in events_arr)
	{
	    output += '<div data-role="collapsible">';
	    output += '<h2>'+formatDate(date)+'</h2>';
	    output += '<ul data-role="listview">';
	    for(var i = 0; i < events_arr[date].length; i++)
	    {
		output += '<li data-icon="arrow-r" data-iconpos="right"><a href="#edit_event" class="my_event" id="event_id_'+events_arr[date][i].event_id+'"><h3>'+events_arr[date][i].event_name+'</h3><h4 class="event_details"> '+events_arr[date][i].location+'</h4><h4 class="event_details">'+events_arr[date][i].address1+'</h4><h4 class="event_details">'+events_arr[date][i].address2+'</h4><h4 class="event_details"> '+formatTime(events_arr[date][i].time)+'</h4></a></li>';
	    }
	    output += '</ul>';
	    output += '</div>';
	}
	jQuery('#my_events').html(output).trigger('create');
	jQuery('.my_event').unbind("click").click(function() {
	    var event_id = jQuery(this).attr('id').substring(9);
	    var event_details = jQuery(this).children('h4');
	    var event_name = jQuery(this).children('h3').text();
	    var event_location = jQuery(event_details[0]).text();
	    var event_address1 = jQuery(event_details[1]).text();
	    var event_address2 = jQuery(event_details[2]).text();
	    var event_time = jQuery(event_details[3]).text();
	    var event_date = jQuery(this).parent().parent().parent().parent().parent().parent().find('span.ui-btn-text').html().split('<');
	    event_date = event_date[0];
	    jQuery("#e_event_id").val(event_id);
	    jQuery("#e_event_name").val(event_name);
	    jQuery("#e_event_location").val(event_location);
	    jQuery("#e_event_address1").val(event_address1);
	    jQuery("#e_event_address2").val(event_address2);
	    jQuery("#e_event_time").val(revertFormatTime(event_time));
	    jQuery("#e_event_date").val(revertFormatDate(event_date));
	    jQuery.post(window.apiURL,{'fb_id': localStorage.me_id, 'name': localStorage.me_name, 'command_type': 'Event', 'function': 'ViewAttendees', 'parameters': '{"event_id": "'+event_id+'"}'},function(data)
	    {
		var friend_list = getFriendIdTranslatingArray();
		var attendees = jQuery.parseJSON(data);
		attendees = attendees.attendees;
		var out = '';
		for(var i = 0; i < attendees.length; i++)
		{
		    if(i > 0)
			out += ", ";
		    out += friend_list[attendees[i].user_table+'-'+attendees[i].user_id];
		}
		jQuery("#attendees").html(out);
	    });
	});
    });
}

function getAttendingEvents()
{
	
	jQuery.post(window.apiURL,{'fb_id': localStorage.me_id, 'name': localStorage.me_name, 'command_type': 'Event', 'function': 'ViewAttending', 'parameters': ''},function(data)
	{
	var events = jQuery.parseJSON(data);
        events = events.events;
		
        var output = '';
        var events_arr = new Array();

        for(var i = 0; i < events.length; i++)
        {
            if(events_arr[events[i].date] == undefined) // initialize new array
            {
                events_arr[events[i].date] = new Array();
            }
            events_arr[events[i].date].push(events[i]);
        }
        for(var date in events_arr)
        {
            output += '<div data-role="collapsible">';
            output += '<h2>'+formatDate(date)+'</h2>';
            output += '<ul data-role="listview">';
            for(var i = 0; i < events_arr[date].length; i++)
            {
                output += '<li data-icon="arrow-r" data-iconpos="right"><a href="#" class="my_event" id="event_id_'+events_arr[date][i].event_id+'"><h3>'+events_arr[date][i].event_name+'</h3><h4 class="event_details"> '+events_arr[date][i].location+'</h4><h4 class="event_details">'+events_arr[date][i].address1+'</h4><h4 class="event_details">'+events_arr[date][i].address2+'</h4><h4 class="event_details"> '+formatTime(events_arr[date][i].time)+'</h4></a></li>';
            }
            output += '</ul>';
            output += '</div>';
        }
        //var node = jQuery(output).trigger('create');
        jQuery('#attending_events').html(output).trigger('create');
        jQuery('.my_event').unbind("click").click(function() {
            var event_id = jQuery(this).attr('id').substring(9);
	    var this_event = this;
	    params = '{"event_id": "'+event_id+'"}';
	    jQuery.post(window.apiURL,{'fb_id': localStorage.me_id, 'name': localStorage.me_name, 'command_type': 'Event', 'function': 'Leave', 'parameters': params},function(data)
	    {
		data = jQuery.parseJSON(data);
		if(data.message=='successfully removed') {
		    alert("You've left this event.");
		    var parent = jQuery(this_event).parent().parent().parent().parent();
		    jQuery(this_event).parent().parent().parent().remove();
		    if(jQuery(parent).html() == '')
		    {
			jQuery(parent).parent().parent().remove();
		    }
		    //remove the display of this event
		} else
		    alert("Error.");
	    });
        });
    });
}

function formatTime(time)
{
    var t = time.split(':');
    if (t[0] >= 12)
    {
	t[2] = 'PM';
    }
    else
    {
	t[2] = 'AM';
    }
    if(t[0] > 12)
	t[0] -= 12;
    var out = t[0]+':'+t[1]+' '+t[2];
    return out;
}

function revertFormatTime(time)
{
    var t = time.split(':');
    var u = t[1].split(' ');
    t[0] = parseInt(t[0]);
    if(u[1] == 'PM' && t[0] < 12)
    {
	t[0] = t[0] + 12;
    }
    if(t[0] < 10)
	t[0] = '0'+t[0];
    var out = t[0]+':'+u[0]+':00';
    return out;
}

function formatDate(date)
{
    date = date.split('-');
    var d = new Date(date[0],date[1]-1,date[2],1,1,1,1);

    var weekday=new Array(7);
    weekday[0]="Sunday";
    weekday[1]="Monday";
    weekday[2]="Tuesday";
    weekday[3]="Wednesday";
    weekday[4]="Thursday";
    weekday[5]="Friday";
    weekday[6]="Saturday";

    var month=new Array();
    month[0]="January";
    month[1]="February";
    month[2]="March";
    month[3]="April";
    month[4]="May";
    month[5]="June";
    month[6]="July";
    month[7]="August";
    month[8]="September";
    month[9]="October";
    month[10]="November";
    month[11]="December";
 
    var output = weekday[d.getDay()]+', '+month[d.getMonth()]+' '+d.getDate()+', '+d.getFullYear();
    return output;
}

function revertFormatDate(date)
{
    var month=new Array();
    month["January"]=1;
    month["February"]=2;
    month["March"]=3;
    month["April"]=4;
    month["May"]=5;
    month["June"]=6;
    month["July"]=7;
    month["August"]=8;
    month["September"]=9;
    month["October"]=10;
    month["November"]=11;
    month["December"]=12;

    date = date.split(' ');
    date[1]; // month
    date[2] = date[2].substring(0,date[2].length-1); // day
    date[3]; // year
    var output = date[3]+'-'+month[date[1]]+'-'+date[2];
    return output;
}

function getFriendIdTranslatingArray()
{
    var fb_ids = JSON.parse(localStorage.friend_fb_ids);
    var local_ids = JSON.parse(localStorage.friend_ids);
    var table_ids = JSON.parse(localStorage.friend_table_ids);
    var names = JSON.parse(localStorage.friend_names);
    
    var result = new Array();
    for(var i = 0; i < names.length; i++)
    {
	result[table_ids[i]+'-'+local_ids[i]] = names[i];
    }
    return result;
}
