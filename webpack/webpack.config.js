const Path = require("path");
const Glob = require("glob");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

const isDevelopment = process.argv.indexOf("--dev") !== -1;

module.exports = {
    mode: isDevelopment ? "development" : "production",
    entry: {
        bundle: "./src/index/Index.ts",
        admin: "./src/admin/Admin.ts",
        login: "./src/login/Login.ts",
    },
    watch: isDevelopment,
    output: {
        path: Path.resolve(__dirname, "..", "assets"),
        filename: "[name].js",
        publicPath: "/assets/",
    },
    module: {
        rules: [
            {
                test: /\.tsx?$/,
                use: "ts-loader",
                exclude: /node_modules/,
            },
            {
                test: /\.s[ac]ss$/i,
                use: [
                    isDevelopment ? "style-loader" : MiniCssExtractPlugin.loader,
                    "css-loader",
                    {
                        loader: "sass-loader",
                        options: {
                            implementation: require("sass"),
                            sassOptions: {
                                fiber: require("fibers"),
                            },
                        }
                    }
                ],
            },
            {
                test: /\.(png|jpg|jpeg|gif|svg|bmp|ttf|otf|eot|woff2|woff)$/,
                use: [
                    {
                        loader: "url-loader",
                        options: {
                            outputPath: "generated",
                            limit: 8000,
                            fallback: "file-loader?outputPath=generated"
                        }
                    }
                ]
            },
        ]
    },
    resolve: {
        extensions: [".ts", ".js"],
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: "[name].css",
            chunkFilename: "[id].css",
        }),
    ]
}