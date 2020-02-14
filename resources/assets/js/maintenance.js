import $ from 'jquery';

window.jQuery = $;
window.jquery = $;
window.$ = $;

require('./maintenance/TweenLite.min');
require('./maintenance/EasePack.min');
require('./maintenance/constellation');
require('./maintenance/jquery.backstretch.min');
require('./maintenance/jquery.youtubebackground');

if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
  $('#video').css({
    opacity: '0.0',
  });
}

$(window).on('load', () => {
  $('.loader-icon').delay(500).fadeOut();
  $('#page-loader').delay(700).fadeOut('slow');
  setTimeout(() => {
    $('header .social-icons').addClass('animated fadeInDown');
    $('header .logo').addClass('animated fadeInDown');
    $('header .typed').addClass('animated fadeInUp');
    $('header p').addClass('animated fadeInUp');
    $('header .countdown').addClass('animated fadeInUp');
  });
});

// $('header').backstretch('images/landing-users.png');

const video = $('#video').data('video');
const mute = $('#video').data('mute');

$('#video').YTPlayer({
  videoId: video,
  mute,
  fitToBackground: true,
});
