module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    dir:{
      publish:'pub',
      build:'app/build',
      src:'app/resources',
      bower:'app/bower_modules',
      node:'node_modules'
    },
    vendor: {
      js: [
      '<%= dir.bower %>/jquery/jquery.min.js',
      '<%= dir.bower %>/jquery/jquery-migrate.js',
      '<%= dir.bower %>/mixitup/src/jquery.mixitup.js',
      '<%= dir.bower %>/bootstrap-sass/js/bootstrap-modal.js',
      '<%= dir.bower %>/bootstrap-sass/js/bootstrap-alert.js',
      '<%= dir.bower %>/bootstrap-sass/js/bootstrap-dropdown.js',
      '<%= dir.node %>/twig/twig.min.js',
      '<%= dir.node %>/modernizr/modernizr.js',
      '<%= dir.bower %>/underscore/underscore-min.js',
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
      '<%= dir.publish %>/views'
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
      libs:{
       src: ['<%= vendor.js %>'],
       dest: '<%= dir.build %>/bin/libs.js'
     }
   },
   uglify: {
    options: {
      banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n'
    },
    libs: {
      files: {
        '<%= dir.publish %>/js/libs.min.js': ['<%= concat.libs.dest %>']
      }
    },
    dist: {
      options:{
        mangle:true
      },
      files: [{
        expand: true,
        src: '**/*.js',
        dest: '<%= dir.publish %>/js',
        cwd: '<%= dir.src %>/js'
      }]
    }
  },
  twig: {
    options: {
      variable:"window.Blueridge",
      amd_wrapper:false,
      template_key: function(filename) {
        return filename.split('/').pop();
      }
    },
    publish: {
      files:{
        "<%= dir.publish %>/js/tmpl.js": ["<%= dir.src %>/templates/**/*.html"]
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
        tasks: ['copy','twig'],
      },
      scripts: {
        files: ['<%= dir.src %>/js/**/*.js'],
        tasks: ['concat','uglify','twig'],
      },
    },
    copy: {
      build: {
        files: [
        {expand: true, cwd:'<%= dir.src %>', src: ['img/**','fonts/**'], dest: '<%= dir.build %>'},
        {expand: true, flatten:true,src: ['<%= dir.src %>/sass/**/{*.eot,*.svg,*ttf,*woff,*.otf}'], dest: '<%= dir.build %>/fonts',filter: 'isFile'},
        {expand: true, flatten:true ,src: '<%= vendor.js %>', dest: '<%= dir.build %>/libs',filter: 'isFile'}
        ]
      },
      publish: {
        files: [
        {expand: true, cwd:'<%= dir.build %>/', src: ['img/**','fonts/**','js/**'], dest: '<%= dir.publish %>/'},
        {expand: true, flatten:true ,src: '<%= dir.build %>/css/**/*.css', dest: '<%= dir.publish %>/css',filter: 'isFile'},
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
grunt.loadNpmTasks('grunt-contrib-copy');
grunt.loadNpmTasks('grunt-contrib-uglify');
grunt.loadNpmTasks('grunt-contrib-jshint');
grunt.loadNpmTasks('grunt-contrib-qunit');
grunt.loadNpmTasks('grunt-contrib-watch');
grunt.loadNpmTasks('grunt-contrib-concat');
grunt.loadNpmTasks('grunt-contrib-compass');
grunt.loadNpmTasks('grunt-twig');

grunt.registerTask('test', ['jshint', 'qunit']);
grunt.registerTask('default', ['build']);
grunt.registerTask('build', ['jshint','clean','compass','copy:build','copy:publish','concat','uglify','twig']);

};