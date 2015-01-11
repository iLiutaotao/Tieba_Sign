$(document).ready(function(){$('#menu_login').click(function(){switch_tabs('login');});
$('#menu_register').click(function(){switch_tabs('register');});
$('#menu_lostpass').click(function(){switch_tabs('lostpass');});});
function switch_tabs(target){$('.center-box').addClass('hidden');
$('#content-'+target).removeClass('hidden');
$('.other>a').removeClass('current');
$('#menu_'+target).addClass('current');}