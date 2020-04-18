import * as $ from "jquery";

type ArrayResponse = {
    items: any;
    count: number;
    countPages: number;
    currentPage: number;
}

type ElementBindedEvent = (el: JQuery<HTMLElement>) => void;

export default class AjaxTemplate
{
    private destinationId: string;
    private destination: JQuery<HTMLElement>;

    private currentPage = 0;
    private totalPages = 0;

    private isRendering = false;

    private bindEvent: ElementBindedEvent = null;

    public constructor(dstId: string)
    {
        this.destinationId = dstId;
        this.destination = $(`#${dstId}`);

        const self = this;

        this.getPrevButton().click(function() {
            if($(this).hasClass("disabled"))
            {
                return;
            }
            
            self.render(-1);
        });

        this.getNextButton().click(function() {
            if($(this).hasClass("disabled"))
            {
                return;
            }
            
            self.render(1);
        });

        this.getRefreshButton().click(function() {
            if($(this).hasClass("disabled"))
            {
                return;
            }
            
            self.render(0);
        });
    }

    public render(pageDelta: number = null)
    {  
        if(this.destination.length === 0)
        {
            console.warn(`Tried to render AjaxTemplate on object that does not exist: ${this.destinationId}`);
            return;
        }

        if(this.isRendering)
        {
            console.error(`Cant render ${this.destination} twice at the same time!`);
            return;
        }

        this.isRendering = true;

        const dst = this.getDestination();

        dst.html("");

        this.getLoadingTemplate().removeClass(this.getProvidedHiddenLoading());
        this.getErrorTemplate().addClass(this.getProvidedHiddenError());
        this.getHeaderTemplate().addClass(this.getProvidedHiddenHeader());
        
        const url = dst.attr("data-url");

        if(pageDelta !== null)
        {
            this.setProvidedPage(this.currentPage + pageDelta);
        }

        this.getInsertionCurrentPage().html((this.getProvidedPage() + 1).toString());
        
        setTimeout(() => {
            $.ajax({
                url: url,
                method: "POST",
                data: this.getPostData(),
                dataType: "json",
                error: (hr, status, error) => 
                {
                    this.finalize(null, error);
                },
                success: (js, status, hr) => {
                    if(js.error)
                    {
                        this.finalize(null, js.errorTranslated);
                    }
                    else if(js.data)
                    {
                        this.finalize(js.data, null);
                    }
                },
            });
        }, this.getProvidedDelay(1000));
    }

    private finalize(data: ArrayResponse, error: string)
    {
        this.getLoadingTemplate().addClass(this.getProvidedHiddenLoading());
        
        const dst = this.getDestination();
        dst.html("");

        this.isRendering = false;

        if(error)
        {
            const temp = this.getErrorTemplate().removeClass(this.getProvidedHiddenError());

            temp.find("[data-insert-error='here']").html(error);

            return;
        }

        this.getHeaderTemplate().removeClass(this.getProvidedHiddenHeader());

        for (const item of data.items)
        {
            const copy = this.getTemplate();
            copy.removeClass("template");
            
            for (const key in item)
            {
                const val = item[key];
                const field = copy.find(`[data-field='${key}']`);

                copy.find(`[data-attr-field-name='${key}']`).each(function() {
                    const el = $(this);

                    const dstName = el.attr("data-attr-field-dest");
                    const attrVal = el.attr(dstName);

                    if(typeof attrVal === "undefined" || attrVal.length === 0)
                    {
                        el.attr(dstName, val);
                    }
                    else
                    {
                        el.attr(dstName, attrVal + val);
                    }
                });

                field.html(val);
            }

            if(this.bindEvent)
            {
                this.bindEvent(copy);
            }

            dst.append(copy);
        }

        this.setProvidedPage(data.currentPage);
        this.currentPage = data.currentPage;
        this.totalPages = data.countPages;

        if(this.currentPage + 1 >= this.totalPages)
        {
            this.getNextButton().addClass("disabled");
        }
        else
        {
            this.getNextButton().removeClass("disabled");
        }

        if(this.currentPage - 1 < 0)
        {
            this.getPrevButton().addClass("disabled");
        }
        else
        {
            this.getPrevButton().removeClass("disabled");
        }

        this.getInsertionTotalPages().html(data.countPages.toString());
    }

    private getPostData()
    {
        const postData: {[key: string]: any} = {};

        const dst = this.getDestination();
        const attribs = dst[0].attributes;

        for(let i = 0; i < attribs.length; i++)
        {
            const node = attribs.item(i);

            const prefix = "data-post-";
            const index = node.name.indexOf(prefix);

            if(index === 0)
            {
                const name = node.name.substring(prefix.length);
                const value = node.value;

                postData[name] = value;
            }
        }

        postData["page"] = this.getProvidedPage();

        return postData;
    }

    public getCurrentPage()
    {
        return this.currentPage;
    }

    public getTotalPages()
    {
        return this.totalPages;
    }

    public setElementBindedEvent(e: ElementBindedEvent)
    {
        this.bindEvent = e; 
    }

    private getProvidedDelay(def: number)
    {
        const delay = this.getDestination().attr("data-delay");

        if(!delay || delay.length === 0)
        {
            return def;
        }

        return parseInt(delay);
    }

    private getProvidedHiddenHeader(def: string = "template")
    {
        const cls = this.getDestination().attr("data-class-hidden-header");

        if(!cls || cls.length === 0)
        {
            return def;
        }

        return cls;
    }

    private getProvidedHiddenLoading(def: string = "template")
    {
        const cls = this.getDestination().attr("data-class-hidden-loading");

        if(!cls || cls.length === 0)
        {
            return def;
        }

        return cls;
    }

    private getProvidedHiddenError(def: string = "template")
    {
        const cls = this.getDestination().attr("data-class-hidden-error");

        if(!cls || cls.length === 0)
        {
            return def;
        }

        return cls;
    }


    private getProvidedPage()
    {
        return parseInt(this.getDestination().attr("data-page"));
    }

    private setProvidedPage(page: number)
    {
        this.getDestination().attr("data-page", page);
    }

    private getInsertionCurrentPage()
    {
        return $("." + this.getDestination().attr("data-insertion-current-page"));
    }

    private getInsertionTotalPages()
    {
        return $("." + this.getDestination().attr("data-insertion-total-pages"));
    }

    private getTemplate()
    {
        return $("#" + this.getDestination().attr("data-template")).clone().removeClass("template");
    }

    private getPrevButton()
    {
        return $("." + this.getDestination().attr("data-button-prev"));
    }

    private getNextButton()
    {
        return $("." + this.getDestination().attr("data-button-next"));
    }

    public getRefreshButton()
    {
        return $("." + this.getDestination().attr("data-button-refresh"));
    }

    public getHeaderTemplate()
    {
        return $("#" + this.getDestination().attr("data-template-header"));
    }

    private getErrorTemplate()
    {
        return $("#" + this.getDestination().attr("data-template-error"));
    }

    private getLoadingTemplate()
    {
        return $("#" + this.getDestination().attr("data-template-loading"));
    }

    private getDestination()
    {
        return this.destination;
    }

}