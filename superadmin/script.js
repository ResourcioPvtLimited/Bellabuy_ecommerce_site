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
	var name=$(".name").val();
	var des=$(".des").val();var max=$(".max").val();var dis=$(".dis").val();
	$.post("back/login.php",{action:"update_product",name:name,des:des,max:max,dis:dis},function(ret) {
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
		if(ret==1){alert("Updated!");
		}else{
			alert(ret);
		}
	});

});$(".enable_product").click(function() {
	
	var id=$(this).attr('title');
	$(this).closest('tr').remove();
	$.post("back/login.php",{action:"enable_product",id:id},function(ret) {
		if(ret==1){alert("Updated!");
		}else{
			alert(ret);
		}
	});

});$(".review_abuse").click(function() {
	
	var id=$(this).attr('title');
	
	$.post("back/login.php",{action:"review_abuse",id:id},function(ret) {
		if(ret==1){
			alert("Deleted");
		}else{
			alert(ret);
		}
	});

});$(".review_decline").click(function() {
	
	var id=$(this).attr('title');
	
	$.post("back/login.php",{action:"review_decline",id:id},function(ret) {
		if(ret==1){
			alert("Declined to delete");
		}else{
			alert(ret);
		}
	});

});$(".ban_user").click(function() {
	
	var id=$(this).attr('title');
	$(this).closest('tr').remove();
	$.post("back/login.php",{action:"ban_user",id:id},function(ret) {
		if(ret==1){alert("Updated!");
			
		}else{
			alert(ret);
		}
	});

});$(".unban_user").click(function() {
	
	var id=$(this).attr('title');
	$(this).closest('tr').remove();
	$.post("back/login.php",{action:"unban_user",id:id},function(ret) {
		if(ret==1){
			alert("Updated!");
		}else{
			alert(ret);
		}
	});

});$(".ban_shop").click(function() {
	
	var id=$(this).attr('title');
	$(this).closest('tr').remove();
	$.post("back/login.php",{action:"ban_shop",id:id},function(ret) {
		if(ret==1){
			alert("Vendor Banned & Items disabled");
		}else{
			alert(ret);
		}
	});

});$(".unban_shop").click(function() {
	
	var id=$(this).attr('title');
	$(this).closest('tr').remove();
	$.post("back/login.php",{action:"unban_shop",id:id},function(ret) {
		if(ret==1){alert("Updated!");
			
		}else{
			alert(ret);
		}
	});

});$(".decline_shop").click(function() {
	
	var id=$(this).attr('title');
	$(this).closest('tr').remove();
	$.post("back/login.php",{action:"decline_shop",id:id},function(ret) {
		if(ret==1){alert("Updated!");
		
		}else{
			alert(ret);
		}
	});

});$(".accept_shop").click(function() {
	
	var id=$(this).attr('title');
	$(this).closest('tr').remove();
	$.post("back/login.php",{action:"accept_shop",id:id},function(ret) {
		if(ret==1){
			alert("Updated!");
		}else{
			alert(ret);
		}
	});

});$(".update_tracking").click(function() {
	
	var id=$(this).attr('title');
	 var t_ = $(this).closest('tr').find('.my_tracking_id').val();
	$.post("back/login.php",{action:"pick__",id:id,t_:t_},function(ret) {
		if(ret==1){alert("Updated!");
					}else{
			alert(ret);
		}
	});

});$(".del__").click(function() {
	
	var id=$(this).attr('title');
	$(this).closest('tr').remove();
	$.post("back/login.php",{action:"del__",id:id},function(ret) {
		if(ret==1){alert("Updated!");
		
		}else{
			alert(ret);
		}
	});

});$(".expire_coupon").click(function() {
	
	var id=$(this).attr('title');
	$.post("back/login.php",{action:"expire_coupon",id:id},function(ret) {
		if(ret==1){alert("Code Expired!");
		
		}else{
			alert(ret);
		}
	});

});$(".delete_coupon").click(function() {
	
	var id=$(this).attr('title');
	$(this).closest('tr').remove();
	$.post("back/login.php",{action:"delete_coupon",id:id},function(ret) {
		if(ret==1){alert("Code Deleted!");
		
		}else{
			alert(ret);
		}
	});

});$(".delete_cat").click(function() {
	
	var id=$(this).attr('title');
	$(this).closest('tr').remove();
	$.post("back/login.php",{action:"delete_cat",id:id},function(ret) {
		if(ret==1){alert("Category Deleted!");
		
		}else{
			alert(ret);
		}
	});

});$(".delete_banner").click(function() {
	
	var id=$(this).attr('title');
	$(this).closest('tr').remove();
	$.post("back/login.php",{action:"delete_banner",id:id},function(ret) {console.log('d');
		if(ret==1){alert("Banner Deleted!");
		
		}else{
			alert(ret);
		}
	});

});$(".join_form").on("submit",function(e) {
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
$(".coup").on("submit", function (e) {
    e.preventDefault();

    var formData = new FormData(this);
    formData.append('action', 'create_coup');

    $.ajax({
        type: 'POST',
        url: 'back/login.php', // Adjust the path to your server-side script
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res == 1) {
                alert("Coupon Created");
            } else {
                alert(res);
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});

});