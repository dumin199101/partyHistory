/**
 * Created by admin on 2017/8/22.
 */
function searchText(id){
    switch (id){
        case 'searchInBook':
            $(".searchInBookBox").css("display","inline-block");
            $("#searchInBook").addClass('active');
            $("#catalog").removeClass('active');
            searchInBook(1);
            break;
        case 'catalog':
            $(".searchInBookBox").hide();
            $(".infoBySearchInBookBox").hide();
            $('.bibliography').show();
            $("#searchInBook").removeClass('active');
            $("#catalog").addClass('active');
            catalogList(1);
            middleBoxHeight();
            break
    }
}
function addField() {
    var num=$(".searchField").length;
    if(num<6){
        var id=num+1;
        var html='<div class="searchField" id="searchField_'+id+'"><select name="fieldRelation_'+id+'" class="fieldRelation"> <option value="1" >并且</option> <option value="2" >或者</option> <option value="3" >异或</option> </select><select name="searchField_'+id+'"><option value="1">标题</option> <option value="2" >正文</option> <option value="3" >作者</option> </select> <input name="searchFieldInput_'+id+'" value=""> <select name="wordRelation_'+id+'"> <option value="1" >并含</option> <option value="2" >或者</option> <option value="3" >不含</option> </select> <input name="attachFieldInput_'+id+'" value=""> <select name="isAccurate_'+id+'"> <option value="1" >精确</option> <option value="2" >模糊</option> </select> </div>';
        $(".timeSectionBox").before(html);
    }

}
function removeField() {
    var num=$(".searchField").length;
    if(num>1){
        var lastField=$(".searchField").last();
        lastField.last().remove();
    }

}

function inputCheck() {
    pattern = new RegExp("':;\"<>]")
}

function advanceSearchShow() {
    var advancedSearchBox= $('.advancedSearchBox');
    if(advancedSearchBox.css('display')==='none'){
        advancedSearchBox.css('display','block');
        $('.advancedSearch').find("img").attr("src","/Public/image/index/down.png");
    }else{
        advancedSearchBox.css('display','none');
        $('.advancedSearch').find("img").attr("src","/Public/image/index/uplist.png");
    }
}
var start = {
    elem: '#start',
    format: 'YYYY/MM/DD',
//                            min: laydate.now(), //设定最小日期为当前日期
    max: '2099-06-16', //最大日期
    min: '1800-01-01', //最大日期
    istime: true,
    istoday: false,
    choose: function(datas){
        end.min = datas; //开始日选好后，重置结束日的最小日期
        end.start = datas //将结束日的初始值设定为开始日
    }
};
var end = {
    elem: '#end',
    format: 'YYYY/MM/DD',
//                            min: laydate.now(),
    max: '2099-06-16',
    istime: true,
    istoday: false,
    choose: function(datas){
        start.max = datas; //结束日选好后，重置开始日的最大日期
    }
};
laydate(start);
laydate(end);