
    $(".preview").change(function(){
        //  先获取选择的图片
        var file = this.files[0];
        // 变成字符串
        var str = getObjectUrl(file);
        // 删除以前选择过的
        $(this).prev('.img_preview').remove();
        // 在框钱放一个图片
        $(this).before("<div class='img_preview' ><img src='"+str+"' width='120' heigth='120'></div>")
    });
    // 将图片变成字符串
    function getObjectUrl(file) {
    var url = null;
    if (window.createObjectURL != undefined) {
        url = window.createObjectURL(file)
    } else if (window.URL != undefined) {
        url = window.URL.createObjectURL(file)
    } else if (window.webkitURL != undefined) {
        url = window.webkitURL.createObjectURL(file)
    }
    return url
}

