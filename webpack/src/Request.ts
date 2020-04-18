import * as $ from "jquery";

export default class Request
{

    private params: {[key: string]: any} = {};
    
    private endpoint: string;
    private method: string;

    private cancel: boolean = false;

    public constructor(endpoint: string, method: string)
    {
        this.endpoint = endpoint;
        this.method = method;
    }

    public setParam(name: string, val: any)
    {
        this.params[name] = val;
        return this;
    }

    public setParams(params: any)
    {
        this.params = params;
        return this;
    }

    public run<T = any>()
    {
        return new Promise<T>((res, rej) => {
            const url = `/api?endpoint=${this.endpoint}&method=${this.method}&type=json`;

            $.ajax({
                url: url,
                data: this.params,
                method: "POST",
                dataType: "json",
                error: (_hr, _status, error) => {
                    if(!this.cancel)
                    {
                        rej(error);
                    }
                },
                success: (js, _status, _hr) => {        
                    if(this.cancel)
                    {
                        return;
                    }
                    
                    try
                    {
                        if(js.error)
                        {
                            rej(js.errorTranslated);
                        }
                        else
                        {
                            res(js.data);
                        }
                    }
                    catch(e)
                    {
                        rej(e);
                    }
                },
            });
        });
    }

}