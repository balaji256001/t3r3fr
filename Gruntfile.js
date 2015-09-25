module.exports = function(grunt) {

    // load all tasks
    require('load-grunt-tasks')(grunt, {scope: 'devDependencies'});
    var fs = require('fs');

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        less: {
            dev:{
                options:{
                },
                files:{
                    'assets/css/waboot.css': (function(){
                        if(fs.existsSync('sources/less/tmp_waboot.less')){
                            return 'sources/less/tmp_waboot.less';
                        }
                        return 'sources/less/waboot.less';
                    })(),
                    'assets/css/bootstrap-pagebuilder.css': 'sources/less/bootstrap-pagebuilder.less',
                    'assets/css/theme-options.css': 'sources/less/theme-options-gui.less',
                    'assets/css/admin.css': 'sources/less/waboot-admin.less'
                }
            },
            production:{
                options:{
                    compress: true
                },
                files: ['<%= less.dev.files %>']
            },
            waboot:{
                options:{
                    compress: true,
                    sourceMap: true,
                    sourceMapFilename: "assets/css/waboot.css.map",
                    sourceMapBasepath: "assets/css"
                },
                files: {
                    'assets/css/waboot.css': (function(){
                        if(fs.existsSync('sources/less/tmp_waboot.less')){
                            return 'sources/less/tmp_waboot.less';
                        }
                        return 'sources/less/waboot.less';
                    })()
                }                
            }
        },
        jshint : {
            all : ['sources/js/**/*.js','!sources/js/waboot.js','!sources/js/vendor/offcanvas.js']
        },
        browserify: {
            dist: {
                src: ['sources/js/main.js'],
                dest: 'sources/js/waboot.js'
            }
        },
        "jsbeautifier" : {
            files : ['sources/js/main.js','sources/js/controllers/*.js','sources/js/views/*.js'],
            options : {
            }
        },
        uglify: {
            options: {
                // the banner is inserted at the top of the output
                banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n'
            },
            dist: {
                files: {
                    'assets/js/waboot.min.js': ['sources/js/waboot.js']
                }
            }
        },
        copy:{
            all:{
                files:[
                    '<%= copy.fontawesome.files %>',
                    '<%= copy.bootstrap.files %>',
                    {
                        expand: true,
                        flatten: true,
                        cwd: "bower_components/html5shiv/dist",
                        src: "html5shiv.min.js",
                        dest: "assets/js"
                    },
                    {
                        expand: true,
                        flatten: true,
                        cwd: "bower_components/respond/dest",
                        src: "respond.min.js",
                        dest: "assets/js"
                    }
                ]
            },
            fontawesome:{
                files:[
                    {
                        expand: true,
                        flatten: true,
                        cwd: "bower_components/fontawesome",
                        src: "css/font-awesome.min.css",
                        dest: "assets/css"
                    },
                    {
                        expand: true,
                        flatten: true,
                        cwd: "bower_components/fontawesome",
                        src: "fonts/*",
                        dest: "assets/fonts"
                    }
                ]
            },
            bootstrap:{
                files:[
                    {
                        expand: true,
                        flatten: true,
                        cwd: "bower_components/bootstrap/dist",
                        src: "fonts/*",
                        dest: "assets/fonts"
                    },
                    {
                        expand: true,
                        flatten: true,
                        cwd: "bower_components/bootstrap/dist",
                        src: "js/bootstrap.min.js",
                        dest: "assets/js/"
                    },
                    {
                        expand: true,
                        flatten: true,
                        cwd: "bower_components/bootstrap/less",
                        src: ['**/*','.csscomb.json','.csslintrc'],
                        dest: "sources/bootstrap/"
                    }
                ]
            },
            dist:{
                files:[
                    {
                        expand: true,
                        cwd: "./",
                        src: [
                            "**/*",
                            "!.*",
                            "!Gruntfile.js",
                            "!package.json",
                            "!.jshintrc",
                            "!.bowerrc",
                            "!bower.json",
                            "!Movefile-sample",
                            "!Movefile",
                            "!builds/**",
                            "!node_modules/**",
                            "!components/**/node_modules/**",
                            "!bower_components/**",
                            "!components/**/bower_components/**",
                            "!assets/cache/**",
                            "!wbf/node_modules/**",
                            "!wbf/bower_components/**",
                            "!wbf/Gruntfile.js",
                            "!wbf/package.json",
                            "!wbf/vendor/**",
                            "!wbf/vendor/bootstrap/*/**",
                            "wbf/vendor/composer/*.php",
                            "wbf/vendor/composer/*.json",
                            "wbf/vendor/acf/**/*",
                            "!wbf/vendor/acf/lang/*",
                            "wbf/vendor/codemirror/lib/*",
                            "wbf/vendor/imagesloaded/*.js",
                            "wbf/vendor/jquery-modal/*.js",
                            "wbf/vendor/mgargano/simplehtmldom/src/*.*",
                            "wbf/vendor/mobiledetect/mobiledetectlib/Mobile_Detect.php",
                            "wbf/vendor/options-framework/**/*",
                            "wbf/vendor/owlcarousel/**/*",
                            "wbf/vendor/theme-updates/**/*",
                            "wbf/vendor/yahnis-elsts/**/*",
                            "wbf/vendor/autoload.php",
                            "wbf/vendor/BootstrapNavMenuWalker.php",
                            "wbf/vendor/breadcrumb-trail.php",
                            "!_bak/**"
                        ],
                        dest: "builds/waboot-<%= pkg.version %>/"
                    }
                ]
            }
        },
        pot:{
            options:{
                text_domain: 'waboot',
                dest: 'languages/',
                keywords: [
                    '__:1',
                    '_e:1',
                    '_x:1,2c',
                    'esc_html__:1',
                    'esc_html_e:1',
                    'esc_html_x:1,2c',
                    'esc_attr__:1',
                    'esc_attr_e:1',
                    'esc_attr_x:1,2c',
                    '_ex:1,2c',
                    '_n:1,2',
                    '_nx:1,2,4c',
                    '_n_noop:1,2',
                    '_nx_noop:1,2,3c'
                ]
            },
            files:{
                src: ['*.php','components/**/*.php','inc/**/*.php','templates/**/*.php'],
                expand: true
            }
        },
        compress:{
            build:{
                options:{
                    archive: "builds/waboot-<%= pkg.version %>.zip"
                },
                files:[
                    {
                        expand: true,
                        cwd: "./",
                        src: '<%= copy.dist.files.0.src %>',
                        dest: "waboot/"
                    }
                ]
            }
        },
        watch: {
            less: {
                files: 'sources/less/*.less',
                tasks: ['less:dev']
            },
            scripts:{
                files: ['<%= jshint.all %>'],
                tasks: ['jsmin']
            }
        }
    });

    // Register tasks
    grunt.registerTask('setup', ['bower-install','copy:all','less:dev']); //Setup task
    grunt.registerTask('default', ['watch']); // Default task
    grunt.registerTask('build', ['less:production','less:waboot','jsmin','pot','compress:build']); // Build task
    grunt.registerTask('js', ['browserify:dist']); // generate waboot.js
    grunt.registerTask('jsmin', ['js','uglify']); // Concat, beautify and minify js

    // Run bower install
    grunt.registerTask('bower-install', function() {
        var exec = require('child_process').exec;
        var cb = this.async();
        exec('bower install', function(err, stdout, stderr) {
            console.log(stdout);
            cb();
        });
    });
}
