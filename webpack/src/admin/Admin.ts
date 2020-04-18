import * as $ from "jquery";

import "./Admin.scss";
import AjaxForm from "../AjaxForm";
import AjaxTemplate from "../AjaxTemplate";
import HTMLEditor from "../HTMLEditor";

$(document).ready(() => {
    
    $(".admin-error-close").click(function() {
        const parent = $(this).parent();

        parent.addClass("admin-error-hidden");
    });

    // For form dialog buttons
    $(".admin-dialog-button").click(function() {
        const container = $(".admin-dialog-container");
        
        if(container.is(":animated"))
        {
            return;
        }

        container.animate({ opacity: "0", top: "-10px" }, 100, () => {
            container.css("top", "-100%").css("bottom", "100%");
        });
    });

    // general form
    $(".admin-form").each(function() {
        const id = $(this).attr("data-id");
        const useList = parseInt($(this).attr("data-use-list"), 10) != 0;
        const useHtml = parseInt($(this).attr("data-use-html"), 10) != 0;
        const useDelete = parseInt($(this).attr("data-use-delete"), 10) != 0;

        const template = useList ? new AjaxTemplate(`${id}-ajax`) : null;
        AjaxForm.create(`#${id}-form-btn`, template);

        if(useList)
        {
            template.render();
        }

        if(useHtml)
        {
            new HTMLEditor(
                `${id}-html-editor`, 
                html => $(`#${id}-html-editor-dest`).val(html),
                () => $(`#${id}-html-editor-dest`).val() as string
            ).render();
        }

        if(useDelete)
        {
            AjaxForm.create(`#delete-${id}-form-btn`);
        }
    });

    new AjaxTemplate("feedback-ajax").render();
});