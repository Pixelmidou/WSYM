function verifsub() {
    mail = document.getElementById("mail").value
    user = document.getElementById("user").value
    if (mail === "" && user === "") {
        return false
    }
}
function verifsub2() {
    mail = document.getElementById("mail2").value
    user = document.getElementById("user2").value
    if (mail === "" && user === "") {
        return false
    }
}
function verifsub3() {
    fidate = document.getElementById("fidate").value
    fiamount = document.getElementById("fiamount").value
    if (fidate === "" && fiamount === "") {
        return false
    }
}