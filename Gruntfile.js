module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        copy: {
            images: {
                files: [
                    {
                        expand: true,
                        cwd: 'assets',
                        src: 'images/*',
                        dest: 'public/build'
                    }
                ]
            }
        },
        postcss: {
            options: {
                processors: [
                    require('autoprefixer')()
                ]
            },
            dist: {
                src: 'public/build/css/*.css'
            }
        },
        uglify: {
            app: {
                files: {
                    'public/build/js/app.js': [
                        'node_modules/handlebars/dist/handlebars.runtime.js',
                        'assets/temp/handlebars-precompiled.js',
                        'assets/js/app.js'
                    ]
                }
            }
        },
        sass: {
            main: {
                files: {
                    'public/build/css/app.css': 'assets/css/app.scss'
                }
            }
        },
        shell: {
            handlebarsPrecompile: {
                command: [
                    'mkdir -p assets/temp',
                    './node_modules/.bin/handlebars templates/handlebars/* -f assets/temp/handlebars-precompiled.js'
                ].join(' && ')
            }
        },
        watch: {
            handlebars: {
                files: ['templates/handlebars/*.html', 'templates/handlebars/*/*.html'],
                tasks: ['build-js']
            },
            js: {
                files: ['assets/js/*.js', 'assets/js/*/*.js'],
                tasks: ['build-js']
            },
            scss: {
                files: ['assets/css/*.scss', 'assets/css/*/*.scss'],
                tasks: ['build']
            }
        }
    });
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-postcss');
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-shell');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    grunt.registerTask('build-css', ['sass', 'postcss']);
    grunt.registerTask('build-js', ['shell:handlebarsPrecompile', 'uglify']);
    grunt.registerTask('build-static-assets', ['copy']);

    grunt.registerTask('build', ['build-css', 'build-js', 'build-static-assets']);
    grunt.registerTask('default', ['build', 'watch']);
};