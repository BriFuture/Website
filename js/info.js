function get_ip() {
  $.post('/',
  {
    ajax:"ajax",
    request:"ip"
  },
  function(data) {
    $("#iptext").attr('value',"data "+data +" .");
  }
  );
  
}

function  add() {
  $.post('/',
  {
    ajax:"ajax",
    request:"add",
    num1: $("#num1").val(),
    num2: $("#num2").val(),
  },
  function(data) {
    $("#result").html(data +" .");
  }
  );
}