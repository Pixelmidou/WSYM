/* js/welcome.js - the dashboard is one page with four panels; this shows one at a time. */
const PANELS = ["balance", "deposit", "withdraw", "wire", "ticket"];

function showPanel(name) {
    PANELS.forEach(function (id) {
        const el = document.getElementById(id);
        if (el) { el.style.display = (id === name) ? "flex" : "none"; }
    });
    ["depval", "withval", "wireval", "wireres", "tick"].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) { el.value = ""; }
    });
}

/* the five predefined value buttons of each panel */
function preset(prefix, field) {
    ["ten", "twenty", "thirty", "forty", "fifty"].forEach(function (name, i) {
        const button = document.getElementById(name + prefix);
        if (button) {
            button.addEventListener("click", function () {
                document.getElementById(field).value = (i + 1) * 10;
            });
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const menu = { balancev: "balance", depositv: "deposit", withdrawv: "withdraw", wirev: "wire" };
    Object.keys(menu).forEach(function (id) {
        const el = document.getElementById(id);
        if (el) { el.addEventListener("click", function () { showPanel(menu[id]); }); }
    });
    preset("", "depval");
    preset("n", "withval");
    preset("nn", "wireval");
});
