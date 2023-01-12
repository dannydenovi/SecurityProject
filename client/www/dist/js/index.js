
//Array di pagine
var elements = [$("#users"), $("#logs"), $("#settings")];
var main = $("#main");
var navbar = $("#navbar");
var logout = $("#logout");

$(document).ready(function() {
    //navbar.load("dist/html/navbar.html");
    main.load("dist/html/users.html");
    logout.click(function () {
        $.ajax({
            url: "../php/loginManager.php", 
            type: "POST"
        });
    });
    
});

//Al click su uno degli elementi presenti nella topbar
//Viene caricata la pagina corrispondente e viene aggiunta la classe active
$("#users").click(function () {
    $("#main").empty();
    selectedItemMenu($("#users"));
    $("#main").load("dist/html/users.html");
});

$("#logs").click(function () {
    $("#main").empty();
    selectedItemMenu($("#logs"));
    $("#main").load("dist/html/logs.html");
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





