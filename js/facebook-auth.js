// Load the SDK Asynchronously
(function(d){
    var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement('script'); js.id = id; js.async = true;
    js.src = "//connect.facebook.net/en_US/all.js";
    ref.parentNode.insertBefore(js, ref);
}(document));

      // Init the SDK upon load
window.fbAsyncInit = function() {
    FB.init({
        appId      : '266784566757859', // App ID
        channelUrl : '//'+window.location.hostname+'/channel', // Path to your Channel File
        status     : true, // check login status
        cookie     : true, // enable cookies to allow the server to access the session
        xfbml      : true  // parse XFBML
    });

        // listen for and handle auth.statusChange events
    FB.Event.subscribe('auth.statusChange', function(response) {
        if (response.authResponse) {
            // user has auth'd your app and is logged into Facebook
            FB.api('/me', function(me){
		if (me.name) {
		    localStorage.me_id = me.id;
		    localStorage.me_name = me.name;
                    jQuery.post(window.apiURL,{'fb_id': me.id, 'name': me.name, 'command_type': 'Auth', 'function': 'null', 'parameters': 'null'},function(data){
			var respone = jQuery.parseJSON(data);
			if(respone.auth == 'success')
			{
			    // load logged in home page
			    jQuery('#home').children('.ui-content').html('Welcome back: '+me.name);

 			    // get friends list
			    jQuery.getJSON("https://graph.facebook.com/me/friends?access_token="+FB.getAccessToken(),function(data){
				var friend_names = new Array();
				var friend_table_ids = new Array();
				var friend_ids = new Array();
				var friend_fb_ids = new Array();
				var friend_temp_ids = new Array();
				for(var i = 0; i < data.data.length; i++)
				{
				    friend_temp_ids.push(data.data[i].id);
				}
				jQuery.post(window.apiURL,{'fb_id': me.id, 'name': me.name, 'command_type': 'Friends', 'function': 'Find', 'parameters': '{"friends_list": "'+friend_temp_ids.join(",")+'"}'},function(data){
				    var friends = jQuery.parseJSON(data);
				    friends = friends.friends;
				    for(var i = 0; i < friends.length; i++)
				    {
					friend_names.push(friends[i].name);
					friend_table_ids.push(friends[i].table_id);
					friend_ids.push(friends[i].local_id);
					friend_fb_ids.push(friends[i].fb_id);
				    }
				    localStorage.friend_names = JSON.stringify(friend_names);
                                    localStorage.friend_ids = JSON.stringify(friend_ids);
                                    localStorage.friend_table_ids = JSON.stringify(friend_table_ids);
                                    localStorage.friend_fb_ids = JSON.stringify(friend_fb_ids);
				});
			    });
			}
			else
			{
			    // load login home page
			    jQuery('#home').children('.ui-content').html('<a href="#" id="login_link">Login</a>');
			}
                    });
		}
            })
        } else {
            // user has not auth'd your app, or is not logged into Facebook
            $.mobile.changePage("#home", "slideup");
        }
    });
        // respond to clicks on the login and logout links
    jQuery('#login_link').click(function(){
        FB.login();
    });
    jQuery('.logout_link').click(function(){
        FB.logout();
	// load login home page                                                                                      
        jQuery('#home').children('.ui-content').html('<a href="#" id="login_link">Login</a>');
	localStorage.me_name = undefined;
        localStorage.me_id = undefined;
	jQuery('#upcoming_events').html('');
	jQuery('#my_events').html('');
	jQuery('#attending_events').html('');
	document.getElementById('login_link').addEventListener('click', function(){
            FB.login();
	});
    });
}