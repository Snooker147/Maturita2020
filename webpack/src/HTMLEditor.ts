// @ts-ignore
import suneditor from "suneditor";
import plugins from 'suneditor/src/plugins'

type EditFn = (html: string) => void;
type InitFn = () => string;

export default class HTMLEditor
{

    private target: string;
    private onEdited: EditFn;
    private onInit: InitFn;

    public constructor(target: string, edit: EditFn, init: InitFn)
    {
        this.target = target;
        this.onEdited = edit;
        this.onInit = init;
    }

    public render()
    {
        const editor = suneditor.create(this.target, {
            plugins: plugins,
            buttonList: [
                ['font', 'fontColor', 'fontSize'],
                ['undo', 'redo'],
                ['bold', 'underline', 'italic', 'strike', 'subscript', 'superscript'],
                ['hiliteColor', 'horizontalRule', 'list', 'table', 'link']
            ],
            height: 500,
            placeholder: "",
        });
        editor.onChange = (c: string) => this.onEdited(c);
        editor.setContents(this.onInit());

    }

}