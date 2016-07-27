$(document).ready(
  function() {
    // h = $(window.frames["blog-iframe"].document).height();
    h= $("#blog-iframe").contents().find("body").height();
    h+=50;
    if(h < 800) {
      h = 1200;
    }
    $("#test").text(h);
    $("#blog-iframe").height(h);
  });