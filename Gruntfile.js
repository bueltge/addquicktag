module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		uglify: {
			all: {
				files: [{
					expand: true,
					cwd: ['js/', 'inc/tinymce/'],
					src: ['*.dev.js', '!*.js'],
					dest: ['js/', 'inc/tinymce/'],
					ext: ['.js', '!*.dev.js']
				}]
			}
		},
		cssmin: {
			all: {
				files: [{
					expand: true,
					cwd: 'css/',
					src: ['*.dev.css', '!*.css'],
					dest: 'css/',
					ext: ['.css', '*.dev.css']
				}]
			}
		}
	});

	// Load the plugin that provides the "uglify" task.
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');

	// Default task(s).
	grunt.registerTask('default', ['uglify', 'cssmin']);

};