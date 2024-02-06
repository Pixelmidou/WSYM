function balance() {
    document.getElementById("balance").style.display = "flex";
    document.getElementById("ticket").style.display = "none";
    document.getElementById("wire").style.display = "none";
    document.getElementById("withdraw").style.display = "none";
    document.getElementById("deposit").style.display = "none";
    document.getElementById("depval").value = ""
    document.getElementById("withval").value = ""
    document.getElementById("wireval").value = ""
    document.getElementById("wireres").value = ""
    document.getElementById("tick").value = ""
}
function deposit() {
    document.getElementById("balance").style.display = "none";
    document.getElementById("ticket").style.display = "none";
    document.getElementById("wire").style.display = "none";
    document.getElementById("withdraw").style.display = "none";
    document.getElementById("deposit").style.display = "flex";
    document.getElementById("depval").value = ""
    document.getElementById("withval").value = ""
    document.getElementById("wireval").value = ""
    document.getElementById("wireres").value = ""
    document.getElementById("tick").value = ""
}
function withdraw() {
    document.getElementById("balance").style.display = "none";
    document.getElementById("ticket").style.display = "none";
    document.getElementById("wire").style.display = "none";
    document.getElementById("withdraw").style.display = "flex";
    document.getElementById("deposit").style.display = "none";
    document.getElementById("depval").value = ""
    document.getElementById("withval").value = ""
    document.getElementById("wireval").value = ""
    document.getElementById("wireres").value = ""
    document.getElementById("tick").value = ""
}
function wire() {
    document.getElementById("balance").style.display = "none";
    document.getElementById("ticket").style.display = "none";
    document.getElementById("wire").style.display = "flex";
    document.getElementById("withdraw").style.display = "none";
    document.getElementById("deposit").style.display = "none";
    document.getElementById("depval").value = ""
    document.getElementById("withval").value = ""
    document.getElementById("wireval").value = ""
    document.getElementById("wireres").value = ""
    document.getElementById("tick").value = ""
}
function ticket() {
    document.getElementById("balance").style.display = "none";
    document.getElementById("ticket").style.display = "flex";
    document.getElementById("wire").style.display = "none";
    document.getElementById("withdraw").style.display = "none";
    document.getElementById("deposit").style.display = "none";
    document.getElementById("depval").value = ""
    document.getElementById("withval").value = ""
    document.getElementById("wireval").value = ""
    document.getElementById("wireres").value = ""
    document.getElementById("tick").value = ""

}
document.getElementById("balancev").addEventListener("click", balance);
document.getElementById("depositv").addEventListener("click", deposit);
document.getElementById("withdrawv").addEventListener("click", withdraw);
document.getElementById("wirev").addEventListener("click", wire);
document.getElementById("ticketv").addEventListener("click", ticket);

document.getElementById("ten").addEventListener("click", function(){ document.getElementById("depval").value = 10; });
document.getElementById("twenty").addEventListener("click", function(){ document.getElementById("depval").value = 20; });
document.getElementById("thirty").addEventListener("click", function(){ document.getElementById("depval").value = 30; });
document.getElementById("forty").addEventListener("click", function(){ document.getElementById("depval").value = 40; });
document.getElementById("fifty").addEventListener("click", function(){ document.getElementById("depval").value = 50; });

document.getElementById("tenn").addEventListener("click", function(){ document.getElementById("withval").value = 10; });
document.getElementById("twentyy").addEventListener("click", function(){ document.getElementById("withval").value = 20; });
document.getElementById("thirtyy").addEventListener("click", function(){ document.getElementById("withval").value = 30; });
document.getElementById("fortyy").addEventListener("click", function(){ document.getElementById("withval").value = 40; });
document.getElementById("fiftyy").addEventListener("click", function(){ document.getElementById("withval").value = 50; });

document.getElementById("tennn").addEventListener("click", function(){ document.getElementById("wireval").value = 10; });
document.getElementById("twentyyy").addEventListener("click", function(){ document.getElementById("wireval").value = 20; });
document.getElementById("thirtyyy").addEventListener("click", function(){ document.getElementById("wireval").value = 30; });
document.getElementById("fortyyy").addEventListener("click", function(){ document.getElementById("wireval").value = 40; });
document.getElementById("fiftyyy").addEventListener("click", function(){ document.getElementById("wireval").value = 50; });

function depositconfirm() {
    depval = document.getElementById("depval").value
    if (depval == "") {
        return false
    } else {
        return confirm(`You are depositing $${depval} into your account,\n Are you sure ?`)
    }
}
function withdrawconfirm() {
    withval = document.getElementById("withval").value
    if (withval == "") {
        return false
    } else {
        return confirm(`You are withdrawing $${withval} from your account,\n Are you sure ?`)
    }
}
function wireconfirm() {
    wireval = document.getElementById("wireval").value
    wireres = document.getElementById("wireres").value
    if (wireval == "" || wireres == "") {
        return false
    } else {
        return confirm(`You are sending $${wireval} to ${wireres} ,\n Are you sure ?`)
    }
}
function ticketconfirm() {
    tick = document.getElementById("tick").value
    if (tick == "") {
        return false
    } else {
        return confirm("Are you sure you want to submit this ticket ?")
    }
}
function logoutconfirm() {
    return confirm("Are you sure you want to logout ?")
}
function resendconfirm() {
    return confirm("Are you sure you want to send a new email verification ?")
}