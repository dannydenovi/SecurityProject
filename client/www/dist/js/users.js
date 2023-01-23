
$(document).ready(function () {
    getUsers();
});

$("#add-user").click(function () {
    addUser();
})

$("#edit-user").click(function () {
    editUser();
})


function addUser(){
    var fd = new FormData();
    var files = $('#file')[0].files[0];
    fd.append('file',files);
    fd.append('name', $("#name").val());
    fd.append('surname', $("#surname").val());
    fd.append('email', $("#email").val());



    $.ajax({
        url: "../php/user.php",
        type: "POST",
        data: fd,
        contentType: false,
        processData: false,
        enctype: 'multipart/form-data',
        success: function (response) {
            var json = JSON.parse(response);
            if (json.success){
                getUsers();
                $("#add-user-modal").modal("hide");
            } else {
                alert("Errore! Controlla i campi.");
            }
        }
    });        
}


function getUsers(){
    var table = $("#usersTable")

    $.ajax({
        url: "../php/user.php",
        type: "GET",
        success: function (response) {
            var users = JSON.parse(response);
            var html = ""
            for (var i = 0; i < users.length; i++) {
                var user = users[i];
                if (user.id == 1)
                    continue;
                
                html += "<tr id=" + user.id + ">";
                html += "<td>" + user.id + "</td>";
                html += "<td>" + user.name + "</td>";
                html += "<td>" + user.surname + "</td>";
                html += "<td>" + user.email + "</td>";
                html += "<td>";
                html += "<button type='button' class='btn btn-warning m-2' onclick='getUser(" + user.id + ")'><i class='bi bi-pen' ></i></button>";
                html += "<button type='button' class='btn btn-danger m-2' onclick='deleteUser(" + user.id + ")'><i class='bi bi-trash'></i></button></td>";
                html += "</tr>";
            }
            table.empty();
            table.append(html);
        }
    });
}


function getUser(id){

    $.ajax({
        url: "../php/user.php",
        type: "GET",
        data: {
            id: id
        },
        success: function (response) {
            var user = JSON.parse(response)[0];
            $("#edit-name").val(user.name);
            $("#edit-surname").val(user.surname);
            $("#edit-email").val(user.email);
            $("#edit-id").val(user.id);

            $("#edit-user-modal").modal("show");
        }
    });

}

function editUser() {
    var id = $("#edit-id").val();
    var name = $("#edit-name").val();
    var surname = $("#edit-surname").val();
    var email = $("#edit-email").val();

    $.ajax({
        url: "../php/user.php",
        type: "PUT",
        data: {
            id: id,
            name: name,
            surname: surname,
            email: email
        },
        success: function (response) {
            console.log(response);
            var json = JSON.parse(response);
            if (json.success){
                getUsers();
                $("#edit-user-modal").modal("hide");
            } else {
                alert("Errore! Controlla i campi.");
            }
            
        }
    });
}

function deleteUser(id) {
    $.ajax({
        url: "../php/user.php",
        type: "DELETE",
        data: {
            id: id
        },
        success: function (response) {
            console.log(response);
            getUsers();
        }
    });
}
