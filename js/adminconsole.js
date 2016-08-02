$(document).ready(
  function() {
    //添加按钮 事件
    $("#image-add-btn").click(function() {
      // reverse_tr("image");
        // console.log($(this));
      $("tr.image").each(function(){
        // console.log($(this).attr("class"));
        $(this).attr("class", "hidden image");
      });
      $("div.div-form.image-form.hidden").each(function() {
        // console.log($(this).attr("class"));
        $(this).attr("class", "div-form image-form");
      })
    });
    $("#option-add-btn").click(function() {
      $("tr.option").each(function() {
        $(this).attr("class", "hidden option");
      });
      // $("div.div-form.option-form.hidden").each(function() {
      //   $(this).attr("class","div-form option-form");
      // });
      $("tr.add-option.hidden").each(function() {
        $(this).attr("class", "add-option");
      });
    });
    //选择好图片，修改path
    $("#upload-image").change(function() {
      console.log($(this).val());
      $("#upload_img_path").attr("value", "/users/upload/"+$(this).val());
    });

    $("#raw_sql_btn").click(function() {
      // console.log("raw_sql_btn clicked");
      // console.log($("#sql_str_textarea").val());
      $.post('/',
      {
        ajax:"ajax",
        request:"excute_raw_sql",
        sql_str: $("#sql_str_textarea").val(),
      },
      function(data) {
        // console.log("data: "+data);
        result = $.parseJSON(data);
        // console.log("data "+result.excute_result);
        $("#sql_excute_result").html(request.excute_result);
        // $("#sql_excute_result").html(request.excute_result);
      });
    });

    //按键触发
    $("button").click(function() {
      //获取 tr 的 id
      id = $(this).parent().parent().attr("id");
      // action = $(this).html();
      // console.log(id);
      if(id.indexOf('images') == 0) {
        id = id.slice(7);
        alter_image($(this), id);
      } else if(id.indexOf('options' == 0)) {
        id = id.slice(8);
        // console.log($(this));
        alter_option($(this), id);
      }
    });
});

function alter_image(obj, id) {
  action = obj.html();
  // console.log(action)
  
  if(action == "修改") {
    par = obj.parent().parent();
    img_name    = par.find("#img_name");
    path        = par.find("#img_path");
    group       = par.find("#img_group");
    description = par.find("#description");
    addons      = par.find("#addons");

    move_file = confirm("移动文件？");

    $.post('/',
    {
      ajax:"ajax",
      request:"alter_image",
      action:"modify",
      id:   id,
      img_name: img_name.val(),
      img_path: path.val(),
      img_group: group.val(),
      description: description.val(),
      addons: addons.val(),
      rename_file: move_file,
    },
    function(data) {
      // console.log("data: "+data);
      result = $.parseJSON(data);
      if(result.status) {
        // window.location.reload();
        alert("修改图片成功！");
      } else {
        alert("修改图片失败！");
      }
    });
  } else if (action == "删除") {
    del = confirm("确定删除？");
    // console.log('delete: '+del);
    if(del) {
      $.post('/',
      {
        ajax:"ajax",
        request:"alter_image",
        action:"delete",
        id:   id,
      },
      function(data) {
        // alert("data: "+data +" .");
        result = $.parseJSON(data);
        if(result.status == 1) {
          // window.location.reload();
          par = obj.parent().parent();
          par.css({"display":"none", 'visibility':"hidden"});
        } else {
          alert("删除图片失败！");
        }
      }
      );
    }
  } else if (action == "确认添加") {
    return;
    // 目前无法异步上传图片
    // par = obj.parent().parent().next();
    // // console.log(par);
    // img_name    = par.find("#img_name");
    // path        = par.find("#img_path");
    // group       = par.find("#img_group");
    // description = par.find("#description");
    // addons      = par.find("#addons");
    // $.post('/',
    // {
    //   ajax:"ajax",
    //   request: "alter_image",
    //   action: "upload",
    // })

    // $.post('/',
    // {
    //   ajax:"ajax",
    //   request:"alter_image",
    //   action:"add",
    //   img_name: img_name.val(),
    //   img_path: path.val(),
    //   img_group: group.val(),
    //   description: description.val(),
    //   addons: addons.val(),
    // },
    // function(data) {
    //   console.log("data: "+data);
    //   result = $.parseJSON(data);
    //   console.log(parseInt(result.status));
    //   if(parseInt(result.status) == 1) {
    //     // window.location.reload();
    //   } else {
    //     alert("上传图片失败！");
    //   }
    // }
    // );
  }
};

function alter_option(obj, id) {
  action = obj.html();
  // console.log("action: "+action);
  par = obj.parent().parent();
  option_name = par.find("#option_name");
  option_value = par.find("#option_value");
  option_autoload = par.find("#option_autoload");
  switch(action) {
    case '修改':
      // console.log("action modify: "+ option_name.val() + " " + option_value.val() + " " + option_autoload.val());
      $.post('/',
      {
        ajax:"ajax",
        request:"alter_option",
        action:"modify",
        id: id,
        option_name: option_name.val(),
        option_value: option_value.val(),
        option_autoload: option_autoload.val()
      },
      function(data) {
        console.log("data mod: "+data);
        result = $.parseJSON(data);
        if(result.status == 0) {
          alert("修改 option 失败！");
        }
      });
      break;
    case '删除':
      // console.log("action modify: "+ option_name.val() + " " + option_value.val() + " " + option_autoload.val());
      $.post('/',
      {
        ajax:"ajax",
        request:"alter_option",
        action:"delete",
        id: id,
        option_name: option_name.val(),
        // option_value: option_value.val(),
        // option_autoload: option_autoload.val()
      },
      function(data) {
        console.log("data del: "+data);
        result = $.parseJSON(data);
        if(result.status == 1) {
          par = obj.parent().parent();
          par.css({"display":"none", 'visibility':"hidden"});
        } else {
          alert("删除 option 失败！");
        }
      });
      break;
    case '确认添加':
      console.log("action modify: "+ option_name.val() + " " + option_value.val() + " " + option_autoload.val());
      $.post('/',
      {
        ajax:"ajax",
        request:"alter_option",
        action:"add",
        // id: id,
        option_name: option_name.val(),
        option_value: option_value.val(),
        option_autoload: option_autoload.val()
      },
      function(data) {
        console.log("data add: "+data);
        result = $.parseJSON(data);
        if(result.status == 1) {
          window.location.reload();
        } else {
          alert("添加 option 失败！");
        }
      });
      break;  
  }
}


/*function reverse_tr(cl) {
  $(".tr-show"+"."+cl).each(function(){
    // console.log($(this));
    $(this).attr("class", "tr-show-mid"+" "+cl);
  });
  $(".tr-hidden"+"."+cl).each(function(){
    $(this).attr("class", "tr-show"+" "+cl);
  });
  $(".tr-show-mid"+"."+cl).each(function(){
    // console.log($(this));
    $(this).attr("class", "tr-hidden"+" "+cl);
  });
}*/