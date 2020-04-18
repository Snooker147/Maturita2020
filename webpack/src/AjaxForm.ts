import * as $ from "jquery";
import AjaxTemplate from "./AjaxTemplate";

export default class AjaxForm
{
    public static create(btnSelector: string, formTemplate: AjaxTemplate = null)
    {
        const self = $(btnSelector);

        const form = $("#" + self.attr("data-form-id"));

        const redirect = self.attr("data-success-redirect") || null;

        form.find("input, select").change(function() {
            form.find(".admin-button-container span").removeClass("span-shown");
        });

        self.click(function(e) {
            e.preventDefault();
            
            const doc = form[0] as HTMLFormElement;
            const url = doc.action;

            const postData: {[key: string]: any} = {};

            form.find("input, select").each(function() {
                const self = $(this);

                if(self.attr("type") === "submit")
                {
                    return;
                }

                const val = self.val();

                if(typeof val === "string")
                {
                    if(val.length === 0)
                    {
                        return;
                    }
                }

                postData[self.attr("name")] = self.val();
            });
            
            $(this).attr("disabled", "true");

            const finalize = () => {
                $(this).removeAttr("disabled");
            };

            const finalizeData = (data: any) => {
                form.find("span").addClass("span-shown");
                
                if(redirect !== null)
                {
                    window.location.replace(`${window.location.origin}/${redirect}`);
                }
                else if(formTemplate)
                {
                    formTemplate.render(0);
                }
            };

            const finalizeError = (err: string) => {
                $(".admin-dialog-content").text(err);
                $(".admin-dialog-container").css("top", "0").css("bottom", "0").animate({opacity: "1"}, 100);
            };

            $.ajax({
                url: url,
                data: postData,
                method: "POST",
                dataType: "json",
                error: (_hr, _status, error) => {
                    finalize();
                    finalizeError(error);
                },
                success: (js, _status, _hr) => {
                    finalize();
                            
                    try
                    {
                        if(js.error)
                        {
                            finalizeError(js.errorTranslated);
                        }
                        else
                        {
                            finalizeData(js.data);
                        }
                    }
                    catch(e)
                    {
                        finalizeError(e);
                    }
                },
            });
        });
    }
}