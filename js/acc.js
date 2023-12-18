function func4(pass) {
    if (pass.type === "password") {
      pass.type = "text";
    } else {
      pass.type = "password";
    }
}

document.getElementById("passv2").addEventListener("click", function(){ func4(document.getElementById("pass")); });
document.getElementById("passv3").addEventListener("click", function(){ func4(document.getElementById("cpass")); });

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
function confmail() {
  mail = document.getElementById("mail").value
  cmail = document.getElementById("cmail").value
  if (mail === "" || cmail === "" || cmail !== mail || cmail === origmail) {
    alert("Check the provided Info !")
    return false
  } else {
    return confirm(`You are changing your email from "${origmail}" to "${cmail}" ,\n Are you sure ?`)
  }
}
function confpass() {
  pass = document.getElementById("pass").value
  cpass = document.getElementById("cpass").value
  if (pass === "" || cpass === "" || cpass !== pass) {
    alert("Check the provided Info !")
    return false
  } else {
    return confirm(`You are changing your password to "${cpass}" ,\n Are you sure ?`)
  }
}