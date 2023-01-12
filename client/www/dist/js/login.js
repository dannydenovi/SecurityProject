$(document).ready(function () {
    var loginButton = $("#loginButton");

    loginButton.click(function () {
        var email = $("#email").val();
        var password = $("#password").val();

        console.log(email + " " + password);
        $.ajax({
            url: "../php/loginManager.php",
            type: "POST",
            data: {
                email: email,
                password: password
            },
            success: function (data) {
                var json = JSON.parse(data);
                if (json.success == true) {
                    window.location.href = "index.php";
                } else {
                    alert(json.error);
                    console.log(json)
                }
            }
        });
    });
});