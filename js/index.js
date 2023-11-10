function func1() {
    document.getElementById("loginpage").style.display = "none";
    document.getElementById("forgotpass").style.display = "none";
    document.getElementById("createaccount").style.display = "flex";
    document.getElementById("user1").value = ""
    document.getElementById("passw1").value = ""
    document.getElementById("user2").value = ""
    document.getElementById("passw2").value = ""
    document.getElementById("email2").value = ""
    document.getElementById("email3").value = ""
}
function func2() {
    document.getElementById("loginpage").style.display = "none";
    document.getElementById("createaccount").style.display = "none";
    document.getElementById("forgotpass").style.display = "flex";
    document.getElementById("user1").value = ""
    document.getElementById("passw1").value = ""
    document.getElementById("user2").value = ""
    document.getElementById("passw2").value = ""
    document.getElementById("email2").value = ""
    document.getElementById("email3").value = ""
}
function func3() {
    document.getElementById("forgotpass").style.display = "none";
    document.getElementById("createaccount").style.display = "none";
    document.getElementById("loginpage").style.display = "flex";
    document.getElementById("user1").value = ""
    document.getElementById("passw1").value = ""
    document.getElementById("user2").value = ""
    document.getElementById("passw2").value = ""
    document.getElementById("email2").value = ""
    document.getElementById("email3").value = ""
}
function func4(pass) {
    if (pass.type === "password") {
      pass.type = "text";
    } else {
      pass.type = "password";
    }
}
document.getElementById("passv1").addEventListener("click", function(){ func4(document.getElementById("passw1")); });
document.getElementById("passv2").addEventListener("click", function(){ func4(document.getElementById("passw2")); });