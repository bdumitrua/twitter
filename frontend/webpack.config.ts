/* eslint-disable @typescript-eslint/no-var-requires */
const path = require("path");
const HtmlWebpackPlugin = require("html-webpack-plugin");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const ForkTsCheckerWebpackPlugin = require("fork-ts-checker-webpack-plugin");

module.exports = {
	mode: "development",
	entry: "./src/index.tsx",
	output: {
		path: path.resolve(__dirname, "build"),
		filename: "[name].bundle.js",
		chunkFilename: "[id].[chunkhash].js",
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
						presets: [
							"@babel/preset-env",
							[
								"@babel/preset-react",
								{
									runtime: "automatic",
								},
							],
						],
					},
				},
			},
			{
				test: /\.(ts|tsx)$/,
				use: {
					loader: "ts-loader",
					options: {
						transpileOnly: true,
						experimentalWatchApi: true,
					},
				},
				exclude: /node_modules/,
			},
			{
				test: /\.css$/, // для всех CSS файлов
				use: ["style-loader", "css-loader"],
			},
			{
				test: /\.(png|jpg|gif|jpeg|svg)$/, // для изображений
				use: [
					{
						loader: "url-loader",
						options: {
							limit: 8192, // файлы меньше 8kb будут преобразованы в Data URLs
							fallback: "file-loader", // для файлов больше 8kb
							name: "images/[name].[ext]", // куда помещать изображения
						},
					},
				],
			},
			{
				test: /\.(woff(2)?|ttf|eot|otf)(\?v=\d+\.\d+\.\d+)?$/,
				type: "asset/resource",
				generator: {
					filename: "fonts/[name][ext][query]",
				},
			},

			// Сборка scss стилей. Порядок важен ибо именно в таком проядке сброщик будет выполнять сборку.
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
								localIdentName:
									"[name]__[local]___[hash:base64:5]", // Преобразовывает имя класса
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
							additionalData:
								'@import "./src/assets/styles/resources.scss";',
						},
					},
					{
						loader: "sass-resources-loader",
						options: {
							sourceMap: true,
							resources: "./src/assets/styles/resources.scss", // Путь к главному SCSS или файлам ресурсов
						},
					},

					// Автоматически добавляет стили для кроссбразурности в bundle
					{
						loader: "postcss-loader",
						options: {
							postcssOptions: {
								plugins: [require("autoprefixer")],
								sourceMap: true,
							},
						},
					},
				],
			},
		],
	},
	resolve: {
		extensions: [".js", ".jsx", ".ts", ".tsx"],
		alias: {
			"@": path.resolve(__dirname, "src"),
		},
	},

	devServer: {
		port: 3000,
		static: {
			directory: path.join(__dirname, "public"),
		},
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
		new CleanWebpackPlugin({
			cleanOnceBeforeBuildPatterns: ["!public/*"],
		}),

		new HtmlWebpackPlugin({
			template: path.resolve(__dirname, "./public/index.html"),
			filename: "index.html",
		}),
		new MiniCssExtractPlugin({
			filename: "[name].css",
			chunkFilename: "[id].css",
		}),
		new ForkTsCheckerWebpackPlugin(),
	],
};
