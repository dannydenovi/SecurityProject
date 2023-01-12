$(document).ready(function () {
    getUserInfos();

    $("#save").click(function () {
        var name = $("#name").val();
        var surname = $("#surname").val();
        var email = $("#email").val();
        var password = $("#password").val();
        var passwordConfirmation = $("#passwordConfirmation").val();


        $.ajax({
            url: "../../php/user.php",
            type: "PUT",
            data: {
                name: name,
                surname: surname,
                email: email,
                password: password,
                passwordConfirmation: passwordConfirmation
            },
            success: function (response) {
                var response = JSON.parse(response);
                if (response.success) {
                    alert("Modifiche salvate con successo!");
                    location.reload();
                } else {
                    alert("Errore! Controlla i campi.");
                }
            }
        });
    });

});


function getUserInfos() {
    $.ajax({
        url: "../../php/user.php",
        type: "GET",
        data: {
            id: 1
        },
        success: function (response) {
            var user = JSON.parse(response);
            $("#name").val(user.name);
            $("#surname").val(user.surname);
            $("#email").val(user.email);
        }
    });
}