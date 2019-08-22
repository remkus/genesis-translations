#!/usr/bin/env node

// This script downloads translation files from the Genesis GlotPress language
// server at https://translate.studiopress.com/global/projects/. It was ported
// from grunt-glotpress: https://github.com/markoheijnen/grunt-glotpress.
//
// Type the `npm run translations` command to run it.
//
// The script expects options to appear under a glotpress key in package.json:
//
// "glotpress": {
// 	"url": "https://translate.studiopress.com/global/",
// 	"slug": "genesis/genesis",
// 	"textdomain": "genesis",
// 	"fileFormat": "lib/languages/%wpLocale%.%format%",
// 	"formats": [
// 		"po",
// 		"mo"
// 	],
// 	"filters": {
// 		"minimumPercentage": 30
// 	}
// },

const axios = require( 'axios' );
const fs = require( 'fs' );

const package = require( process.cwd() + '/package.json' );
const options = package.glotpress;

axios.defaults.headers.common['User-Agent'] = `${package.glotpress.textdomain}`;

const translations = {
	apiUrl: '',

	/**
	 * Get project data and download the translations.
	 */
	init: function() {
		if ( ! options ) {
			console.log( 'Could not find glotpress key in package.json' );
			return;
		}

		translations.apiUrl = options.url + 'api/projects/' + options.slug;
		translations.getProjectData().then( translations.downloadAll );
	},

	/**
	 * Get language sets that meet the minimum thresholds.
	 *
	 * Thresholds are specified in `package.json` under `glotpress.filters`.
	 *
	 * Example data endpoint: https://translate.studiopress.com/global/api/projects/genesis/genesis
	 *
	 * @return {Promise<array|void>} A promise resolving to an array of the
	 *                               filtered language sets, or void if sets
	 *                               could not be fetched.
	 */
	getProjectData: function() {
		console.log( `Requesting translation data from ${translations.apiUrl}` );

		return axios.get( translations.apiUrl ).then( response => {
			let filteredTranslationSets;

			if ( 200 === response.status ) {
				filteredTranslationSets = response.data.translation_sets.filter( set => {
					return set.percent_translated >= options.filters.minimumPercentage;
				});

				return filteredTranslationSets;
			}

			console.log(
				`Unable to fetch project data. Response: ${response.status} ${response.statusText}`
			);
		});
	},

	/**
	 * Download given language sets for formats listed in the config (mo/po).
	 *
	 * @param {array} sets The translation sets from the GlotPress server.
	 */
	downloadAll: function( sets ) {
		sets.forEach( set => {
			options.formats.forEach( format => {
				translations.downloadSet( set, format );
			});
		});
	},

	/**
	 * Download the translation file for the given translation set and format.
	 *
	 * Example download: https://translate.studiopress.com/global/api/projects/genesis/genesis/en-gb/default/export-translations?format=po
	 *
	 * @param {object} set Information returned by the GlotPress server for an
	 *                     individual set, such as 'en_GB'.
	 * @param {string} format The translation format, such as 'mo' or 'po'.
	 */
	downloadSet: function( set, format ) {
		const url =
			translations.apiUrl +
			'/' +
			set.locale +
			'/' +
			set.slug +
			'/export-translations?format=' +
			format;

		const info = {
			domainPath: options.domainPath || '',
			textdomain: options.textdomain,
			locale: set.locale,
			wpLocale: set.wp_locale || set.locale,
			slug: set.slug,
			slugSuffix: 'default' === set.slug ? '' : '-' + set.slug,
			format: format
		};

		translations.getFile( url, translations.buildFilename( info ) );
	},

	/**
	 * Get the local file path and name to use for the downloaded file.
	 *
	 * Substitutes placeholders wrapped in `#` in the `glotpress.fileFormat`
	 * value in `package.json` with properties from the supplied setInfo
	 * object. For example, `lib/languages/%wpLocale%.%format%"` might become
	 * `lib/languages/en-GB.mo"`.
	 *
	 * @param {object} setInfo Information about the translation set.
	 * @return {string} The local filename relative to the project root.
	 */
	buildFilename: function( setInfo ) {
		return options.fileFormat.replace( /%(\w*)%/g, ( _, key ) => {
			return setInfo.hasOwnProperty( key ) ? setInfo[key] : '';
		});
	},

	/**
	 * Get a file from a given URL and save it with the provided filename.
	 *
	 * @param {string} url The URL to download.
	 * @param {string} filename The file path and name to write to,
	 *                          relative to the project root.
	 */
	getFile: function( url, filename ) {
		axios({
			method: 'get',
			url: url,
			responseType: 'arraybuffer'
		}).then( response => {
			if ( 200 === response.status ) {
				const encoding = filename.includes( '.mo' ) ? 'binary' : 'utf8';
				fs.writeFile(
					process.cwd() + '/' + filename,
					response.data,
					encoding,
					err => {
						if ( err ) {
							console.log( `Could not write language file: ${err}` );
							return;
						}
						console.log( `Writing language file ${filename}` );
					}
				);
			} else {
				console.log(
					`Could not fetch file. Response: ${response.status} ${response.statusText}`
				);
			}
		});
	}
};

translations.init();
