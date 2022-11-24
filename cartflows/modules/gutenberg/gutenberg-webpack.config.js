const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

const wp_rules = defaultConfig.module.rules.filter( function ( item ) {
	if ( String( item.test ) === String( /\.jsx?$/ ) ) {
		return true;
	}

	if ( String( item.test ) === String( /\.(sc|sa)ss$/ ) ) {
		item.exclude = [ /node_modules/, /editor/ ];
		return true;
	}
	return false;
} );

module.exports = {
	...defaultConfig,
	entry: {
		blocks: path.resolve( __dirname, 'src/blocks.js' ),
	},

	resolve: {
		alias: {
			...defaultConfig.resolve.alias,
			'@Controls': path.resolve( __dirname, 'src/controls/' ),
			'@Components': path.resolve( __dirname, 'src/components/' ),
			'@CFBlocks': path.resolve( __dirname, 'src/blocks/' ),
			'@Utils': path.resolve( __dirname, 'src/utils/' ),
			'@Attributes': path.resolve( __dirname, 'src/blocks-attributes/' ),
		},
	},
	module: {
		rules: [
			...wp_rules,
			{
				test: /\.(scss|css)$/,
				exclude: [ /node_modules/, /style/ ],
				use: [
					{
						loader: 'style-loader',
						options: {
							injectType: 'lazySingletonStyleTag',
							attributes: { id: 'uagb-editor-styles' },
						},
					},
					'css-loader',
					'sass-loader',
				],
			},
		],
	},
	// output: {
	// 	...defaultConfig.output,
	// 	// eslint-disable-next-line no-undef
	// 	path: path.resolve( __dirname, 'build' ),
	// },
};
