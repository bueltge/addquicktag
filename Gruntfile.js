module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		removelogging: {
			dist: {
				src: ['js/editor_plugin.dev.js'],
				dest: 'js/editor_plugin.log.js',

				options: {
				}
			}
		},
		uglify: {
			all: {
				files: [{
					expand: true,
					cwd: 'js/',
					src: ['*.dev.js'],
					dest: 'js/',
					ext: ['.js']
				}]
			}
		},
		cssmin: {
			all: {
				files: [{
					expand: true,
					cwd: 'css/',
					src: ['*.dev.css'],
					dest: 'css/',
					ext: ['.css']
				}]
			}
		}
	});

	grunt.loadNpmTasks("grunt-remove-logging");
	// Load the plugin that provides the "uglify" task.
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');

	// Default task(s).
	grunt.registerTask('default', ['removelogging', 'uglify', 'cssmin']);

};
