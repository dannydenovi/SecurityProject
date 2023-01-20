$("#add-user").click(function () {
    addUser();
})


function addUser(){
    console.log("click");
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
            console.log(response);
        }
    });        
}

