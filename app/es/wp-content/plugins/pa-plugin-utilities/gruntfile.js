/*!
 * Bootstrap's Gruntfile
 * http://getbootstrap.com
 * Copyright 2013-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 */

module.exports = function (grunt) {
  'use strict';

  // Force use of Unix newlines
  grunt.util.linefeed = '\n';

  RegExp.quote = function (string) {
    return string.replace(/[-\\^$*+?.()|[\]{}]/g, '\\$&');
  };


// Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    plugin : {name: '', version: ''},

    // Task configuration.
    clean: {
      dist: ['<%= plugin.name %>*.zip']
    },

    //TODO jshintplugin

    compress: {
      dist: {
        options: {
          archive: '<%= plugin.name %>-<%= plugin.version %>.zip'
        },
        files: [{ 
          expand: true, 
          cwd: './', 
          src: ['./*.php', './*.css', './*.png', './*.jpg', './*.jpeg', './*.gif', 
                'views/**', 'static/**', 'languages/**', 'flavours/static/**', 
                'custom_static/**', 'classes/**', 'compori/**'], 
          dest: ''
        }]
      }
    }

    //connect
    //jekyll
    //jade
    //validation
    //watch
    //sed
    //sauce labs
    //exec

 
  });

  grunt.registerTask('load-plugin-data', 'Find plugin version', function(){
    var contents = grunt.file.read('pa-plugin-utilities.php');
    var verMatch = contents.match( /^[V|v]ersion: ?(.*)$/m);
    if (verMatch && verMatch[1]){
      grunt.config('plugin.version', verMatch[1]);
    } else {
      grunt.fail.fatal('pa-plugin-utilities.php don\'t contains a version definition. Stopping execution...');
    }

    var dirMarch = __dirname.match( /([^\/]*)$/ );
    grunt.config('plugin.name', dirMarch[1]);
  });


  // These plugins provide necessary tasks.
  require('load-grunt-tasks')(grunt, {scope: 'devDependencies'});
  require('time-grunt')(grunt);

  grunt.registerTask('dist', ['load-plugin-data', 'clean', 'compress']);
  // Default task.
  grunt.registerTask('default', [ 'dist']);

};