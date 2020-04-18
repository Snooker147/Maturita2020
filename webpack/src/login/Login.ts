import * as $ from "jquery";

import "./Login.scss";

$(document).ready(() => {
    $("#login-error-close-btn").click(() => {
        $(".login-error").css("opacity", "0");
    });
});