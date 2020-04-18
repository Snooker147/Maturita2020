import * as $ from "jquery";

type HeaderMenuStrategy = "click" | "hover";

export default class HeaderMenu
{

    private strategy: HeaderMenuStrategy = "hover";

    private containerSelector: string;

    private menuSelector: string;
    private menuShowClass: string;

    private menuContentSelector: string;

    public constructor(containerSelector: string, menuSelector: string, menuShowClass: string, menuContentSelector: string)
    {
        this.containerSelector = containerSelector;
        this.menuSelector = menuSelector;
        this.menuShowClass = menuShowClass;
        this.menuContentSelector = menuContentSelector;
    }

    public init()
    {
        const self = this;

        this.getMenus().each(function() {
            const menu = $(this);

            const btn = menu.find("button");

            if(self.strategy === "click")
            {
                btn.click(function() {
                    self.setStateAll(false, self.getTarget($(this)));
                    self.setState(!menu.hasClass(self.menuShowClass), menu);
                });
            }
            else
            {
                menu.mouseenter(function() {
                    self.setStateAll(false, self.getTarget($(this)));
                    self.setState(true, menu);
                });

                menu.mouseleave(function() {
                    self.setStateAll(false);
                });
            }

            self.setState(false, menu);
        });
    }

    private setStateAll(open: boolean, exceptTarget: string = null)
    {
        const self = this;

        this.getMenus().each(function() {
            if(exceptTarget && self.getTarget($(this).find("button")) === exceptTarget)
            {
                return;
            }

            self.setState(open, $(this));    
        });
    }

    private setState(open: boolean, menu: JQuery<HTMLElement>)
    {
        const contents = menu.find(this.menuContentSelector);

        if(open)
        {
            let actualH = 0;

            contents.children().each(function() {
                actualH += $(this).outerHeight();
            });

            contents.css("height", `${actualH}px`);
            menu.addClass(this.menuShowClass);
        }
        else
        {
            contents.css("height", "0");
            menu.removeClass(this.menuShowClass);
        }
    }

    public getContainer()
    {
        return $(this.containerSelector);
    }

    public getMenus()
    {
        return $(this.containerSelector).find(this.menuSelector);
    }

    public getTarget(el: JQuery<HTMLElement>)
    {
        return el.attr("data-menu-target");
    }

    public setStrategy(s: HeaderMenuStrategy)
    {
        this.strategy = s;
        return this;
    }

}