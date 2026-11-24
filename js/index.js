/* js/index.js - switch between the three panels of the front page. */
function showPanel(which) {
    ["loginpage", "createaccount", "forgotpass"].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) { el.style.display = (id === which) ? "flex" : "none"; }
    });
    ["user1", "passw1", "user2", "passw2", "email2", "email3"].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) { el.value = ""; }
    });
}

function func1() { showPanel("createaccount"); }
function func2() { showPanel("forgotpass"); }
function func3() { showPanel("loginpage"); }

/* the little eye checkbox next to a password field */
function func4(pass) { pass.type = (pass.type === "password") ? "text" : "password"; }

document.addEventListener("DOMContentLoaded", function () {
    const eye1 = document.getElementById("passv1");
    const eye2 = document.getElementById("passv2");
    if (eye1) { eye1.addEventListener("click", function () { func4(document.getElementById("passw1")); }); }
    if (eye2) { eye2.addEventListener("click", function () { func4(document.getElementById("passw2")); }); }
});
