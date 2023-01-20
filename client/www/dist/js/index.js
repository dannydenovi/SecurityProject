
//Array di pagine
var elements = [$("#users"), $("#settings")];
var main = $("#main");
var navbar = $("#navbar");
var logout = $("#logout");

$(document).ready(function() {
    //navbar.load("dist/html/navbar.html");
    main.load("dist/html/users.html");
    getAdmin();
    getUsers();
    logout.click(function () {
        $.ajax({
            url: "../php/loginManager.php", 
            type: "POST"
        });
    });
    

    const myModal = document.getElementById('add-user-modal')
    const myInput = document.getElementById('add-user-btn')

    myModal.addEventListener('shown.bs.modal', () => {
        myInput.focus()
    })



});

//Al click su uno degli elementi presenti nella topbar
//Viene caricata la pagina corrispondente e viene aggiunta la classe active
$("#users").click(function () {
    $("#main").empty();
    selectedItemMenu($("#users"));
    $("#main").load("dist/html/users.html");
});

$("#settings").click(function () {
    $("#main").empty();
    selectedItemMenu($("#settings"));
    $("#main").load("dist/html/settings.html");
});

//Funzione che aggiunge la classe active all'elemento selezionato
//e rimuove la classe active agli altri elementi
function selectedItemMenu(activeElement) {
    for (var i = 0; i < elements.length; i++) {
        elements[i].removeClass("link-secondary");
        elements[i].addClass("link-dark");
    }

    activeElement.addClass("link-secondary");
    activeElement.removeClass("link-dark");
}

function getAdmin(id){
    $.ajax({
        url: "../php/user.php",
        type: "GET",
        data: {
            id: id
        },
        success: function (response) {
            var user = JSON.parse(response)[0];
            $("#namePlace").text(user.name + " " + user.surname);
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
            console.log(users);
            var html = ""
            for (var i = 0; i < users.length; i++) {
                console.log(users[i]);
                var user = users[i];

                html += "<tr id=" + user.id + ">";
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

 