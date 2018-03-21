/* jshint node:true */
module.exports = function(grunt) {
  var SOURCE_DIR = './',
    BUILD_DIR = 'build/';

  grunt.initConfig({
    glotpress_download: {
      core: {
        options: {
          domainPath: 'translations',
          url: 'https://translate.studiopress.com/global/',
          slug: 'genesis/genesis-2.5',
          textdomain: 'genesis',
          file_format: '%domainPath%/%wp_locale%.%format%'
        }
      },
    },
    makepot: {
      core: {
        options: {
          domainPath: '/languages',
          type: 'wp-plugin',
        }
      }
    },
  });


  // Load plugins
  grunt.loadNpmTasks('grunt-glotpress');
  grunt.loadNpmTasks('grunt-wp-i18n');


  // Pre-commit task.
  grunt.registerTask('i18n', 'Runs front-end dev/test tasks in preparation for a commit.',
    ['glotpress_download:core', 'makepot:core']);

}