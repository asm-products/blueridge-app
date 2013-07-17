module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    dir:{
      publish:'pub',
      build:'app/build',
      src:'app/src',
      api:'api',
      vendor:'app/vendor'
    },
    vendor: {
      js: [
      '<%= dir.vendor %>/jquery/jquery.min.js',   
      '<%= dir.vendor %>/angular.min/index.js',
      '<%= dir.vendor %>/angular-bootstrap/ui-bootstrap-tpls.min.js',
      '<%= dir.vendor %>/angular-cookies/angular-cookies.min.js',
      '<%= dir.vendor %>/angular-resource/angular-resource.min.js',
      '<%= dir.vendor %>/restangular/dist/restangular.min.js',
      '<%= dir.vendor %>/underscore/underscore-min.js'
      ]
    },

    clean:{
      build:[ '<%= dir.build %>'],
      publish:[ '<%= dir.publish %>' ]
    },
    concat: {
      options: {
        separator: ';',
        stripBanners:{
          options:{
            block:true,
            line:true
          }
        },
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n'
      },
      dist: {
        src: ['module.prefix','<%= dir.build %>/js/*.js','module.suffix'],        
        dest: '<%= dir.build %>/bin/<%= pkg.name %>.js'
      },      
      libs:{
       src: ['<%= vendor.js %>'],
       dest: '<%= dir.build %>/bin/libs.js'
     }
   },
   ngmin: {
    dist: {
      files: [{
        expand: true,
        cwd: '<%= dir.src %>/scripts/',
        src: '*.js',
        dest: '<%= dir.build %>/js'
      }]
    }
  },
  uglify: {
    options: {
      banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n'
    },
    dist: {
      files: {
        '<%= dir.publish %>/js/<%= pkg.name %>.min.js': ['<%= concat.dist.dest %>'],
        '<%= dir.publish %>/js/libs.min.js': ['<%= concat.libs.dest %>']
      }
    }
  },
  qunit: {
    files: ['test/**/*.html']
  },
  jshint: {
    files: ['gruntfile.js', '<%= dir.src %>/**/*.js', 'test/**/*.js'],
    options: {
        // options here to override JSHint defaults
        globals: {
          jQuery: true,
          console: true,
          module: true,
          document: true
        }
      }
    },
    watch: {
      options: {
        livereload: true,
      },
      dist: {
        files: ['<%= dir.src %>/**/*.*'],
        tasks: ['publish'],
      },
    },
    copy: {
      build: {
        files: [
        {expand: true, cwd:'<%= dir.src %>', src: ['img/**','views/**'], dest: '<%= dir.build %>'},
        {expand: true, cwd:'<%= dir.src %>',src: ['index.html'], dest: '<%= dir.build %>/'},
        {expand: true, flatten:true ,src: '<%= vendor.js %>', dest: '<%= dir.build %>/libs',filter: 'isFile'},
        {expand: true, cwd:'<%= dir.api %>',src: ['api.php'], dest: '<%= dir.build %>/'},
        ]
      },
      publish: {
        files: [
        {expand: true, cwd:'<%= dir.build %>/', src: ['css/**','img/*','views/**'], dest: '<%= dir.publish %>/'},
        {expand: true, cwd:'<%= dir.build %>/',src: ['index.html'], dest: '<%= dir.publish %>/'},
        {expand: true, cwd:'<%= dir.api %>',src: ['api.php'], dest: '<%= dir.publish %>/'},
        ]
      }
    },
    compass: {
      dist: {
        options: {
          sassDir: '<%= dir.src %>/sass',
          cssDir: '<%= dir.build %>/css',
          environment: 'production',
          raw: "preferred_syntax = :scss\n"
        }
      },
      libs: {
        options: {
          sassDir: '<%= dir.vendor %>/bootstrap-sass/lib',
          cssDir: '<%= dir.build %>/css',
          environment: 'production',
          raw: "preferred_syntax = :scss\n"
        }
      }
    },
  });

grunt.loadNpmTasks('grunt-contrib-clean');
grunt.loadNpmTasks('grunt-ngmin');
grunt.loadNpmTasks('grunt-contrib-copy');
grunt.loadNpmTasks('grunt-contrib-uglify');
grunt.loadNpmTasks('grunt-contrib-jshint');
grunt.loadNpmTasks('grunt-contrib-qunit');
grunt.loadNpmTasks('grunt-contrib-watch');
grunt.loadNpmTasks('grunt-contrib-concat');
grunt.loadNpmTasks('grunt-contrib-compass');

grunt.registerTask('test', ['jshint', 'qunit']);
grunt.registerTask('default', ['build']);
grunt.registerTask('build', ['jshint','clean','compass','copy:build','ngmin']);
grunt.registerTask('publish', ['build','copy:publish','concat','uglify']);
grunt.registerTask('develop', ['publish','watch']);



};