function func4(pass) {
    if (pass.type === "password") {
      pass.type = "text";
    } else {
      pass.type = "password";
    }
}
document.getElementById("passv1").addEventListener("click", function(){ func4(document.getElementById("passw1")); });

function confuser() {
  uuser = document.getElementById("uuser").value
  cuuser = document.getElementById("cuuser").value
  if (uuser === "" || cuuser === "" || cuuser !== uuser || cuuser === origuser) {
    alert("Check the provided Info !")
    return false
  } else {
    return confirm(`You are changing your username from "${origuser}" to "${cuuser}" ,\n Are you sure ?`)
  }
}