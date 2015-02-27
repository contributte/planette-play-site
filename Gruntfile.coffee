
module.exports = (grunt) ->

  grunt.initConfig

    useminPrepare:
      html: ['app/FrontModule/templates/@layout.latte']
      options:
        dest: '.'

    netteBasePath:
      task:
        basePath: 'www'
        options:
          removeFromPath: ['app/FrontModule/templates/']

    less:
      development:
        options:
          compress: false
        files:
          "www/style/site/main.css": [
            "www/style/site/main.less"
          ]

    watch:
      styles:
        files: 'www/style/**/*.less'
        tasks: ['less']
        options:
          interrupt: true

    autoprefixer:
      no_dest:
        src: 'www/style/site/app.min.css'

  # These plugins provide necessary tasks.
  grunt.loadNpmTasks 'grunt-contrib-watch'
  grunt.loadNpmTasks 'grunt-contrib-less'
  grunt.loadNpmTasks 'grunt-contrib-concat'
  grunt.loadNpmTasks 'grunt-contrib-uglify'
  grunt.loadNpmTasks 'grunt-contrib-cssmin'
  grunt.loadNpmTasks 'grunt-usemin'
  grunt.loadNpmTasks 'grunt-nette-basepath'
  grunt.loadNpmTasks 'grunt-autoprefixer'

  # Default task.
  grunt.registerTask 'default', [
    'less'
    'useminPrepare'
    'netteBasePath'
    'concat'
    'uglify'
    'cssmin'
    'autoprefixer'
  ]
