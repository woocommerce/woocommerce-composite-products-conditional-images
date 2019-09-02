/* jshint node:true */
module.exports = function( grunt ) {

	'use strict';

	grunt.initConfig({

		// Set folder templates.
		dirs: {
			css:  'assets/css',
			js:   'assets/js'
		},

		// JavaScript linting with JSHint.
		jshint: {
			options: {
				'force': true,
				'boss': true,
				'curly': true,
				'eqeqeq': false,
				'eqnull': true,
				'es3': false,
				'expr': false,
				'immed': true,
				'noarg': true,
				'onevar': true,
				'quotmark': 'single',
				'trailing': true,
				'undef': true,
				'unused': true,
				'sub': false,
				'browser': true,
				'maxerr': 1000,
				globals: {
					'jQuery': false,
					'$': false,
					'Backbone': false,
					'_': false,
					'wp': false,
					'wc_composite_admin_params': false,
					'wc_cp_ci_admin_params': false,
					'$wc_cp_window': false
				},
			},
			all: [
				'Gruntfile.js',
				'<%= dirs.js %>/*.js',
				'!<%= dirs.js %>/*.min.js'
			]
		},

		// Remove jshint headers.
		preprocess : {
			js: {
				src : [
					'<%= dirs.js %>/*js',
					'!<%= dirs.js %>/*.min.js'
				],
				options: {
					inline : true,
				},
			},
		},

		// Minify .js files.
		uglify: {
			options: {
				preserveComments: false
			},
			jsfiles: {
				files: [{
					expand: true,
					cwd: '<%= dirs.js %>',
					src: [
						'*.js',
						'!*.min.js'
					],
					dest: '<%= dirs.js %>',
					ext: '.min.js'
				}]
			}
		},

		// Autoprefixer.
		postcss: {
			options: {
				processors: [
					require( 'autoprefixer' )( {
						browsers: [
							'> 0.1%',
							'ie 8',
							'ie 9'
						]
					} )
				]
			},
			dist: {
				src: [
					'<%= dirs.css %>/**/*.css'
				]
			}
		},

		rtlcss: {
			options: {
				config: {
					swapLeftRightInUrl: false,
					swapLtrRtlInUrl: false,
					autoRename: false,
					preserveDirectives: true
				}
			},
			main: {
				expand: true,
				ext: '-rtl.css',
				src: [
					'<%= dirs.css %>/meta-boxes-product.css',
					'<%= dirs.css %>/single-product.css'
				]
			}
		},

		// Generate POT files.
		makepot: {
			options: {
				type: 'wp-plugin',
				domainPath: 'languages',
				potHeaders: {
					'report-msgid-bugs-to': 'https://woocommerce.com/my-account/create-a-ticket/',
					'language-team': 'LANGUAGE <EMAIL@ADDRESS>'
				}
			},
			go: {
				options: {
					potFilename: 'woocommerce-composite-products-conditional-images.pot',
					exclude: [
						'languages/.*',
						'assets/.*',
						'node-modules/.*',
						'woo-includes/.*'
					]
				}
			}
		},

		// Check textdomain errors.
		checktextdomain: {
			options:{
				text_domain: [ 'woocommerce-composite-products-conditional-images' , 'woocommerce-composite-products' ],
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src:  [
					'**/*.php', // Include all files
					'!apigen/**', // Exclude apigen/
					'!deploy/**', // Exclude deploy/
					'!node_modules/**' // Exclude node_modules/
				],
				expand: true
			}
		}
	});

	// Load NPM tasks to be used here
	grunt.loadNpmTasks( 'grunt-preprocess' );
	grunt.loadNpmTasks( 'grunt-contrib-jshint' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-postcss' );
	grunt.loadNpmTasks( 'grunt-rtlcss' );
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-checktextdomain' );

	// Register tasks.
	grunt.registerTask( 'dev', [
		'checktextdomain',
		'uglify',
		'preprocess',
		'postcss',
		'rtlcss'
	] );

	grunt.registerTask( 'default', [
		'dev',
		'makepot'
	] );
};
