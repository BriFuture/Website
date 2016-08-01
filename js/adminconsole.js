$(document).ready(
  function() {
    //添加按钮 事件
    $("#image-add-btn").click(function() {
      // reverse_tr("image");
      $("tr.tr-show.image").each(function(){
        // console.log($(this).attr("class"));
        $(this).attr("class", "tr-hidden image");
      });
      $("form.image-form-hidden").each(function() {
        $(this).attr("class", "image-form-show form-inline");
      })
    });
    //确认添加
    $("#image-add-confirm-btn").click(function() {
      // reverse_tr("image");
      // $("tr.tr-hidden.image").each(function(){
      //   // console.log($(this).attr("class"));
      //   $(this).attr("class", "tr-show image");
      // });
      // $("form.image-show").each(function() {
      //   console.log($(this));
      //   $($(this).attr("class", "form-inline image-form-hidden"));
      // });
      // alter_image($(this), 0);
    });
    //选择好图片，修改path
    $("#upload-image").change(function() {
      console.log($(this).val());
      $("#upload_img_path").attr("value", "/users/upload/"+$(this).val());
    });

    $("button").click(function() {
      //获取 tr 的 id
      id = $(this).parent().parent().attr("id");
      // action = $(this).html();
      // console.log(id);
      if(id.indexOf('images') == 0) {
        id = id.slice(7);
        alter_image($(this), id);
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
    },
    function(data) {
      console.log("data: "+data);
      result = $.parseJSON(data);
      if(result.status == 1) {
        // window.location.reload();
        alert("修改图片成功！");
      } else {
        alert("修改图片失败！");
      }
    }
    );
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