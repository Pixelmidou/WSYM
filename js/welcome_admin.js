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
function verifsub4() {
    mailb = document.getElementById("mailb").value
    userb = document.getElementById("userb").value
    if (mailb === "" && userb === "") {
        return false
    }
}
function verifsub5() {
    mailbacc = document.getElementById("mailbacc").value
    userbacc = document.getElementById("userbacc").value
    if (mailbacc === "" && userbacc === "") {
        return false
    }
}
function logoutconfirm() {
    return confirm("Are you sure you want to logout ?")
}