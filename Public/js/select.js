

            function beginSelect() {
                datestart('begin_date_y', 'begin_date_m', 'begin_date_d');
            }

            function expertBeginSelect() {
                datestart('expert_begin_date_y', 'expert_begin_date_m', 'expert_begin_date_d');
            }

            function finishSelect() {
                datestart('finish_date_y', 'finish_date_m', 'finish_date_d');
            }

            function expertFinishSelect() {
                datestart('expert_finish_date_y', 'expert_finish_date_m', 'expert_finish_date_d');
            }

            function datestart(date_y, date_m, date_d) {
                MonHead = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
                //赋予年下拉框年份
                y = 1930;
                var beginSelectY = document.getElementById(date_y);
                for (var i = y; i <= y + 30; i++) {
                    beginSelectY.options.add(new Option(i, i));
                }
                if (beginSelectY.value == '') {
                    beginSelectY.value = 1;
                }


                //赋予月下拉框内容
                var beginSelectM = document.getElementById(date_m);
                for (var j = 1; j <= 12; j++) {
                    if(j<=9){
                        beginSelectM.options.add(new Option('0'+j, '0'+j));
                    }else{
                        beginSelectM.options.add(new Option(j, j));
                    }

                }
                if (beginSelect.value == '') {
                    beginSelectM.value = 1;
                }
                var n = MonHead[beginSelectM.value-1];
                writeDay(n, date_d);

            }
            //修改年份
            function changeYear(str, id) {
                var date_m = id.replace(/_y/, '_m');
                beginSelectMValue = document.getElementById(date_m).value;
                var n = MonHead[beginSelectMValue - 1];
                if (isRunYear(str) && beginSelectMValue == 2) {
                    n++;
                }
                var date_d=id.replace(/_y/, '_d')
                writeDay(n,date_d);
            }

            //修改月份
            function changeMon(str, id) {
                var date_y = id.replace(/_m/, '_y');
                beginSelectYValue = document.getElementById(date_y).value;
                var n = MonHead[str - 1];
                if (str == 2 && isRunYear(beginSelectYValue)) {
                    n++;
                }
                var date_d=id.replace(/_m/, '_d');
                writeDay(n,date_d);
            }


            //根据条件写日期
            function writeDay(n,date_d) {
                var beginSelectD = document.getElementById(date_d);
                optionsClear(beginSelectD);
                for (var k = 1; k <= n; k++) {
                    if(k<=9){
                        beginSelectD.options.add(new Option('0'+k,'0'+k));
                    }else{
                        beginSelectD.options.add(new Option(k, k));
                    }

                }
                if(beginSelectD.value==''){
                    beginSelectD.value = '0'+1;
                }

            }
            //判断是闰年还是平年
            function isRunYear(year) {
                if (0 == year % 4 || (0 !== year % 100 && 0 == year % 400)) {
                    return true;
                } else {
                    return false;
                }
            }

            function optionsClear(e) {
                e.options.length = 1;
            }

            if (document.attachEvent) {
                window.attachEvent("onload", beginSelect);
                window.attachEvent("onload", expertBeginSelect);
                window.attachEvent("onload", finishSelect);
                window.attachEvent("onload", expertFinishSelect);
            } else {
                window.addEventListener('load', beginSelect, false);
                window.addEventListener('load', expertBeginSelect, false);
                window.addEventListener('load', finishSelect, false);
                window.addEventListener('load', expertFinishSelect, false);
            }


            function category_search() {
                choose_search('category');
            }
            function catalog_search() {
                choose_search('catalog');
            }
            function expert_search() {
                choose_search('expert');
            }
            function choose_search(str) {
                $("#"+str+'_form').parents().find("form").hide();
                $("#"+str+'_form').show();
                $('#'+str+'_li').parents().find('li').removeClass('active');
                $('#'+str+'_li').addClass('active');
            }
            

            function category_check(str){
                var beginYear=$('#'+str+'begin_date_y');
                var beginMonth=$('#'+str+'begin_date_m');
                var beginDay=$('#'+str+'begin_date_d');
                var finishYear=$('#'+str+'finish_date_y');
                var finishMonth=$('#'+str+'finish_date_m');
                var finishDay=$('#'+str+'finish_date_d');
                var beginTime= beginYear.val()+'-'+beginMonth.val()+'-'+beginDay.val();
                var finishTime= finishYear.val()+'-'+finishMonth.val()+'-'+finishDay.val();
                if(finishTime>=beginTime){
                    $('#cue_word').html('');
                    finishYear.css('border-color','#716b6b');
                    finishMonth.css('border-color','#716b6b');
                    finishDay.css('border-color','#716b6b');
                    $("input[name='beginTime']").val(beginTime);
                    $("input[name='finishTime']").val(finishTime);

                }else{
                    finishYear.css('border-color','yellow');
                    finishMonth.css('border-color','yellow');
                    finishDay.css('border-color','yellow');
                    $('#'+str+'cue_word').html('截止时间小于开始时间！！！！');
                    return false;
                }
            }
            
            function expert_check() {
                var listNum=$("#noBracketsList").find('li').length;
                $("input[name='noBracketsListNum']").val(listNum);
                var expertName=new Array();
                for(var i=1;i<=listNum;i++){
                    var expert_name=$("input[name=expert_name_"+i+"]").val();
                    if(expert_name==""){
                        alert('检索词不能为空');
                        return false;
                    }
                    var expert_select=$("#expert_select_"+i).val();
                }
                if(category_check('expert_')==false){
                    return false;
                }
            }

