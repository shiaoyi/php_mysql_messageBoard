const { src, dest, parallel } = require('gulp');
const sass = require('gulp-sass');
const cleanCSS = require('gulp-clean-css');
const uglify = require('gulp-uglify');
const babel = require('gulp-babel');
const rename = require("gulp-rename");

function css() {
    return src('./*.sass')
        .pipe(sass().on('error',sass.logError))
        .pipe(cleanCSS())
        .pipe(dest('./build'));
}

function js(){
    return src('./*.js')
        .pipe(babel())
        .pipe(uglify())
        .pipe(rename({ extname: '.min.js' }))
        .pipe(dest('./build'));
}

exports.css = css;
exports.js = js;
exports.default = parallel(css,js);