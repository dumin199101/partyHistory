/**
 * Created by admin on 2017/4/11.
 */
function ajaxDealData(url,data,response){
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'json',
        beforeSend: loading,//执行loading
        success: response,
        error:function (){
            alert('error');
        }
    });
}

function middleBoxHeight() {
    //直接去掉style
    $(".bookMeddleRight").removeAttr("style");
    $(".middleLeftBox").removeAttr("style");
    var left=$(".middleLeftBox").height();
    var right=$(".bookMeddleRight").height();
    if(left>=right){
        $(".bookMeddleRight").height(left-2);
    }else{
        $(".middleLeftBox").height(right+2+60);
    }
}

function display_laypage(data,curr,funcName){
    $("#page").show();
    //显示分页
    laypage({
        cont: 'page', //容器。值支持id名、原生dom对象，jquery对象。【如该容器为】：<div id="page1"></div>
        pages: Math.ceil(data.count.total_count/data.pageSize), //通过后台拿到的总页数
        curr: curr || 1, //当前页
        groups:10,
        //skip:true,
        jump: function(obj, first){ //触发分页后的回调
            if(!first){ //点击跳页触发函数自身，并传递当前页：obj.curr
                switch(funcName){
                    case 'catalogList':
                        catalogList(obj.curr);
                        break;
                    case 'searchInBook':
                        searchInBook(obj.curr);
                        break;
                    case 'bookListPage':
                        bookListPage(obj.curr);
                        break;
                    case 'bookListByCatPage':
                        bookListPage(obj.curr,catId);
                        break;
                    case 'bookListByLeaderPage':
                        bookListPage(obj.curr,'',leaderId);
                        break;
                }
            }
            myScroll();
        }
    });
}

function myScroll() {
    $(window).scrollTop(0);
}
function loading() {
    $('#table_box').append('<img id="loading" src="/Public/image/loading.gif"  />');
}


