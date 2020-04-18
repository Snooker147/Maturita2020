import * as $ from "jquery";

import "./Index.scss";
import HeaderMenu from "./scripts/HeaderMenu";
import IndexDialog from "./scripts/IndexDialog";

const updateResponsiveHeader = () =>
{
    const wrapper = $(".header-content-wrapper");
    const mobileWrapper = $(".header-mobile");

    const currentWidth = wrapper.innerWidth();
    const imageWidth = $(".header-content-wrapper img").innerWidth();
    const buttonsWidth = $(".header-buttons").innerWidth();

    const remainingSpace = currentWidth - imageWidth - buttonsWidth;

    if(remainingSpace < 1)
    {
        wrapper.addClass("header-content-wrapper-mobile");
        mobileWrapper.addClass("header-mobile-show");
    }
    else
    {
        wrapper.removeClass("header-content-wrapper-mobile");
        mobileWrapper.removeClass("header-mobile-show");
    }

    const headerVideo = $(".header-video-self");
    const windowWidth = $(window).innerWidth();
    const windowHeight = $(window).innerHeight();

    const headerVideoClass16to9 = "video-16-9";
    const headerVideoClass4to3 = "video-4-3";

    headerVideo.removeClass(headerVideoClass16to9);
    headerVideo.removeClass(headerVideoClass4to3);
    headerVideo.addClass(windowWidth > windowHeight ? headerVideoClass16to9 : headerVideoClass4to3);
    
    mobileButtonClick(false);
}

const mobileButtonClick = (force: boolean = null) => 
{
    const className = "header-mobile-open";
    const mobile = $(".header-mobile");
    
    let isOpen = !mobile.hasClass(className);

    if(force !== null)
    {
        isOpen = force;
    }

    if(isOpen)
    {
        $("html, body").css("overflow-y", "hidden");
        mobile.addClass(className);
    }
    else
    {
        $("html, body").css("overflow-y", "auto");
        mobile.removeClass(className);
    }
}

$(document).ready(() => {
    // Standard header logic
    new HeaderMenu(
        ".header-buttons", // container
        ".header-button-wrapper", // each button and their contents
        "header-button-wrapper-show",  // class to add when the button is opened
        ".header-button-menu-content" // class to calculate height on show
    ).init(); 
    
    // Mobile header logic
    new HeaderMenu(
        ".header-mobile-buttons", // container
        ".header-mobile-button-wrapper", // each button and their contents
        "header-mobile-button-wrapper-show",  // class to add when the button is opened
        ".header-mobile-button-content" // class to calculate height on show
    ).setStrategy("click").init(); 
    
    $(".header-mobile-menu-btn").click(() => mobileButtonClick());

    // when resizing
    $(window).resize(updateResponsiveHeader);

    updateResponsiveHeader();
    
    $(".calendar-day-colors div").click(function() {
        new IndexDialog()
        .setTitle($(this).attr("data-event-message"))
        .setContent({
            endpoint: "events",
            method: "get-event-detail",
            params: { id: $(this).attr("data-event-id") },
            templateId: "calendar-dialog-event-template"
        })
        .setButtons("ok")
        .show();
    });

    const PREVIEW_OPEN = "preview-open";
    
    $(".page-gallery-photo").click(function() {
        const preview = $(".page-gallery-photo-preview");
        const src = $(this).find("img").attr("src");

        preview.find("img").attr("src", src);
        preview.addClass(PREVIEW_OPEN);

        $("body, html").css("overflow-y", "hidden");
    });

    $(".page-gallery-close-btn").click(function() {
        $(".page-gallery-photo-preview").removeClass(PREVIEW_OPEN);
        $("body, html").css("overflow-y", "auto");
    });

});