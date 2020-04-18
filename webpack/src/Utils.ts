class Utils
{

    public getVariable(obj: any, key: string)
    {
        const keys = key.split(".");

        let o = obj;
        for(const k of keys)
        {
            o = o[k];
        }

        return o;
    }

}

export default new Utils();