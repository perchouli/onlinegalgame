// Index 
$('#step td').mouseover( function () { $(this , 'p').css('color' , '#7BCA1C') ; } ).mouseout( function () { $(this , 'p').css('color' , '') ; } );

// Role List
$('.role_list .show_role').mouseover( function (){
	$("#"+this.id+"_toolbar").show();
}).mouseout( function (){
	$("#"+this.id+"_toolbar").hide();
});
$(document).ready(function(){
	var all_role = $('.show_role');
	for ( var i=0; i < all_role.length; i++)
	{
		var degree = Math.random()*10;
		if ( degree > 5 )
		degree = -degree;
		$('.show_role').eq(i).css('-o-transform','rotate('+degree+'deg)').css('-webkit-transform','rotate('+degree+'deg)').css('-moz-transform','rotate('+degree+'deg)');
	}
	
	$("input, textarea, select, button").uniform();
});
