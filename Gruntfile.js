module.exports = function (grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify: {
            all: {
                files: [{
                    expand: true,
                    cwd: 'inc/tinymce/', // 'js/',
                    src: ['*.dev.js'],
                    dest: 'inc/tinymce/', // 'js/',
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
        },
        babel: {
            options: {
                sourceMap: true
            },
            dist: {
                files: [{
                    'expand': true,
                    'cwd': 'js/',
                    'src': ['**/add-quicktag-gutenberg.dev.js'],
                    'dest': 'js/',
                    'ext': ['.js']
                }]
            }
        }
    });

    // Load the plugin that provides the "uglify" task.
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-babel');

    // Default task(s).
    grunt.registerTask('default', ['uglify', 'cssmin', 'babel']);

};
