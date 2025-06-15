$(document).ready(function(){
	
$(".login_form").on("submit",function(e) {
	e.preventDefault();
	var email=$(".email").val();
	var pass=$(".pass").val();
	$.post("back/login.php",{action:"login",email:email,pass:pass},function(ret) {
		if(ret==1){
			window.location.href="index.php";
		}else{
			alert(ret);
		}
	});

});$(".update_product").on("submit",function(e) {
	e.preventDefault();
	var name=$(".name").val();var num=$(".num").val();var specs=$(".specs").val();
	var des=$(".des").val();var max=$(".max").val();var dis=$(".dis").val();var size=$(".size").val();
	$.post("back/login.php",{action:"update_product",name:name,specs:specs,des:des,max:max,dis:dis,size:size,num:num},function(ret) {
		if(ret==1){
			alert("Updated!");
		}else{
			alert(ret);
		}
	});

});
$(".update_profile").on("submit",function(e) {
	e.preventDefault();
	var email=$(".email").val();
	var name=$(".name").val();var phone=$(".phone").val();var adr=$(".adr").val();
	$.post("back/login.php",{action:"update_profile",email:email,name:name,phone:phone,adr:adr},function(ret) {
		if(ret==1){
			alert("Updated!");
		}else{
			alert(ret);
		}
	});

});$(".update_pass").on("submit",function(e) {
	e.preventDefault();
	var pass=$(".pass").val();
	
	$.post("back/login.php",{action:"update_pass",pass:pass},function(ret) {
		if(ret==1){
			alert("Updated!");
		}else{
			alert(ret);
		}
	});
});
	$(".disable_product").click(function() {
	
	var id=$(this).attr('title');
	$(this).closest('tr').remove();
	$.post("back/login.php",{action:"disable_product",id:id},function(ret) {
		if(ret==1){
			
		}else{
			alert(ret);
		}
	});

});$(".enable_product").click(function() {
	
	var id=$(this).attr('title');
	$(this).closest('tr').remove();
	$.post("back/login.php",{action:"enable_product",id:id},function(ret) {
		if(ret==1){
			
		}else{
			alert(ret);
		}
	});

});$(".review_abuse").click(function() {
	
	var id=$(this).attr('title');
	
	$.post("back/login.php",{action:"review_abuse",id:id},function(ret) {
		if(ret==1){
			alert("Submitted");
		}else{
			alert(ret);
		}
	});

});
$(".out_").click(function() {
	
	var id=$(this).attr('title');
	
	$.post("back/login.php",{action:"out_",id:id},function(ret) {
		if(ret==1){
			alert("Submitted");
		}else{
			alert(ret);
		}
	});

});$(".in_").click(function() {
	
	var id=$(this).attr('title');
	
	$.post("back/login.php",{action:"in_",id:id},function(ret) {
		if(ret==1){
			alert("Submitted");
		}else{
			alert(ret);
		}
	});

});
$(".join_form").on("submit",function(e) {
	e.preventDefault();
	var shopName = $(".name").val();
        var emailAddress = $(".email").val();
        var password = $(".pass").val();
        var phone = $(".phone").val();
        var shopAddress = $(".adr").val();
        var lat = $(".lat").val();
        var lon = $(".lon").val();

        var formData = {
        	"action":"join",
            "shopName": shopName,
            "emailAddress": emailAddress,
            "password": password,
            "phone": phone,
            "shopAddress": shopAddress,"lat": lat,"lon": lon,
        };
	
	$.post("back/login.php",formData,function(ret) {
		if(ret==1){
			alert("We received your request to join us. We will mail you after your account is verified!");
		}else{
			alert(ret);
		}
	});

});
$(".update_tracking").click(function() {
	
	var id=$(this).attr('title');
	 var t_ = $(this).closest('tr').find('.my_tracking_id').val();
	$.post("back/login.php",{action:"pick__",id:id,t_:t_},function(ret) {
		if(ret==1){
			alert("Done");
		}else{
			alert(ret);
		}
	});

});
});