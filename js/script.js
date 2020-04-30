window.onload = function() {  
    document.getElementById("id_gotologs").setAttribute("onclick", "checkForm()");
    document.getElementById("id_cancel").setAttribute("onclick", "checkFormCancel()");
    document.getElementById("id_submitbutton").setAttribute("onclick", "checkForm()");

};

function updatePostParams() {
    console.log('post');
    document.getElementById("updatePost").style.display = "block";
    document.getElementsByTagName("form")[0].action = '';
};

function updateGetParams() {
    console.log('get');
    document.getElementById("updateGet").style.display = "block";
    document.getElementsByTagName("form")[0].action = '';
};

function checkForm() {
    document.getElementsByTagName("form")[0].action = 'update.php';
    return true;
}

function checkFormCancel(){
    //Parche
    document.getElementsByTagName("form")[0].action = 'update.php';
    skipClientValidation = true;
    return true;

} 
