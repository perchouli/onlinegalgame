	var role_dom = ['cloth', 'hair', 'face', 'eye'];
	var role_select = document.querySelectorAll('select');
	
	
	function validator_role_from()
	{
		var json = [];
		var profile = document.getElementById('profile');
		for (var i in role_dom)
		{
			
			json[i] = '"'+document.getElementById(role_dom[i]).getAttribute('style')+'"';
		}
		profile.value = json;
		document.getElementById('role_form').submit();
	}