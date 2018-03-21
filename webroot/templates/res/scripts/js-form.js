// JavaScript Document

$(document).ready(function() {
// Форма обратной связи................................./

var regVr22 = "<div><img style='margin-bottom:-4px;' src='/resources/img/load.gif' alt='Отправка...' width='16' height='16'><span style='font: 13px; color:#333; margin-left:6px;'>Сообщение обрабатывается...</span></div><br />";

$("#send").click(function(){
		$("#loadBar").html(regVr22).show();
		var posName = $("#posName").val();
		var posEmail = $("#posEmail").val();
		var posText = $("#posText").val();
		$.ajax({
			type: "POST",
			url: "/otzivi/send.php",
			data: {"posName": posName, "posEmail": posEmail, "posText": posText},
			cache: false,
			success: function(response){
		var messageResp = "<p style='font-size:14px; color:green; border:1px solid #00CC00; padding:10px; margin:20px; border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px; background-color:#fff;'>Спасибо, <strong>";
		var resultStat = "!</strong><br> Ваше сообщение отправлено!</p>";
		var oll = (messageResp + posName + resultStat);
				if(response == 1){
				$("#loadBar").html(oll).fadeIn(3000);
				$("#posName").val("");
				$("#posEmail").val("");
				$("#posText").val("");
				} else {
		$("#loadBar").html(response).fadeIn(3000); }
										}
		});
		return false;
});


});
