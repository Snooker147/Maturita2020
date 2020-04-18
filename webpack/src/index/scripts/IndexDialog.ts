import * as $ from "jquery";
import Request from "../../Request";
import Utils from "../../Utils";

export type IndexDialogParams = {
    endpoint: string;
    method: string;
    params: {[key: string]: any};
    displayField?: string;
    templateId?: string;
};

export type IndexDialogRetrievable = string | IndexDialogParams;

export type IndexDialogButtons = "yes-no" | "confirm-cancel" | "ok";
export type IndexDialogButtonResult = "yes" | "no" | "confirm" | "cancel" | "ok";

export type IndexDialogCallback = (result: IndexDialogButtonResult) => boolean | undefined;

export default class IndexDialog
{
    public static CLASS_OPEN = "index-dialog-open";
    public static BUTTON_CLASS_HIDDEN = "index-dialog-buttons-section-hidden";

    private title: IndexDialogRetrievable;
    private content: IndexDialogRetrievable;

    private buttons: IndexDialogButtons;
    private result: IndexDialogButtonResult;
    private callback: IndexDialogCallback;

    private open: boolean = true;

    public show()
    {
        $("body, html").css("overflow-y", "hidden");

        const self = this;

        const doc = this.getDialog();
        
        const title = doc.find(".index-dialog-title");
        const content = doc.find(".index-dialog-text");
        
        this.resolveRetrievable(title, this.title);
        this.resolveRetrievable(content, this.content);
        
        doc.find(".index-dialog-buttons-section").addClass(IndexDialog.BUTTON_CLASS_HIDDEN);
        
        doc.find(".index-dialog-buttons-section").each(function() {
            if($(this).attr("data-type") !== self.buttons)
            {
                return;
            }
            
            $(this).removeClass(IndexDialog.BUTTON_CLASS_HIDDEN);

            $(this).find("button").off("click").click(function() {
                const btn = $(this);
                const result = btn.attr("data-result");
                
                if(typeof self.result !== "undefined")
                {
                    return;
                }
                self.result = result as any;
                let close = true;
                
                if(self.callback)
                {
                    const callbackResult = self.callback(self.result);
                
                    if(typeof callbackResult !== "undefined")
                    {
                        close = callbackResult;
                    }
                }

                if(close)
                {
                    self.close();
                }
            });
        });

        doc.addClass(IndexDialog.CLASS_OPEN);
    }

    public close()
    {   
        this.getDialog().removeClass(IndexDialog.CLASS_OPEN);

        $("body, html").css("overflow-y", "auto");

        this.open = false;
        return this;
    }

    public setCallback(callback: IndexDialogCallback)
    {
        this.callback = callback;
        return this;
    }

    public setTitle(title: IndexDialogRetrievable)
    {
        this.title = title;
        return this;
    }

    public setContent(content: IndexDialogRetrievable)
    {
        this.content = content;
        return this;
    }

    public setButtons(btns: IndexDialogButtons)
    {
        this.buttons = btns;
        return this;
    }
    
    private resolveRetrievable(doc: JQuery, r: IndexDialogRetrievable)
    {
        const CLASS_LOADING = "index-dialog-loading";

        doc.html("Loading...");
        doc.removeClass(CLASS_LOADING);

        if(typeof r === "string")
        {
            doc.html(r);
            return true;
        }

        doc.addClass(CLASS_LOADING);

        new Request(r.endpoint, r.method).setParams(r.params).run().then(data => {
            if(!this.open)
            {
                return;
            }

            doc.removeClass(CLASS_LOADING);

            if(typeof data === "string")
            {
                doc.html(data);
                return;
            }

            if(r.templateId)
            {
                const template = $(`#${r.templateId}`).clone();
                
                template.find("[data-key]").each(function() {
                    const key = $(this).attr("data-key");
                    $(this).html(Utils.getVariable(data, key));
                });

                doc.html("").append(template);
            }
            else
            {
                doc.html(Utils.getVariable(data, r.displayField)); 
            }
        }).catch(err => {
            if(!this.open)
            {
                return;
            }

            doc.removeClass(CLASS_LOADING);
            doc.html(`Error: ${err}`);
        });

        return true;
    }

    private getDialog()
    {
        return $(".index-dialog");
    }
}