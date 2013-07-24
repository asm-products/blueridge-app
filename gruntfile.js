module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    dir:{
      publish:'pub',
      build:'build',
      src:'app/src',
      api:'api',
      vendor:'app/vendor'
    },
    vendor: {
      js: [
      '<%= dir.vendor %>/jquery/jquery.min.js',
      '<%= dir.vendor %>/angular-all-unstable/angular.min.js',
      '<%= dir.vendor %>/angular-all-unstable/angular-cookies.min.js',
      '<%= dir.vendor %>/angular-bootstrap/ui-bootstrap-tpls.min.js',
      '<%= dir.vendor %>/restangular/dist/restangular.min.js',
      '<%= dir.vendor %>/js-base64/base64.min.js',
      '<%= dir.vendor %>/underscore/underscore-min.js',
      ],
      nomin:[
      '<%= dir.vendor %>/angular-jquery.payment/lib/jquery.payment.js',
      '<%= dir.vendor %>/angular-jquery.payment/lib/angular-jquery.payment.js',
      '<%= dir.vendor %>/angular-google-analytics/src/angular-google-analytics.js',
      ]
    },

    clean:{
      build:[ 
      '<%= dir.build %>/css',
      '<%= dir.build %>/fonts',
      '<%= dir.build %>/img',
      '<%= dir.build %>/bin',
      '<%= dir.build %>/js',
      '<%= dir.build %>/libs',
      '<%= dir.build %>/views',
      '<%= dir.build %>/*.html',
      '<%= dir.build %>/*.php'
      ],
      publish:[ 
      '<%= dir.publish %>/css',
      '<%= dir.publish %>/fonts',
      '<%= dir.publish %>/js',
      '<%= dir.publish %>/img',
      '<%= dir.publish %>/views',
      '<%= dir.publish %>/*.html',
      '<%= dir.publish %>/*.php'
      ]
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
        src: ['module.prefix','<%= dir.build %>/js/**/*.js','module.suffix'],        
        dest: '<%= dir.build %>/bin/<%= pkg.name %>.js'
      },      
      libs:{
       src: ['<%= vendor.js %>'],
       dest: '<%= dir.build %>/bin/libs.js'
     },
     libs_nomin:{
       src: ['<%= vendor.nomin %>'],
       dest: '<%= dir.build %>/bin/libs.nomin.js'
     }
   },
   ngmin: {
    dist: {
      files: [{
        expand: true,
        cwd: '<%= dir.src %>/scripts',
        src: '**/*.js',
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
        '<%= dir.publish %>/js/libs.min.js': ['<%= concat.libs.dest %>','<%= concat.libs_nomin.dest %>']
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
      css: {
        files: ['<%= dir.src %>/sass/**/*.scss'],
        tasks: ['compass','copy:publish'],
      },
      img: {
        files: ['<%= dir.src %>/img/**/*'],
        tasks: ['copy'],
      },
      fonts: {
        files: ['<%= dir.src %>/fonts/*','<%= dir.src %>/sass/**/{*.eot,*.svg,*ttf,*woff,*.otf}'],
        tasks: ['copy'],
      },
      html: {
        files: ['<%= dir.src %>/**/*.html'],
        tasks: ['copy'],
      },
      scripts: {
        files: ['<%= dir.src %>/scripts/**/.js'],
        tasks: ['ngmin','concat','uglify'],
      },
    },
    copy: {
      build: {
        files: [
        {expand: true, cwd:'<%= dir.src %>', src: ['img/**','views/**','fonts/**'], dest: '<%= dir.build %>'},
        {expand: true, flatten:true,src: ['<%= dir.src %>/sass/**/{*.eot,*.svg,*ttf,*woff,*.otf}'], dest: '<%= dir.build %>/fonts',filter: 'isFile'},
        {expand: true, cwd:'<%= dir.src %>',src: ['*.html'], dest: '<%= dir.build %>/'},
        {expand: true, flatten:true ,src: '<%= vendor.js %>', dest: '<%= dir.build %>/libs',filter: 'isFile'},
        {expand: true, flatten:true ,src: '<%= vendor.nomin %>', dest: '<%= dir.build %>/libs',filter: 'isFile'},
        {expand: true, cwd:'<%= dir.api %>',src: ['api.php'], dest: '<%= dir.build %>/'},
        ]
      },
      publish: {
        files: [
        {expand: true, cwd:'<%= dir.build %>/', src: ['img/**','views/**','fonts/**'], dest: '<%= dir.publish %>/'},
        {expand: true, flatten:true ,src: '<%= dir.build %>/css/**/*.css', dest: '<%= dir.publish %>/css',filter: 'isFile'},
        {expand: true, cwd:'<%= dir.build %>/',src: ['*.html','*.php'], dest: '<%= dir.publish %>/'},
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
          sassDir: '<%= dir.src %>/sass/lib',
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
grunt.registerTask('build', ['jshint','clean','compass','copy:build','ngmin','copy:publish','concat','uglify']);

};