const path = require("path");
const HtmlWebpackPlugin = require("html-webpack-plugin");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const { HotModuleReplacementPlugin } = require("webpack");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
	mode: "development",
	entry: "./src/js/index.jsx",
	output: {
		path: path.resolve(__dirname, "public/js"),
		filename: "bundle.js",
		publicPath: "/",
	},
	module: {
		rules: [
			{
				test: /\.(js|jsx)$/,
				exclude: /node_modules/,
				use: {
					loader: "babel-loader",
					options: {
						presets: ["@babel/preset-env", "@babel/preset-react"],
					},
				},
			},
			{
				test: /\.css$/, // для всех CSS файлов
				use: ["style-loader", "css-loader"], // использование загрузчиков в указанном порядке
			},
			{
				test: /\.(png|jpg|gif|jpeg|svg)$/, // для изображений
				use: [
					{
						loader: "url-loader",
						options: {
							limit: 8192, // файлы меньше 8kb будут преобразованы в Data URLs
							fallback: "file-loader", // для файлов больше 8kb используйте file-loader
							name: "images/[name].[ext]", // куда помещать изображения
						},
					},
				],
			},
			{
				test: /\.scss$/,
				use: [
					process.env.NODE_ENV !== "production"
						? "style-loader"
						: MiniCssExtractPlugin.loader,
					{
						loader: "css-loader",
						options: {
							importLoaders: 2,
							modules: {
								localIdentName: "[name]__[local]___[hash:base64:5]",
							},
						},
					},
					{
						loader: "postcss-loader",
						options: {
							postcssOptions: {
								plugins: [require("autoprefixer"), require("cssnano")],
							},
						},
					},
					{
						loader: "resolve-url-loader",
					},
					{
						loader: "sass-loader",
						options: {
							sourceMap: true,
							additionalData: '@import "path/to/your/global/styles.scss";',
						},
					},
					{
						loader: "sass-resources-loader",
						options: {
							resources: "./path/to/your/global/resources.scss", // Путь к вашему главному SCSS или файлам ресурсов
						},
					},
				],
			},
		],
	},
	resolve: {
		extensions: [".js", ".jsx"],
	},
	devServer: {
		contentBase: path.join(__dirname, "public"),
		historyApiFallback: true,
		hot: true,
	},
	devtool: "eval-source-map",
	optimization: {
		splitChunks: {
			chunks: "all",
		},
	},
	plugins: [
		new CleanWebpackPlugin(),
		new HtmlWebpackPlugin({
			template: "./public/index.html",
			filename: "../index.html",
		}),
		new HotModuleReplacementPlugin(),
		new MiniCssExtractPlugin({
			filename: "[name].css",
			chunkFilename: "[id].css",
		}),
	],
};
