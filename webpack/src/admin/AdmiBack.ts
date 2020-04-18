/*
// Users page
    const usersTemplate = new AjaxTemplate("users");
    AjaxForm.create("#create-user-form-btn", usersTemplate);
    usersTemplate.render();

    AjaxForm.create("#edit-user-form-btn");
    AjaxForm.create("#delete-user-form-btn");
    

    // Pages page
    const pageTemplate = new AjaxTemplate("pages");
    pageTemplate.setElementBindedEvent(e => {
        const el = e.find("[data-attr-field-name='LanguageID']");
        
        el.html(getLangName(el.attr("data-language-id")));
    });

    AjaxForm.create("#create-page-form-btn", pageTemplate);
    pageTemplate.render();
    
    AjaxForm.create("#edit-page-form-btn");
    AjaxForm.create("#delete-page-form-btn");

    new HTMLEditor(
        "page-editor", 
        html => $("#page-editor-html").val(html),
        () => $("#page-editor-html").val() as string
    ).render();

    // Articles page
    const articleTemplate = new AjaxTemplate("articles");
    articleTemplate.setElementBindedEvent(e => {
        const el = e.find("[data-attr-field-name='LanguageID']");
        
        el.html(getLangName(el.attr("data-language-id")));
    });

    AjaxForm.create("#create-article-form-btn", articleTemplate);
    articleTemplate.render();
    
    AjaxForm.create("#edit-article-form-btn");
    AjaxForm.create("#delete-article-form-btn");

    new HTMLEditor(
        "article-editor", 
        html => $("#article-editor-html").val(html),
        () => $("#article-editor-html").val() as string
    ).render();

    // Events page
    const calendarTemplate = new AjaxTemplate("events");

    AjaxForm.create("#create-event-form-btn", calendarTemplate);
    calendarTemplate.render();
    
    AjaxForm.create("#edit-event-form-btn");
    AjaxForm.create("#delete-event-form-btn");*/