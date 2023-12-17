function func4(pass) {
    if (pass.type === "password") {
      pass.type = "text";
    } else {
      pass.type = "password";
    }
}
document.getElementById("passv1").addEventListener("click", function(){ func4(document.getElementById("passw1")); });