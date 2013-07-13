module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    dest:{
      publish:'pub',
      build:'build'
    },
    vendor: {
      js: [
      'vendor/jquery/jquery.min.js',   
      'vendor/angular/angular.min.js',
      'vendor/angular-bootstrap/ui-bootstrap-tpls.min.js',
      'vendor/angular-cookies/angular-cookies.min.js',
      'vendor/angular-resource/angular-resource.min.js',
      'vendor/restangular/dist/restangular.min.js',
      'vendor/underscore/underscore-min.js'
      ]
    },

    clean:{
      build:[ '<%= dest.build %>'],
      publish:[ '<%= dest.publish %>' ]
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
        src: ['module.prefix','<%= dest.build %>/js/*.js','module.suffix'],        
        dest: '<%= dest.build %>/bin/<%= pkg.name %>.js'
      },      
      libs:{
       src: ['<%= vendor.js %>'],
       dest: '<%= dest.build %>/bin/libs.js'
     }
   },
   ngmin: {
    dist: {
      files: [{
        expand: true,
        cwd: 'src/scripts/',
        src: '*.js',
        dest: '<%= dest.build %>/js'
      }]
    }
  },
  uglify: {
    options: {
      banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n'
    },
    dist: {
      files: {
        '<%= dest.publish %>/js/<%= pkg.name %>.min.js': ['<%= concat.dist.dest %>'],
        '<%= dest.publish %>/js/libs.min.js': ['<%= concat.libs.dest %>']
      }
    }
  },
  qunit: {
    files: ['test/**/*.html']
  },
  jshint: {
    files: ['gruntfile.js', 'src/**/*.js', 'test/**/*.js'],
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
      files: ['<%= jshint.files %>'],
      tasks: ['jshint', 'qunit']
    },
    copy: {
      build: {
        files: [
        {expand: true, cwd:'src/images/', src: ['**'], dest: '<%= dest.build %>/img/'},
        {expand: true, cwd:'src/views/', src: ['**'], dest: '<%= dest.build %>/views/'},
        {expand: true, cwd:'src',src: ['index.html'], dest: '<%= dest.build %>/'},
        {expand: true, flatten:true ,src: '<%= vendor.js %>', dest: '<%= dest.build %>/libs',filter: 'isFile'}
        ]
      },
      publish: {
        files: [
        {expand: true, cwd:'<%= dest.build %>/', src: ['css/**','img/*','views/**'], dest: '<%= dest.publish %>/'},
        {expand: true, cwd:'<%= dest.build %>/',src: ['index.html'], dest: '<%= dest.publish %>/'}
        ]
      }
    },
    compass: {
      dist: {
        options: {
          sassDir: 'src/sass',
          cssDir: '<%= dest.build %>/css',
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



};