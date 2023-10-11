const path = require("path");

module.exports = {
	mode: "development",
	entry: "./src/js/index.jsx",
	output: {
		path: path.resolve(__dirname, "public/js"),
		filename: "index.jsx",
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
		],
	},
	resolve: {
		extensions: [".js", ".jsx"],
	},
	devServer: {
		historyApiFallback: true,
	},
};
